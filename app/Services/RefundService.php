<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;

class RefundService
{
    /**
     * Check if a booking is eligible for the first 20 minutes guarantee refund.
     */
    public function isEligibleForFirst20MinRefund(Booking $booking): bool
    {
        // Must be completed
        if ($booking->status !== 'completed') {
            return false;
        }

        // Must have a successful payment
        $payment = $booking->payment;
        if (!$payment || $payment->status !== 'success') {
            return false;
        }

        // Cannot already have a refund
        if ($booking->refund()->exists()) {
            return false;
        }

        // Must be within 24 hours of session completion
        if (!$booking->completed_at || $booking->completed_at->diffInHours(now()) > 24) {
            return false;
        }

        // Session duration must be 20 minutes or less (first session guarantee)
        // OR check if this is user's first ever session
        $user = $booking->student;
        $completedSessionsCount = Booking::where('student_id', $user->id)
            ->where('status', 'completed')
            ->count();

        // Eligible if it's their first completed session AND duration is 20 min
        return $completedSessionsCount === 1 && $booking->duration_minutes === 20;
    }

    /**
     * Request a refund for the first 20 minutes guarantee.
     */
    public function requestFirst20MinRefund(Booking $booking, ?string $description = null): Refund
    {
        if (!$this->isEligibleForFirst20MinRefund($booking)) {
            throw new \Exception('This booking is not eligible for a refund.');
        }

        $payment = $booking->payment;
        $amount = $payment->amount;

        return Refund::create([
            'booking_id' => $booking->id,
            'payment_id' => $payment->id,
            'user_id' => $booking->student_id,
            'amount' => $amount,
            'currency' => $payment->currency ?? 'KES',
            'reason' => 'first_20min_guarantee',
            'description' => $description ?? '100% money-back guarantee for first 20-minute session',
            'status' => 'approved', // Auto-approve for first 20 min guarantee
            'approved_at' => now(),
        ]);
    }

    /**
     * Request a refund for other reasons (requires admin approval).
     */
    public function requestRefund(
        Booking $booking,
        string $reason,
        ?string $description = null,
        ?float $customAmount = null
    ): Refund {
        $payment = $booking->payment;
        if (!$payment || $payment->status !== 'success') {
            throw new \Exception('No successful payment found for this booking.');
        }

        if ($booking->refund()->exists()) {
            throw new \Exception('A refund has already been requested for this booking.');
        }

        $amount = $customAmount ?? $payment->amount;

        return Refund::create([
            'booking_id' => $booking->id,
            'payment_id' => $payment->id,
            'user_id' => $booking->student_id,
            'amount' => $amount,
            'currency' => $payment->currency ?? 'KES',
            'reason' => $reason,
            'description' => $description,
            'status' => 'pending', // Requires admin approval
        ]);
    }

    /**
     * Approve a refund request (admin action).
     */
    public function approveRefund(Refund $refund, User $approver, ?string $adminNote = null): void
    {
        if (!$approver->isAdmin()) {
            throw new \Exception('Only admins can approve refunds.');
        }

        $refund->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'admin_note' => $adminNote,
        ]);

        // Process the refund immediately
        $this->processRefund($refund);
    }

    /**
     * Reject a refund request (admin action).
     */
    public function rejectRefund(Refund $refund, User $approver, string $adminNote): void
    {
        if (!$approver->isAdmin()) {
            throw new \Exception('Only admins can reject refunds.');
        }

        $refund->update([
            'status' => 'rejected',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'admin_note' => $adminNote,
        ]);
    }

    /**
     * Process an approved refund - credit user's wallet.
     */
    public function processRefund(Refund $refund): void
    {
        if ($refund->status !== 'approved') {
            throw new \Exception('Refund must be approved before processing.');
        }

        if ($refund->isCompleted()) {
            throw new \Exception('Refund has already been processed.');
        }

        $user = $refund->user;
        $wallet = $user->wallet;

        // Create wallet if it doesn't exist
        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'currency' => $refund->currency,
            ]);
        }

        // Credit the wallet
        $wallet->credit(
            $refund->amount,
            'Refund for booking #' . $refund->booking_id . ' - ' . ucfirst(str_replace('_', ' ', $refund->reason)),
            'REFUND-' . $refund->id
        );

        // Update payment status
        $refund->payment->update(['status' => 'refunded']);

        // Mark refund as completed
        $refund->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Auto-process first 20 min guarantee refunds (can be run as a scheduled job).
     */
    public function autoProcessFirst20MinRefunds(): void
    {
        $pendingRefunds = Refund::where('reason', 'first_20min_guarantee')
            ->where('status', 'approved')
            ->whereNull('completed_at')
            ->get();

        foreach ($pendingRefunds as $refund) {
            try {
                $this->processRefund($refund);
            } catch (\Exception $e) {
                \Log::error('Failed to process refund #' . $refund->id . ': ' . $e->getMessage());
            }
        }
    }
}
