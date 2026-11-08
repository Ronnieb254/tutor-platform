<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\IMBankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class IMBankPaymentController extends Controller
{
    protected IMBankService $imbank;

    public function __construct(IMBankService $imbank)
    {
        $this->imbank = $imbank;
    }

    /**
     * Handle I&M Bank payment callback
     * This is called by I&M Bank API when a payment is processed
     */
    public function callback(Request $request)
    {
        Log::info('I&M Bank callback received', [
            'body' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        try {
            // Verify signature if provided
            $signature = $request->header('X-IMBank-Signature');
            if ($signature) {
                $payload = $request->getContent();
                if (!$this->imbank->verifyCallbackSignature($payload, $signature)) {
                    Log::warning('I&M Bank: invalid callback signature');
                    return response()->json(['error' => 'Invalid signature'], 401);
                }
            }

            // Parse callback data
            $data = $this->imbank->parseCallback($request->all());

            Log::info('I&M Bank callback parsed', $data);

            // Find the payment record
            $payment = Payment::where('imbank_reference', $data['reference'])
                ->orWhere('imbank_transaction_id', $data['transaction_id'])
                ->first();

            if (!$payment) {
                Log::warning('I&M Bank callback: payment not found', [
                    'reference' => $data['reference'],
                    'transaction_id' => $data['transaction_id'],
                ]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // Update payment based on status
            DB::transaction(function () use ($payment, $data) {
                $payment->imbank_transaction_id = $data['transaction_id'];
                $payment->imbank_receipt_number = $data['receipt_number'];
                $payment->payment_method = $data['payment_method'];
                $payment->gateway_response = $data['raw_response'];

                if ($data['status'] === 'success') {
                    $payment->status = 'completed';
                    $payment->paid_at = now();
                    
                    Log::info('I&M Bank payment completed', [
                        'payment_id' => $payment->id,
                        'amount' => $payment->amount,
                        'receipt' => $data['receipt_number'],
                    ]);

                    // Trigger post-payment actions (booking confirmation, notifications, etc.)
                    $this->handleSuccessfulPayment($payment);

                } elseif ($data['status'] === 'failed' || $data['status'] === 'cancelled') {
                    $payment->status = 'failed';
                    
                    Log::info('I&M Bank payment failed', [
                        'payment_id' => $payment->id,
                        'reason' => $data['message'],
                    ]);
                }

                $payment->save();
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Callback processed',
            ]);

        } catch (\Exception $e) {
            Log::error('I&M Bank callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Callback processing failed',
            ], 500);
        }
    }

    /**
     * Handle result notification (alternative callback endpoint)
     */
    public function result(Request $request)
    {
        // Some gateways send results to a separate endpoint
        return $this->callback($request);
    }

    /**
     * Handle timeout notification
     */
    public function timeout(Request $request)
    {
        Log::info('I&M Bank timeout notification', $request->all());

        $data = $this->imbank->parseCallback($request->all());
        
        $payment = Payment::where('imbank_reference', $data['reference'])
            ->orWhere('imbank_transaction_id', $data['transaction_id'])
            ->first();

        if ($payment) {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => $data['raw_response'],
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Query payment status (for polling)
     */
    public function queryStatus(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
        ]);

        $payment = Payment::findOrFail($request->payment_id);

        try {
            $status = $this->imbank->queryPaymentStatus(
                $payment->imbank_transaction_id,
                $payment->imbank_reference
            );

            return response()->json([
                'status' => 'success',
                'data' => $status,
            ]);

        } catch (\Exception $e) {
            Log::error('I&M Bank status query failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Could not query payment status',
            ], 500);
        }
    }

    /**
     * Handle post-payment actions after successful payment
     */
    protected function handleSuccessfulPayment(Payment $payment)
    {
        // Update booking status
        if ($payment->bookable) {
            $booking = $payment->bookable;
            $booking->status = 'confirmed';
            $booking->confirmed_at = now();
            $booking->save();

            // Send confirmation emails/notifications
            // You can dispatch jobs here for email notifications
            // dispatch(new SendBookingConfirmation($booking));
        }

        // Additional logic for other payment types
        // (group sessions, clinic enrollments, etc.)
    }
}
