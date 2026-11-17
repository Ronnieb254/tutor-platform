<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Handles the Paystack Standard (redirect) payment flow for the three
 * payment types: 1-on-1 bookings, clinics, and group sessions.
 *
 *  - initiate(): AJAX endpoint called after a Payment record exists; returns
 *    the Paystack authorization_url the browser should redirect to.
 *  - callback(): Paystack redirects the browser here after payment; we verify
 *    server-side and confirm the booking/enrollment.
 *  - webhook():  server-to-server reliability backstop; verifies the payload
 *    signature and confirms the record if the browser never returned.
 *
 * Actual booking/enrollment confirmation is delegated to
 * @see PaystackCallbackHelper
 */
class PaystackPaymentController extends Controller
{
    public function __construct(
        protected PaystackService $paystack
    ) {}

    /**
     * Initialize a Paystack transaction for an existing pending Payment.
     *
     * Expects: { payment_id }
     * Returns: { status, authorization_url, access_code, reference }
     */
    public function initiate(Request $request)
    {
        $request->validate(['payment_id' => 'required|integer']);

        $payment = Payment::where('id', $request->payment_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$payment) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Payment not found.',
            ], 404);
        }

        if ($payment->status === 'success') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Payment already completed.',
            ], 422);
        }

        $user      = $payment->user ?? $request->user();
        $reference = $payment->mpesa_reference;

        $result = $this->paystack->initializeTransaction(
            $user->email,
            (int) round((float) $payment->amount * 100),
            $reference,
            array_merge((array) ($payment->metadata ?? []), [
                'flow'       => 'paystack',
                'payment_id' => $payment->id,
            ]),
            route('paystack.callback')
        );

        if (!($result['status'] ?? false) || empty($result['data']['authorization_url'])) {
            // Log the full Paystack response for server-side debugging.
            \Log::error('Paystack initializeTransaction failed in initiate()', [
                'payment_id' => $payment->id,
                'reference'  => $payment->mpesa_reference,
                'amount_kobo'=> (int) round((float) $payment->amount * 100),
                'email'      => $user->email,
                'result'     => $result,
                'secret_key_set' => !empty(config('paystack.secret_key')),
            ]);

            $errorMsg = $result['message'] ?? 'Could not initialize payment.';
            // Expose Paystack's exact message in non-production for easier debugging.
            if (config('app.debug')) {
                $errorMsg .= ' [Paystack: ' . ($result['message'] ?? 'no message') . ']';
            }

            return response()->json([
                'status'  => 'error',
                'message' => $errorMsg,
            ], 422);
        }

        $data = $result['data'];

        // Persist the access_code / authorization_url for traceability.
        $metadata = (array) ($payment->metadata ?? []);
        $metadata['flow']              = 'paystack';
        $metadata['access_code']       = $data['access_code'] ?? null;
        $metadata['authorization_url'] = $data['authorization_url'] ?? null;
        $payment->metadata = $metadata;
        $payment->save();

        return response()->json([
            'status'            => 'ok',
            'authorization_url' => $data['authorization_url'],
            'access_code'       => $data['access_code'] ?? null,
            'reference'         => $reference,
        ]);
    }

    /**
     * Paystack redirects the customer's browser here after payment.
     * We verify the transaction server-side, then confirm the related record.
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference') ?? $request->query('trxref') ?? $request->input('reference');

        if (!$reference) {
            return redirect()->route('dashboard')->with('error', 'Payment reference missing.');
        }

        $payment = Payment::where('mpesa_reference', $reference)->first();

        if (!$payment) {
            return redirect()->route('dashboard')
                ->with('error', 'Payment not found. Please contact support.');
        }

        // The webhook may have confirmed before the browser arrived.
        if ($payment->status === 'success') {
            return PaystackCallbackHelper::redirectAfterPayment(
                $payment,
                'Payment confirmed! Your session is now active.'
            );
        }

        $result = $this->paystack->verifyTransaction($reference);

        $verified = ($result['status'] ?? false)
            && (($result['data']['status'] ?? '') === 'success');

        if ($verified) {
            $data     = $result['data'] ?? [];
            $metadata = (array) ($payment->metadata ?? []);
            $metadata['flow']            = 'paystack';
            $metadata['gateway_response'] = $data['gateway_response'] ?? null;
            $metadata['channel']          = $data['channel'] ?? null;

            $payment->status        = 'success';
            $payment->paid_at       = now();
            $payment->mpesa_receipt = $data['reference'] ?? $reference;
            $payment->metadata      = $metadata;
            $payment->save();

            return PaystackCallbackHelper::confirmAndRedirect($payment);
        }

        // Verification failed — mark the payment and notify the customer.
        $payment->status         = 'failed';
        $payment->failure_reason = ($result['data']['gateway_response'] ?? null)
            ?: ($result['message'] ?? 'Verification failed');
        $payment->save();

        return redirect()->route('dashboard')
            ->with('error', 'Payment could not be verified. If you were charged, please contact support with reference: ' . $reference);
    }

    /**
     * Paystack server-to-server webhook.
     * Verifies the signature and confirms the record if not already done.
     */
    public function webhook(Request $request)
    {
        $signature = $request->header('Paystack-Signature');
        $payload   = $request->getContent();

        if (!$this->paystack->verifyWebhookSignature($payload, $signature ?? '')) {
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
        }

        $event = json_decode($payload, true);

        // Only charge.success needs confirmation handling.
        if (!is_array($event) || ($event['event'] ?? '') !== 'charge.success') {
            return response()->json(['status' => 'ok']);
        }

        $data     = $event['data'] ?? [];
        $reference = $data['reference'] ?? '';

        if (!$reference) {
            return response()->json(['status' => 'ok']);
        }

        $payment = Payment::where('mpesa_reference', $reference)->first();

        if (!$payment) {
            return response()->json(['status' => 'ok']);
        }

        // Already confirmed (e.g. the browser callback finished first).
        if ($payment->status === 'success') {
            return response()->json(['status' => 'ok']);
        }

        $metadata = (array) ($payment->metadata ?? []);
        $metadata['flow']            = 'paystack';
        $metadata['gateway_response'] = $data['gateway_response'] ?? null;
        $metadata['channel']          = $data['channel'] ?? null;

        $payment->status        = 'success';
        $payment->paid_at       = now();
        $payment->mpesa_receipt = $data['reference'] ?? $reference;
        $payment->metadata      = $metadata;
        $payment->save();

        // PaystackCallbackHelper relies on auth()->user(); there is no session
        // in a webhook, so set the payment's owner as the current user for the
        // duration of this request only (setUser does not touch the session).
        $owner = $payment->user;
        if ($owner && !auth()->user()) {
            auth()->setUser($owner);
        }

        try {
            PaystackCallbackHelper::confirmAndRedirect($payment);
        } catch (\Throwable $e) {
            Log::error('Paystack webhook confirmation failed: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'reference'  => $reference,
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}
