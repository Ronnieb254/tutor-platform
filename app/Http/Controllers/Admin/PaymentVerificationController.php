<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClinicEnrollment;
use App\Models\Payment;
use App\Notifications\GroupSessionEnrollmentConfirmed;
use App\Notifications\StudentEnrolledInGroupSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentVerificationController extends Controller
{
    /**
     * Show pending group session payments for verification
     */
    public function index(Request $request)
    {
        $payments = Payment::where('payment_type', 'group_session')
            ->where('status', 'pending_verification')
            ->with(['user', 'clinic'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.payments.pending-group-sessions', compact('payments'));
    }

    /**
     * Verify a payment and send notifications
     */
    public function verify(Request $request, Payment $payment)
    {
        // Check authorization
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        // Verify it's a group session payment
        if ($payment->payment_type !== 'group_session') {
            return back()->with('error', 'This is not a group session payment.');
        }

        // Check if already verified
        if ($payment->status === 'success') {
            return back()->with('info', 'This payment has already been verified.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($request->action === 'reject') {
            // Mark payment as failed
            $payment->update([
                'status' => 'failed',
                'admin_note' => $request->notes ?? 'Payment rejected by admin',
                'verified_at' => now(),
            ]);

            // Update enrollment to cancelled
            $enrollment = ClinicEnrollment::where('payment_id', $payment->id)->first();
            if ($enrollment) {
                $enrollment->update(['status' => 'cancelled']);
            }

            return back()->with('success', 'Payment rejected and student notified.');
        }

        try {
            // Mark payment as verified
            $payment->update([
                'status' => 'success',
                'verified_at' => now(),
            ]);

            // Get the enrollment
            $enrollment = ClinicEnrollment::where('payment_id', $payment->id)->first();

            if (!$enrollment) {
                return back()->with('error', 'Enrollment not found for this payment.');
            }

            // Generate session code for the student
            $sessionCode = 'SES-' . strtoupper(Str::random(8));

            // Update enrollment status and add session code
            $enrollment->update([
                'status' => 'confirmed',
                'session_code' => $sessionCode,
            ]);

            // Send notification to student
            $student = $enrollment->student;
            $student->notify(new GroupSessionEnrollmentConfirmed($enrollment, $sessionCode));

            // Send notification to tutor
            $tutor = $enrollment->clinic->tutor;
            if ($tutor) {
                $tutor->notify(new StudentEnrolledInGroupSession($enrollment));
            }

            return back()->with('success', 'Payment verified! Student and tutor notified. Session code: ' . $sessionCode);

        } catch (\Exception $e) {
            \Log::error('Payment verification failed: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
            ]);
            return back()->with('error', 'Failed to verify payment: ' . $e->getMessage());
        }
    }

    /**
     * Bulk verify payments
     */
    public function bulkVerify(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'payment_ids' => 'required|array',
            'payment_ids.*' => 'exists:payments,id',
        ]);

        $paymentIds = $request->payment_ids;
        $verified = 0;
        $failed = 0;

        foreach ($paymentIds as $paymentId) {
            $payment = Payment::find($paymentId);

            if ($payment->payment_type !== 'group_session' || $payment->status !== 'pending_verification') {
                $failed++;
                continue;
            }

            try {
                $payment->update(['status' => 'success', 'verified_at' => now()]);

                $enrollment = ClinicEnrollment::where('payment_id', $payment->id)->first();
                if ($enrollment) {
                    $sessionCode = 'SES-' . strtoupper(Str::random(8));
                    $enrollment->update(['status' => 'confirmed', 'session_code' => $sessionCode]);

                    $student = $enrollment->student;
                    $student->notify(new GroupSessionEnrollmentConfirmed($enrollment, $sessionCode));

                    $tutor = $enrollment->clinic->tutor;
                    if ($tutor) {
                        $tutor->notify(new StudentEnrolledInGroupSession($enrollment));
                    }

                    $verified++;
                }
            } catch (\Exception $e) {
                $failed++;
                \Log::error('Bulk verification failed for payment ' . $paymentId . ': ' . $e->getMessage());
            }
        }

        return back()->with('success', "Verified $verified payments. " . ($failed > 0 ? "$failed failed." : ''));
    }
}
