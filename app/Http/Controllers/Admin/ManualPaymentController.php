<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClinicEnrollment;
use App\Models\Payment;
use App\Notifications\BookingConfirmedStudent;
use App\Notifications\BookingConfirmedTutor;
use App\Notifications\PaymentRejected;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ManualPaymentController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    /**
     * Paginated list of pending_verification payments, newest first.
     */
    public function index()
    {
        $payments = Payment::where('status', 'pending_verification')
            ->with(['user', 'booking.tutor', 'booking.student', 'booking.subject'])
            ->latest()
            ->paginate(20);

        $recentlyVerified = Payment::whereIn('status', ['success', 'failed'])
            ->whereNotNull('verified_at')
            ->where('verified_at', '>=', now()->subHours(24))
            ->with(['user', 'booking', 'verifiedBy'])
            ->latest('verified_at')
            ->limit(10)
            ->get();

        return view('pages.admin.manual-payments', compact('payments', 'recentlyVerified'));
    }

    /**
     * Mark payment as verified/success, confirm related booking or clinic, send codes.
     */
    public function verify(Request $request, Payment $payment)
    {
        if ($payment->status !== 'pending_verification') {
            return back()->with('error', 'Payment is not pending verification.');
        }

        DB::transaction(function () use ($payment, $request) {
            $payment->update([
                'status'      => 'success',
                'paid_at'     => now(),
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'admin_note'  => $request->input('note'),
            ]);

            if ($payment->booking_id) {
                $this->confirmRelatedBooking($payment);
            }

            if ($payment->clinic_id) {
                $this->confirmRelatedClinic($payment);
            }
        });

        return back()->with('success', 'Payment verified. Session codes sent to student and tutor.');
    }

    /**
     * Reject a pending payment, cancel its booking, and notify the student.
     */
    public function reject(Request $request, Payment $payment)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        if ($payment->status !== 'pending_verification') {
            return back()->with('error', 'Payment is not pending verification.');
        }

        $payment->update([
            'status'      => 'failed',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'admin_note'  => $request->input('reason'),
        ]);

        // Cancel associated booking so it does not sit as pending
        if ($payment->booking) {
            $payment->booking->update([
                'status'              => 'cancelled',
                'cancellation_reason' => 'Payment rejected: ' . $request->input('reason'),
                'cancelled_at'        => now(),
            ]);
        }

        try {
            Notification::sendNow($payment->user, new PaymentRejected($payment));
        } catch (\Throwable $e) {
            Log::warning('PaymentRejected notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Payment rejected. Student has been notified.');
    }

    /**
     * Generate session codes and notify student + tutor for a booking payment.
     */
    private function confirmRelatedBooking(Payment $payment): void
    {
        $booking = $payment->booking->fresh();

        if ($booking->status === 'confirmed') {
            return;
        }

        try {
            $this->bookingService->confirmBooking($booking);
        } catch (\Throwable $e) {
            // Fallback: direct update without service layer
            $booking->update([
                'status'               => 'confirmed',
                'session_code_tutor'   => 'TUT-' . strtoupper(Str::random(6)),
                'session_code_student' => 'STU-' . strtoupper(Str::random(6)),
                'meeting_room'         => 'Mwalimu-session-' . $booking->uuid,
                'meeting_url'          => 'https://meet.jit.si/Mwalimu-session-' . $booking->uuid,
            ]);
            $booking->refresh()->load(['student', 'tutor', 'subject']);
            try {
                Notification::sendNow($booking->student, new BookingConfirmedStudent($booking));
            } catch (\Throwable $ne) {
                Log::warning('BookingConfirmedStudent notification failed: ' . $ne->getMessage());
            }
            try {
                Notification::sendNow($booking->tutor, new BookingConfirmedTutor($booking));
            } catch (\Throwable $ne) {
                Log::warning('BookingConfirmedTutor notification failed: ' . $ne->getMessage());
            }
        }
    }

    /**
     * Create a ClinicEnrollment with session code for a clinic payment.
     */
    private function confirmRelatedClinic(Payment $payment): void
    {
        $already = ClinicEnrollment::where('payment_id', $payment->id)->exists();
        if ($already) {
            return;
        }

        $sessionCode = 'CLI-' . strtoupper(Str::random(6));
        ClinicEnrollment::create([
            'clinic_id'    => $payment->clinic_id,
            'student_id'   => $payment->user_id,
            'payment_id'   => $payment->id,
            'session_code' => $sessionCode,
            'attended'     => false,
        ]);

        try {
            $payment->user->notify(new \App\Notifications\ClinicEnrollmentConfirmed(
                $payment->clinic,
                $sessionCode
            ));
        } catch (\Throwable $e) {
            Log::warning('ClinicEnrollmentConfirmed notification failed: ' . $e->getMessage());
        }
    }
}
