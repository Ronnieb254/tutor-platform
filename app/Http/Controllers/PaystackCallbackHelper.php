<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\ClinicEnrollment;
use App\Services\BookingService;
use Illuminate\Support\Facades\Log;
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
            $enrollment = ClinicEnrollment::where('payment_id', $payment->id)->first();
            if ($enrollment) {
                $route = $type === 'group_session'
                    ? route('group-sessions.show', $enrollment->clinic_id)
                    : route('clinic.my-bookings');
                return redirect($route)->with('success', $message);
            }
            return redirect()->route('clinic.my-bookings')->with('success', $message);
        }

        $booking  = $payment->booking;
        $redirect = $booking ? route('bookings.show', $booking->uuid) : route('dashboard');
        return redirect($redirect)->with('success', $message);
    }

    /**
     * Create enrollment for clinic / group session after payment verified.
     * Uses the payment's user relationship so this works in webhook context too.
     */
    public static function confirmClinicEnrollment(Payment $payment): \Illuminate\Http\RedirectResponse
    {
        $clinicId = $payment->clinic_id;

        // Resolve user: prefer the authenticated session, fall back to payment owner.
        $user = auth()->user() ?? $payment->user;

        if (!$clinicId || !$user) {
            Log::error('confirmClinicEnrollment: missing clinic_id or user', [
                'payment_id' => $payment->id,
                'clinic_id'  => $clinicId,
                'user_id'    => $payment->user_id,
            ]);
            return redirect()->route('dashboard')->with('error', 'Could not confirm enrollment. Please contact support.');
        }

        $clinic = \App\Models\ExaminerClinic::find($clinicId);
        if (!$clinic) {
            return redirect()->route('dashboard')->with('error', 'Session not found. Please contact support.');
        }

        // Idempotent — already enrolled with an active enrollment
        $existing = ClinicEnrollment::where('clinic_id', $clinicId)
            ->where('student_id', $user->id)
            ->first();

        if ($existing && $existing->session_code) {
            $route = $payment->payment_type === 'group_session'
                ? route('group-sessions.show', $clinic->id)
                : route('clinic.my-bookings');
            return redirect($route)->with('success', 'You are already enrolled. Code: ' . $existing->session_code);
        }

        // Capacity check
        if ($clinic->isFull()) {
            return redirect()->route('examiner-clinic')
                ->with('error', 'Session became full while processing. Please contact support for a refund.');
        }

        // If a provisional enrollment exists (old manual flow), upgrade it.
        if ($existing) {
            $existing->update([
                'session_code' => strtoupper(Str::random(8)),
                'attended'     => false,
            ]);
            $enrollment = $existing->fresh();
        } else {
            $enrollment = ClinicEnrollment::create([
                'clinic_id'   => $clinic->id,
                'student_id'  => $user->id,
                'payment_id'  => $payment->id,
                'session_code'=> strtoupper(Str::random(8)),
                'attended'    => false,
            ]);
        }

        // Auto-create overflow session if now full
        if ($clinic->fresh()->isFull() && $clinic->parent_clinic_id === null) {
            try {
                $clinic->autoCreateNextSession();
            } catch (\Throwable $e) {
                Log::warning('autoCreateNextSession failed: ' . $e->getMessage());
            }
        }

        // Send the correct notification based on payment type
        try {
            if ($payment->payment_type === 'group_session') {
                $user->notify(new \App\Notifications\GroupSessionEnrollmentConfirmed(
                    $enrollment,
                    $enrollment->session_code
                ));
            } else {
                $user->notify(new \App\Notifications\ClinicEnrollmentConfirmed($enrollment));
            }
        } catch (\Throwable $e) {
            Log::warning('Enrollment confirmation notification failed: ' . $e->getMessage());
        }

        $isGroup = $payment->payment_type === 'group_session';
        $route   = $isGroup ? route('group-sessions.my-sessions') : route('clinic.my-bookings');
        $dateStr = $clinic->scheduled_at->setTimezone('Africa/Nairobi')->format('D, M j \a\t g:ia') . ' EAT';

        // Notify tutor for group session enrollment
        if ($isGroup && $clinic->tutor) {
            try {
                $clinic->tutor->notify(new \App\Notifications\StudentEnrolledInGroupSession($enrollment));
            } catch (\Throwable $e) {
                Log::warning('StudentEnrolledInGroupSession notification failed: ' . $e->getMessage());
            }
        }

        return redirect($route)
            ->with('success', "Enrolled! Your session code: {$enrollment->session_code}. Session on {$dateStr}.");
    }
}
