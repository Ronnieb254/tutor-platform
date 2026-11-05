<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Wallet;
use App\Notifications\SessionCompletedStudent;
use App\Notifications\SessionCompletedTutor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SessionLifecycleService
{
    /**
     * Finalise a booking after the session ends.
     *
     * 1. Classify delivery (fully / partially / not_delivered)
     * 2. Mark booking completed
     * 3. Credit tutor wallet (if delivered at all)
     * 4. Credit student wallet refund (if not_delivered or partially)
     * 5. Send notifications to both parties
     */
    public function finaliseSession(Booking $booking): void
    {
        if ($booking->status === 'completed') {
            Log::info("Session {$booking->uuid} already finalised.");
            return;
        }

        $booking->loadMissing(['student', 'tutor', 'subject']);

        // ── 1. Classify ────────────────────────────────────────────────────
        $delivery = $booking->classifyDelivery();

        // ── 2. Persist in transaction ───────────────────────────────────────
        DB::transaction(function () use ($booking, $delivery) {
            $booking->update([
                'status'          => 'completed',
                'completed_at'    => $booking->completed_at ?? now(),
                'delivery_status' => $delivery,
            ]);

            // ── 3. Tutor earnings ──────────────────────────────────────────
            if (in_array($delivery, ['fully_delivered', 'partially_delivered'])
                && !$booking->earnings_settled
                && (float) $booking->tutor_earning > 0
            ) {
                // For partial delivery (<80%), pay tutor proportionally
                $payFraction = $delivery === 'partially_delivered' ? 0.5 : 1.0;
                $earnAmount  = round((float) $booking->tutor_earning * $payFraction, 2);

                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $booking->tutor_id],
                    ['balance' => 0, 'currency' => 'KES']
                );
                $wallet->credit(
                    $earnAmount,
                    "Earnings from session #{$booking->uuid} ({$delivery})",
                    $booking->uuid
                );

                $booking->update(['earnings_settled' => true]);
            }

            // ── 4. Student refund ──────────────────────────────────────────
            if (!$booking->refund_credited) {
                $refundAmount = 0.0;

                if ($delivery === 'not_delivered') {
                    // Full refund to student wallet
                    $refundAmount = (float) $booking->total_amount;
                } elseif ($delivery === 'partially_delivered') {
                    // 50% refund to student wallet
                    $refundAmount = round((float) $booking->total_amount * 0.5, 2);
                }

                if ($refundAmount > 0) {
                    $studentWallet = Wallet::firstOrCreate(
                        ['user_id' => $booking->student_id],
                        ['balance' => 0, 'currency' => 'KES']
                    );
                    $studentWallet->credit(
                        $refundAmount,
                        "Refund for session #{$booking->uuid} ({$delivery})",
                        $booking->uuid
                    );

                    $booking->update(['refund_credited' => true]);
                }
            }
        });

        // ── 5. Notify both parties ─────────────────────────────────────────
        $booking->refresh()->loadMissing(['student', 'tutor', 'subject']);

        try {
            \Illuminate\Support\Facades\Notification::sendNow(
                $booking->student,
                new SessionCompletedStudent($booking)
            );
        } catch (\Throwable $e) {
            Log::warning("SessionCompletedStudent notification failed: {$e->getMessage()}");
        }

        try {
            \Illuminate\Support\Facades\Notification::sendNow(
                $booking->tutor,
                new SessionCompletedTutor($booking)
            );
        } catch (\Throwable $e) {
            Log::warning("SessionCompletedTutor notification failed: {$e->getMessage()}");
        }

        Log::info("Session {$booking->uuid} finalised as {$delivery}.");
    }
}
