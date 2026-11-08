<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\BookingService;
use App\Services\IntaSendService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IntaSendWebhookController extends Controller
{
    public function __construct(
        protected IntaSendService $intaSend,
        protected BookingService  $bookingService
    ) {}

    /**
     * Handle IntaSend payment webhook.
     */
    public function handle(Request $request)
    {
        // 1. Verify webhook signature
        if (!$this->intaSend->verifyWebhook($request)) {
            Log::warning('IntaSend webhook: invalid signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $payload = $request->json()->all();
        Log::info('IntaSend webhook received', ['payload' => $payload]);

        // 2. Parse payload
        $parsed = $this->intaSend->parseWebhook($payload);

        if (!$parsed['reference']) {
            Log::warning('IntaSend webhook: no reference found', ['payload' => $payload]);
            return response()->json(['status' => 'ok', 'note' => 'no reference']);
        }

        // 3. Find the payment record
        $payment = Payment::where('mpesa_reference', $parsed['reference'])->first();

        if (!$payment) {
            Log::warning('IntaSend webhook: payment not found', ['reference' => $parsed['reference']]);
            return response()->json(['status' => 'ok', 'note' => 'payment not found']);
        }

        // Avoid reprocessing
        if ($payment->status === 'success') {
            return response()->json(['status' => 'ok', 'note' => 'already processed']);
        }

        // 4. Update payment status
        $updateData = ['status' => $parsed['status']];

        if ($parsed['status'] === 'success') {
            $updateData['paid_at'] = now();
            if ($parsed['receipt']) {
                $updateData['mpesa_receipt'] = $parsed['receipt'];
            }
        }

        $payment->update($updateData);

        // 5. Confirm booking on success
        if ($parsed['status'] === 'success' && $payment->booking) {
            try {
                $booking = $payment->booking;
                if ($booking->status === 'pending') {
                    $this->bookingService->confirmBooking($booking);
                    Log::info('IntaSend webhook: booking confirmed', ['booking_id' => $booking->id]);
                }
            } catch (\Throwable $e) {
                Log::error('IntaSend webhook: confirmBooking failed', [
                    'error'      => $e->getMessage(),
                    'payment_id' => $payment->id,
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
