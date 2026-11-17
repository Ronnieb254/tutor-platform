<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Paystack payment gateway integration (Standard / redirect flow).
 *
 * Flow:
 *  1. initializeTransaction() → returns an authorization_url the user is sent to.
 *  2. User pays on Paystack's hosted page.
 *  3. Paystack redirects back to our callback_url and/or POSTs a webhook.
 *  4. verifyTransaction() → confirms the transaction status server-side.
 *
 * Credentials live in config/paystack.php (reads PAYSTACK_* env keys).
 */
class PaystackService
{
    protected string $secretKey;
    protected string $paymentUrl;
    protected string $currency;

    public function __construct()
    {
        $this->secretKey  = config('paystack.secret_key', '');
        $this->paymentUrl = rtrim(config('paystack.payment_url', 'https://api.paystack.co'), '/');
        $this->currency   = config('paystack.currency', 'KES');
    }

    /**
     * Initialize a Paystack transaction.
     *
     * @param  string       $email        Customer email address
     * @param  int          $amountKobo   Amount in the smallest currency unit (e.g. kobo/cents)
     * @param  string       $reference    Unique transaction reference
     * @param  array        $metadata     Additional metadata to attach to the transaction
     * @param  string|null  $callbackUrl  URL Paystack redirects to after payment
     * @return array        Paystack API response
     */
    public function initializeTransaction(
        string $email,
        int $amountKobo,
        string $reference,
        array $metadata = [],
        ?string $callbackUrl = null
    ): array {
        try {
            $response = Http::withToken($this->secretKey)
                ->timeout(30)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->paymentUrl}/transaction/initialize", [
                    'email'        => $email,
                    'amount'       => $amountKobo,
                    'reference'    => $reference,
                    'currency'     => $this->currency,
                    'metadata'     => $metadata,
                    'callback_url' => $callbackUrl,
                ]);

            if (!$response->successful()) {
                Log::error('Paystack initializeTransaction failed', [
                    'status'    => $response->status(),
                    'body'      => $response->body(),
                    'reference' => $reference,
                ]);

                return [
                    'status'  => false,
                    'message' => 'Paystack initialization failed.',
                ];
            }

            return $response->json();
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Paystack: connection failed', [
                'error'     => $e->getMessage(),
                'reference' => $reference,
            ]);

            return [
                'status'  => false,
                'message' => 'Cannot connect to Paystack. Please try again.',
            ];
        }
    }

    /**
     * Verify a transaction by its reference.
     *
     * @param  string  $reference  The transaction reference to verify
     * @return array   Paystack API response (includes the `data` block)
     */
    public function verifyTransaction(string $reference): array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->timeout(30)
                ->get("{$this->paymentUrl}/transaction/verify/{$reference}");

            if (!$response->successful()) {
                Log::error('Paystack verifyTransaction failed', [
                    'status'    => $response->status(),
                    'body'      => $response->body(),
                    'reference' => $reference,
                ]);

                return [
                    'status'  => false,
                    'message' => 'Paystack verification failed.',
                ];
            }

            return $response->json();
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Paystack: verification connection failed', [
                'error'     => $e->getMessage(),
                'reference' => $reference,
            ]);

            return [
                'status'  => false,
                'message' => 'Cannot connect to Paystack to verify payment.',
            ];
        }
    }

    /**
     * Verify the signature of an incoming Paystack webhook payload.
     *
     * @param  string  $payload    The raw request body
     * @param  string  $signature  The Paystack-Signature header value
     * @return bool
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        if (empty($this->secretKey) || empty($signature)) {
            return false;
        }

        $computed = hash_hmac('sha512', $payload, $this->secretKey);

        return hash_equals($computed, $signature);
    }

    /**
     * Generate a unique transaction reference.
     *
     * @param  string  $prefix
     * @return string
     */
    public function generateReference(string $prefix = 'PS'): string
    {
        return $prefix . '-' . strtoupper(Str::random(12)) . '-' . time();
    }
}
