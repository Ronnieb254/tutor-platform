<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\BookingService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function __construct(
        protected PaystackService $paystack,
        protected BookingService  $booking
    ) {}

    public function handle(Request $request)
    {
        $payload   = $request->getContent();
        $signature = $request->header('X-Paystack-Signature', '');

        if (!$this->paystack->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Paystack webhook: invalid signature');
            return response()->json(['status' => 'invalid signature'], 401);
        }

        $event = json_decode($payload, true);
        Log::info('Paystack webhook event: ' . ($event['event'] ?? 'unknown'));

        if (($event['event'] ?? '') === 'charge.success') {
            $this->handleChargeSuccess($event['data']);
        }

        return response()->json(['status' => 'ok']);
    }

    private function handleChargeSuccess(array $data): void
    {
        $reference = $data['reference'] ?? null;
        if (!$reference) return;

        $payment = Payment::where('paystack_reference', $reference)->first();
        if (!$payment) {
            Log::warning('Webhook: payment not found for reference ' . $reference);
            return;
        }

        // Idempotent: already handled
        if ($payment->status === 'success') {
            Log::info('Webhook: payment already confirmed, skipping', ['reference' => $reference]);
            return;
        }

        $payment->update([
            'status'  => 'success',
            'paid_at' => now(),
        ]);

        $type = $payment->payment_type ?? null;

        // ── 1-on-1 booking ────────────────────────────────────────────────────
        if ($type === null || $type === 'booking' || $payment->booking_id) {
            $booking = $payment->booking;
            if ($booking && $booking->status === 'pending') {
                $this->booking->confirmBooking($booking);
                Log::info('Webhook: 1-on-1 booking confirmed', ['booking_id' => $booking->id]);
            }
            return;
        }

        // ── Clinic / Group session ─────────────────────────────────────────────
        if (in_array($type, ['clinic', 'group_session'])) {
            $clinicId = $payment->clinic_id;
            if (!$clinicId) {
                Log::warning('Webhook: clinic_id missing on payment', ['payment_id' => $payment->id]);
                return;
            }

            $clinic = \App\Models\ExaminerClinic::find($clinicId);
            if (!$clinic) {
                Log::warning('Webhook: clinic not found', ['clinic_id' => $clinicId]);
                return;
            }

            $user = \App\Models\User::find($payment->user_id);
            if (!$user) {
                Log::warning('Webhook: user not found', ['user_id' => $payment->user_id]);
                return;
            }

            // Idempotent — already enrolled
            if ($clinic->isEnrolledBy($user)) {
                Log::info('Webhook: already enrolled, skipping', ['clinic_id' => $clinicId, 'user_id' => $user->id]);
                return;
            }

            if ($clinic->isFull()) {
                Log::warning('Webhook: clinic full, cannot enroll', ['clinic_id' => $clinicId]);
                return;
            }

            $enrollment = \App\Models\ClinicEnrollment::create([
                'clinic_id'    => $clinic->id,
                'student_id'   => $user->id,
                'payment_id'   => $payment->id,
                'session_code' => strtoupper(\Illuminate\Support\Str::random(8)),
            ]);

            if ($clinic->fresh()->isFull() && $clinic->parent_clinic_id === null) {
                $clinic->autoCreateNextSession();
            }

            Log::info('Webhook: clinic/group enrollment confirmed', [
                'clinic_id'     => $clinicId,
                'user_id'       => $user->id,
                'enrollment_id' => $enrollment->id,
            ]);
        }
    }
}
