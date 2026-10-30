<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\ClinicEnrollment;
use App\Services\BookingService;
use Illuminate\Support\Str;

class PaystackCallbackHelper
{
    /**
     * Confirm whatever session type is linked to this payment,
     * then redirect to the right success page.
     */
    public static function confirmAndRedirect(Payment $payment): \Illuminate\Http\RedirectResponse
    {
        $type = $payment->payment_type ?? null;

        // ── 1-on-1 booking ────────────────────────────────────────────────────
        if ($type === null || $type === 'booking' || $payment->booking_id) {
            $booking = $payment->booking;
            if ($booking && $booking->status === 'pending') {
                app(BookingService::class)->confirmBooking($booking);
            }
            $booking = $payment->booking()->first();
            $redirect = $booking ? route('bookings.show', $booking->uuid) : route('dashboard');
            return redirect($redirect)->with('success', 'Payment confirmed! Your booking is now active.');
        }

        // ── Clinic / Group session ─────────────────────────────────────────────
        if (in_array($type, ['clinic', 'group_session'])) {
            return static::confirmClinicEnrollment($payment);
        }

        // Fallback
        return redirect()->route('dashboard')->with('success', 'Payment confirmed!');
    }

    /**
     * Redirect after payment was already confirmed (webhook beat us here).
     */
    public static function redirectAfterPayment(Payment $payment, string $message): \Illuminate\Http\RedirectResponse
    {
        $type = $payment->payment_type ?? null;

        if (in_array($type, ['clinic', 'group_session'])) {
            // Find the enrollment
            $enrollment = ClinicEnrollment::where('payment_id', $payment->id)->first();
            if ($enrollment) {
                $route = $type === 'group_session'
                    ? route('group-sessions.show', $enrollment->clinic_id)
                    : route('clinic.my-bookings');
                return redirect($route)->with('success', $message);
            }
            return redirect()->route('clinic.my-bookings')->with('success', $message);
        }

        // 1-on-1 booking
        $booking = $payment->booking;
        $redirect = $booking ? route('bookings.show', $booking->uuid) : route('dashboard');
        return redirect($redirect)->with('success', $message);
    }

    /**
     * Create enrollment for clinic / group session after payment verified.
     */
    public static function confirmClinicEnrollment(Payment $payment): \Illuminate\Http\RedirectResponse
    {
        $clinicId = $payment->clinic_id;
        $user     = auth()->user();

        if (!$clinicId || !$user) {
            return redirect()->route('dashboard')->with('error', 'Could not confirm enrollment. Please contact support.');
        }

        $clinic = \App\Models\ExaminerClinic::find($clinicId);
        if (!$clinic) {
            return redirect()->route('dashboard')->with('error', 'Session not found. Please contact support.');
        }

        // Idempotent — already enrolled
        if ($clinic->isEnrolledBy($user)) {
            $route = $payment->payment_type === 'group_session'
                ? route('group-sessions.show', $clinic->id)
                : route('clinic.my-bookings');
            return redirect($route)->with('warning', 'You are already enrolled in this session.');
        }

        // Capacity check (session may have filled while payment was processing)
        if ($clinic->isFull()) {
            return redirect()->route('examiner-clinic')
                ->with('error', 'Session became full while processing. Please contact support for a refund.');
        }

        $enrollment = ClinicEnrollment::create([
            'clinic_id'    => $clinic->id,
            'student_id'   => $user->id,
            'payment_id'   => $payment->id,
            'session_code' => strtoupper(Str::random(8)),
        ]);

        // Auto-create overflow session if now full
        if ($clinic->fresh()->isFull() && $clinic->parent_clinic_id === null) {
            $clinic->autoCreateNextSession();
        }

        $isGroup   = $payment->payment_type === 'group_session';
        $route     = $isGroup ? route('group-sessions.show', $clinic->id) : route('clinic.my-bookings');
        $dateStr   = $clinic->scheduled_at->format('D, M j \a\t g:ia');

        return redirect($route)
            ->with('success', "Booking confirmed! Your session code: {$enrollment->session_code}. The session is on {$dateStr}.");
    }
}
