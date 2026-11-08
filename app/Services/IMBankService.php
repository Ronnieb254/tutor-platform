<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * I&M Bank Payment API Gateway – drop-in replacement for DarajaService.
 *
 * All public methods keep the same signatures and return shapes as DarajaService
 * so every call-site (BookingController, ClinicBookingController, etc.) works
 * without any changes beyond swapping the injected service.
 *
 * Return shape from initializeStkPush() mirrors the old Daraja shape:
 *   ResponseCode           '0' = success, anything else = failure
 *   CheckoutRequestID      our internal reference (used for callback matching)
 *   MerchantRequestID      gateway transaction id
 *   CustomerMessage        message to show the user
 *   _mock                  set only in sandbox-placeholder mode
 *
 * Callback shape from parseCallback() mirrors the old Daraja shape:
 *   success                bool
 *   result_code            int|null
 *   result_desc            string
 *   checkout_request_id    string  (matches what we stored at initiation time)
 *   mpesa_receipt          string|null  (I&M transaction receipt)
 *   phone                  string|null
 *   amount                 float|null
 *   raw                    array
 */
class IMBankService
{
    protected string $apiKey;
    protected string $apiSecret;
    protected string $clientId;
    protected string $clientSecret;
    protected string $merchantId;
    protected string $paybillNumber;
    protected string $callbackUrl;
    protected string $environment;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey        = config('imbank.api_key', '');
        $this->apiSecret     = config('imbank.api_secret', '');
        $this->clientId      = config('imbank.client_id', '');
        $this->clientSecret  = config('imbank.client_secret', '');
        $this->merchantId    = config('imbank.merchant_id', '');
        $this->paybillNumber = config('imbank.paybill_number', '');
        $this->callbackUrl   = config('imbank.callback_url', '');
        $this->environment   = config('imbank.environment', 'sandbox');
        $this->baseUrl       = config("imbank.base_url.{$this->environment}", '');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PUBLIC API — same signatures as DarajaService
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Initiate a payment request (replaces initializeStkPush).
     *
     * Returns a Daraja-compatible array so every call-site works unchanged:
     *   ResponseCode, CheckoutRequestID, MerchantRequestID, CustomerMessage
     */
    public function initializeStkPush(
        string $phone,
        float  $amount,
        string $reference,
        string $description = 'Mwalimu Payment'
    ): array {
        // Sandbox / no-credentials mock
        if ($this->isSandboxPlaceholder()) {
            Log::info('IMBankService sandbox mode – returning mock response', [
                'reference' => $reference,
                'amount'    => $amount,
            ]);
            return [
                'ResponseCode'      => '0',
                'CheckoutRequestID' => $reference,
                'MerchantRequestID' => 'MOCK-' . strtoupper(Str::random(10)),
                'CustomerMessage'   => 'Payment request sent. Please check your phone.',
                '_mock'             => true,
            ];
        }

        try {
            $token = $this->getAccessToken();

            $payload = [
                'merchant_id'   => $this->merchantId,
                'paybill'       => $this->paybillNumber,
                'amount'        => round($amount, 2),
                'currency'      => 'KES',
                'phone_number'  => $this->normalizePhone($phone),
                'reference'     => $reference,
                'description'   => $description,
                'callback_url'  => $this->callbackUrl,
            ];

            Log::info('IMBankService: initiating STK push', [
                'reference' => $reference,
                'amount'    => $amount,
                'phone'     => substr($this->normalizePhone($phone), 0, 7) . 'XXX',
            ]);

            $response = Http::withToken($token)
                ->timeout(config('imbank.timeout.request', 45))
                ->retry(config('imbank.retry.attempts', 2), config('imbank.retry.delay', 2000))
                ->post("{$this->baseUrl}/v1/payments/mobile-money/stk-push", $payload);

            if (!$response->successful()) {
                Log::error('IMBankService: STK push HTTP error', [
                    'status'    => $response->status(),
                    'body'      => $response->body(),
                    'reference' => $reference,
                ]);
                return [
                    'errorCode'    => (string) $response->status(),
                    'errorMessage' => 'I&M Gateway error: ' . $response->status(),
                ];
            }

            $data = $response->json();

            Log::info('IMBankService: STK push response', [
                'reference' => $reference,
                'response'  => $data,
            ]);

            // Map I&M response → Daraja-compatible shape
            $success = in_array(strtolower($data['status'] ?? ''), ['success', 'pending', 'initiated', '0']);

            return [
                'ResponseCode'      => $success ? '0' : '1',
                'CheckoutRequestID' => $data['checkout_id'] ?? $data['transaction_id'] ?? $reference,
                'MerchantRequestID' => $data['transaction_id'] ?? $data['merchant_ref'] ?? null,
                'CustomerMessage'   => $data['message'] ?? 'Payment request sent. Please check your phone.',
                'ResponseDescription' => $data['description'] ?? '',
                '_imbank_raw'       => $data,
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('IMBankService: connection failed', [
                'reference' => $reference,
                'error'     => $e->getMessage(),
            ]);
            return [
                'errorCode'    => 'CONN_ERR',
                'errorMessage' => 'Could not connect to I&M Bank: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Parse an inbound I&M Bank payment callback.
     * Returns same shape as DarajaService::parseCallback() so
     * DarajaCallbackController works without changes.
     */
    public function parseCallback(array $payload): array
    {
        // I&M Bank POST body shape (adjust field names once you have their docs):
        //   status          : 'success' | 'failed' | 'cancelled'
        //   result_code     : 0 = success
        //   transaction_id  : their transaction reference
        //   checkout_id     : the reference we sent at initiation time
        //   receipt_number  : receipt / M-Pesa-style ID
        //   phone_number    : payer's phone
        //   amount          : confirmed amount
        //   description     : result message

        $status  = strtolower($payload['status']      ?? '');
        $code    = $payload['result_code']             ?? ($status === 'success' ? 0 : 1);
        $ckId    = $payload['checkout_id']             ?? $payload['reference'] ?? $payload['merchant_reference'] ?? null;
        $txId    = $payload['transaction_id']          ?? $payload['trans_id']  ?? null;
        $receipt = $payload['receipt_number']          ?? $payload['receipt']   ?? $txId;
        $phone   = $payload['phone_number']            ?? $payload['msisdn']    ?? null;
        $amount  = isset($payload['amount']) ? (float) $payload['amount'] : null;
        $desc    = $payload['description']             ?? $payload['message']   ?? '';

        return [
            'success'             => (int) $code === 0 || $status === 'success',
            'result_code'         => (int) $code,
            'result_desc'         => $desc,
            'merchant_request_id' => $txId,
            'checkout_request_id' => $ckId,   // ← this is what Payment::where('checkout_request_id') matches
            'mpesa_receipt'       => $receipt, // ← stored as mpesa_receipt in payments table
            'phone'               => $phone,
            'amount'              => $amount,
            'raw'                 => $payload,
        ];
    }

    /**
     * Query payment status by checkout ID.
     * Mirrors DarajaService::queryStkStatus() signature.
     */
    public function queryStkStatus(string $checkoutRequestId): array
    {
        if ($this->isSandboxPlaceholder()) {
            return ['status' => 'pending', 'checkout_request_id' => $checkoutRequestId];
        }

        try {
            $token = $this->getAccessToken();

            $response = Http::withToken($token)
                ->timeout(15)
                ->get("{$this->baseUrl}/v1/payments/status/{$checkoutRequestId}");

            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::error('IMBankService: status query failed', [
                'checkout_id' => $checkoutRequestId,
                'error'       => $e->getMessage(),
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Generate a unique payment reference.
     * Same signature as DarajaService::generateReference().
     */
    public function generateReference(string $prefix = 'TL'): string
    {
        return $prefix . '-' . strtoupper(Str::random(12)) . '-' . time();
    }

    /**
     * Normalise a Kenyan phone number to 254XXXXXXXXX.
     * Same signature and logic as DarajaService::normalizePhone().
     */
    public function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '254')) {
            $phone = '254' . $phone;
        }

        return $phone;
    }

    /**
     * True when no real credentials are configured.
     */
    public function isSandboxPlaceholder(): bool
    {
        return empty($this->apiKey)
            || empty($this->clientId)
            || str_contains(strtolower($this->apiKey), 'replace')
            || str_contains(strtolower($this->apiKey), 'xxxx');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INTERNAL
    // ─────────────────────────────────────────────────────────────────────────

    protected function getAccessToken(): string
    {
        $cacheKey = 'imbank_access_token';

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->timeout(30)
            ->post("{$this->baseUrl}/oauth/token", [
                'grant_type' => 'client_credentials',
                'scope'      => 'payment',
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'I&M Bank auth failed: HTTP ' . $response->status() . ' – ' . $response->body()
            );
        }

        $data = $response->json();

        if (empty($data['access_token'])) {
            throw new \RuntimeException('I&M Bank auth: no access_token in response');
        }

        $ttl = ($data['expires_in'] ?? 3600) - 300;
        Cache::put($cacheKey, $data['access_token'], now()->addSeconds($ttl));

        return $data['access_token'];
    }
}
