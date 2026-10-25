<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Refund;
use App\Services\RefundService;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function __construct(protected RefundService $refundService)
    {
    }

    /**
     * Request a refund for first 20 minutes guarantee.
     */
    public function requestFirst20MinRefund(Request $request, Booking $booking)
    {
        $user = auth()->user();

        // Only the student who booked can request refund
        if ($booking->student_id !== $user->id) {
            abort(403, 'You can only request refunds for your own bookings.');
        }

        try {
            $refund = $this->refundService->requestFirst20MinRefund(
                $booking,
                $request->input('description')
            );

            // Auto-process since it's pre-approved
            $this->refundService->processRefund($refund);

            return back()->with('success', 'Your refund of KSh ' . number_format($refund->amount, 2) . ' has been processed and added to your wallet.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Request a refund for other reasons.
     */
    public function requestRefund(Request $request, Booking $booking)
    {
        $user = auth()->user();

        if ($booking->student_id !== $user->id) {
            abort(403, 'You can only request refunds for your own bookings.');
        }

        $request->validate([
            'reason' => 'required|in:technical_issue,quality_issue,other',
            'description' => 'required|string|max:1000',
        ]);

        try {
            $refund = $this->refundService->requestRefund(
                $booking,
                $request->reason,
                $request->description
            );

            return back()->with('success', 'Your refund request has been submitted and will be reviewed by our team within 24 hours.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Admin: Approve a refund request.
     */
    public function approve(Request $request, Refund $refund)
    {
        $user = auth()->user();

        if (!$user->isAdmin()) {
            abort(403);
        }

        try {
            $this->refundService->approveRefund(
                $refund,
                $user,
                $request->input('admin_note')
            );

            return back()->with('success', 'Refund approved and processed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Admin: Reject a refund request.
     */
    public function reject(Request $request, Refund $refund)
    {
        $user = auth()->user();

        if (!$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'admin_note' => 'required|string|max:500',
        ]);

        try {
            $this->refundService->rejectRefund(
                $refund,
                $user,
                $request->admin_note
            );

            return back()->with('success', 'Refund request rejected.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
