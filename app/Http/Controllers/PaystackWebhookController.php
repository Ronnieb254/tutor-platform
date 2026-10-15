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
        if (!$payment || $payment->status === 'success') return;

        $payment->update([
            'status'  => 'success',
            'paid_at' => now(),
        ]);

        $booking = $payment->booking;
        if ($booking && $booking->status === 'pending') {
            $this->booking->confirmBooking($booking);
        }
    }
}
