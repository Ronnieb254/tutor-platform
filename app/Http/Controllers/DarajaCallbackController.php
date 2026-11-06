<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\BookingService;
use App\Services\DarajaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Handles inbound callbacks from Safaricom Daraja (M-Pesa STK Push).
 *
 * Safaricom POSTs to /api/daraja/callback after the customer completes
 * or dismisses the STK Push prompt.  This endpoint must be publicly
 * reachable (no auth middleware) and excluded from CSRF.
 */
class DarajaCallbackController extends Controller
{
    public function __construct(
        protected DarajaService  $daraja,
        protected BookingService $bookingService
    ) {}

    /**
     * Receive the Daraja STK Push callback.
     */
    public function handle(Request $request): \Illuminate\Http\JsonResponse
    {
        $payload = $request->all();

        Log::info('Daraja callback received', [
            'ip'           => $request->ip(),
            'payload_size' => strlen($request->getContent()),
            'preview'      => array_slice($payload, 0, 3),
        ]);

        $parsed = $this->daraja->parseCallback($payload);

        Log::info('Daraja callback parsed', [
            'success'     => $parsed['success'],
            'result_code' => $parsed['result_code'],
            'result_desc' => $parsed['result_desc'],
            'checkout_id' => $parsed['checkout_request_id'],
            'receipt'     => $parsed['mpesa_receipt'],
        ]);

        // Find payment by checkout_request_id stored at STK Push time
        $checkoutId = $parsed['checkout_request_id'];
        if (!$checkoutId) {
            Log::warning('Daraja callback: missing CheckoutRequestID');
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        $payment = Payment::where('checkout_request_id', $checkoutId)->first();

        if (!$payment) {
            Log::warning('Daraja callback: payment not found', ['checkout_request_id' => $checkoutId]);
            // Always return 200/success so Safaricom does not retry indefinitely
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        // Idempotent: already handled
        if ($payment->status === 'success') {
            Log::info('Daraja callback: payment already confirmed', ['payment_id' => $payment->id]);
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        if ($parsed['success']) {
            $payment->update([
                'status'         => 'success',
                'paid_at'        => now(),
                'mpesa_receipt'  => $parsed['mpesa_receipt'],
                'payer_phone'    => $parsed['phone'],
            ]);

            $this->confirmRelatedRecord($payment);

            Log::info('Daraja callback: payment confirmed', [
                'payment_id' => $payment->id,
                'receipt'    => $parsed['mpesa_receipt'],
            ]);
        } else {
            $payment->update([
                'status'         => 'failed',
                'failure_reason' => $parsed['result_desc'],
            ]);

            Log::info('Daraja callback: payment failed', [
                'payment_id'  => $payment->id,
                'result_code' => $parsed['result_code'],
                'result_desc' => $parsed['result_desc'],
            ]);
        }

        // Safaricom expects a 200 with this exact body
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    /**
     * Confirm the booking / clinic enrollment linked to a payment.
     */
    private function confirmRelatedRecord(Payment $payment): void
    {
        $type = $payment->payment_type ?? null;

        // ── 1-on-1 booking ────────────────────────────────────────────────
        if ($type === null || $type === 'booking' || $payment->booking_id) {
            $booking = $payment->booking;
            if ($booking && $booking->status === 'pending') {
                try {
                    $this->bookingService->confirmBooking($booking);
                    Log::info('Daraja callback: booking confirmed', ['booking_id' => $booking->id]);
                } catch (\Throwable $e) {
                    Log::error('Daraja callback: confirmBooking failed', [
                        'booking_id' => $booking->id,
                        'error'      => $e->getMessage(),
                    ]);
                }
            }
            return;
        }

        // ── Clinic / Group session ─────────────────────────────────────────
        if (in_array($type, ['clinic', 'group_session'])) {
            $clinicId = $payment->clinic_id;
            $clinic   = $clinicId ? \App\Models\ExaminerClinic::find($clinicId) : null;
            $user     = $payment->user_id ? \App\Models\User::find($payment->user_id) : null;

            if (!$clinic || !$user) {
                Log::warning('Daraja callback: clinic/user not found', [
                    'clinic_id' => $clinicId,
                    'user_id'   => $payment->user_id,
                ]);
                return;
            }

            if ($clinic->isEnrolledBy($user)) {
                Log::info('Daraja callback: already enrolled', ['clinic_id' => $clinicId]);
                return;
            }

            if ($clinic->isFull()) {
                Log::warning('Daraja callback: clinic full', ['clinic_id' => $clinicId]);
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

            // Notify student with their unique access code
            try {
                $user->notify(new \App\Notifications\ClinicEnrollmentConfirmed($enrollment));
            } catch (\Throwable $e) {
                Log::warning('Daraja callback: could not send ClinicEnrollmentConfirmed', [
                    'enrollment_id' => $enrollment->id,
                    'error'         => $e->getMessage(),
                ]);
            }

            Log::info('Daraja callback: clinic enrollment confirmed', [
                'clinic_id'     => $clinicId,
                'user_id'       => $user->id,
                'enrollment_id' => $enrollment->id,
            ]);
        }

        // ── Course enrollment ──────────────────────────────────────────────
        if ($type === 'course' && $payment->course_id) {
            \App\Models\CourseEnrollment::firstOrCreate([
                'student_id' => $payment->user_id,
                'course_id'  => $payment->course_id,
            ]);
            Log::info('Daraja callback: course enrollment confirmed', [
                'course_id' => $payment->course_id,
                'user_id'   => $payment->user_id,
            ]);
        }
    }

    /**
     * Poll endpoint — the frontend calls this after initiating STK Push
     * to check whether the callback has arrived and the payment confirmed.
     */
    public function status(Request $request): \Illuminate\Http\JsonResponse
    {
        $reference = $request->get('reference');

        if (!$reference) {
            return response()->json(['status' => 'error', 'message' => 'Missing reference'], 400);
        }

        $payment = Payment::where('mpesa_reference', $reference)->first();

        if (!$payment) {
            return response()->json(['status' => 'pending', 'message' => 'Waiting for payment']);
        }

        if ($payment->status === 'success') {
            $booking = $payment->booking;
            return response()->json([
                'status'       => 'success',
                'booking_uuid' => $booking?->uuid,
                'student_code' => $booking?->fresh()?->session_code_student,
                'booking_url'  => $booking ? route('bookings.show', $booking->uuid) : null,
            ]);
        }

        if ($payment->status === 'failed') {
            return response()->json([
                'status'  => 'failed',
                'message' => $payment->failure_reason ?? 'Payment was not completed.',
            ]);
        }

        return response()->json(['status' => 'pending', 'message' => 'Awaiting M-Pesa confirmation']);
    }
}
