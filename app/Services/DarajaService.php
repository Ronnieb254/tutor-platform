<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Safaricom Daraja API – M-Pesa STK Push (Lipa Na M-Pesa Online).
 *
 * Flow:
 *  1. initializeStkPush()   → sends an STK Push prompt to the customer's phone.
 *  2. Customer enters PIN   → Safaricom calls our callback URL.
 *  3. handleCallback()      → verifies and marks the payment success/failed.
 *  4. queryStkStatus()      → optional polling to check status of a push.
 */
class DarajaService
{
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $shortcode;        // Business Short Code (till / paybill)
    protected string $passkey;          // Lipa Na M-Pesa Online Passkey
    protected string $callbackUrl;
    protected string $baseUrl;
    protected string $environment;     // 'sandbox' | 'production'

    public function __construct()
    {
        $this->consumerKey    = config('daraja.consumer_key', '');
        $this->consumerSecret = config('daraja.consumer_secret', '');
        $this->shortcode      = config('daraja.shortcode', '');
        $this->passkey        = config('daraja.passkey', '');
        $this->callbackUrl    = config('daraja.callback_url', '');
        $this->environment    = config('daraja.environment', 'sandbox');
        $this->baseUrl        = $this->environment === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AUTH
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Obtain a short-lived OAuth access token from Daraja.
     */
    public function getAccessToken(): string
    {
        if ($this->isSandboxPlaceholder()) {
            return 'SANDBOX_TOKEN_' . Str::random(20);
        }

        try {
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->timeout(30)
                ->retry(3, 1000) // Retry 3 times with 1 second between attempts
                ->get("{$this->baseUrl}/oauth/v1/generate", ['grant_type' => 'client_credentials']);

            if (!$response->successful()) {
                throw new \RuntimeException(
                    'Daraja auth request failed: HTTP ' . $response->status() . ' - ' . $response->body()
                );
            }

            $data = $response->json();

            if (!isset($data['access_token'])) {
                Log::error('Daraja: failed to get access token', ['response' => $data]);
                throw new \RuntimeException('Daraja authentication failed: ' . ($data['errorMessage'] ?? 'unknown error'));
            }

            return $data['access_token'];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Daraja: connection failed', [
                'error' => $e->getMessage(),
                'url' => "{$this->baseUrl}/oauth/v1/generate",
            ]);
            throw new \RuntimeException(
                'Cannot connect to M-Pesa payment gateway. Please check your internet connection or contact support.',
                0,
                $e
            );
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STK PUSH
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Trigger an STK Push to the customer's phone.
     *
     * @param  string $phone     Customer phone in international format: 254XXXXXXXXX
     * @param  float  $amount    Amount in KES (integer-rounded)
     * @param  string $reference Our internal reference (AccountReference, max 12 chars)
     * @param  string $desc      Short description shown on the customer's phone
     * @return array             Daraja API response
     */
    public function initializeStkPush(
        string $phone,
        float  $amount,
        string $reference,
        string $desc = 'Mwalimu Session'
    ): array {
        if ($this->isSandboxPlaceholder()) {
            Log::info('Daraja sandbox mode – returning mock STK Push response');
            return [
                'ResponseCode'       => '0',
                'ResponseDescription' => 'Success. Request accepted for processing',
                'MerchantRequestID'  => 'MOCK-' . Str::random(8),
                'CheckoutRequestID'  => 'ws_CO_MOCK_' . Str::random(16),
                'CustomerMessage'    => 'Success. Request accepted for processing',
                '_mock'              => true,
            ];
        }

        try {
            $token     = $this->getAccessToken();
            $timestamp = now()->format('YmdHis');
            $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);

            $payload = [
                'BusinessShortCode' => $this->shortcode,
                'Password'          => $password,
                'Timestamp'         => $timestamp,
                'TransactionType'   => 'CustomerPayBillOnline',
                'Amount'            => (int) ceil($amount),
                'PartyA'            => $this->normalizePhone($phone),
                'PartyB'            => $this->shortcode,
                'PhoneNumber'       => $this->normalizePhone($phone),
                'CallBackURL'       => $this->callbackUrl,
                'AccountReference'  => substr($reference, 0, 12),
                'TransactionDesc'   => substr($desc, 0, 13),
            ];

            $response = Http::withToken($token)
                ->timeout(45)
                ->retry(2, 2000) // Retry twice with 2 second delay
                ->post("{$this->baseUrl}/mpesa/stkpush/v1/processrequest", $payload);

            if (!$response->successful()) {
                throw new \RuntimeException(
                    'Daraja STK Push request failed: HTTP ' . $response->status() . ' - ' . $response->body()
                );
            }

            $data = $response->json();

            Log::info('Daraja STK Push response', [
                'reference' => $reference,
                'phone'     => $this->normalizePhone($phone),
                'amount'    => (int) ceil($amount),
                'response'  => $data,
            ]);

            return $data;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Daraja: STK Push connection failed', [
                'error' => $e->getMessage(),
                'reference' => $reference,
            ]);
            throw new \RuntimeException(
                'Cannot connect to M-Pesa payment gateway. Please try again or contact support.',
                0,
                $e
            );
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // QUERY STK STATUS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Query the status of an STK Push transaction.
     *
     * @param  string $checkoutRequestId  The CheckoutRequestID from initializeStkPush()
     */
    public function queryStkStatus(string $checkoutRequestId): array
    {
        if ($this->isSandboxPlaceholder()) {
            return [
                'ResponseCode'       => '0',
                'ResultCode'         => '0',
                'ResultDesc'         => 'The service request is processed successfully.',
                '_mock'              => true,
            ];
        }

        $token     = $this->getAccessToken();
        $timestamp = now()->format('YmdHis');
        $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $response = Http::withToken($token)
            ->timeout(15)
            ->post("{$this->baseUrl}/mpesa/stkpushquery/v1/query", [
                'BusinessShortCode' => $this->shortcode,
                'Password'          => $password,
                'Timestamp'         => $timestamp,
                'CheckoutRequestID' => $checkoutRequestId,
            ]);

        return $response->json();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CALLBACK VERIFICATION
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Parse and validate the Daraja STK Push callback body.
     *
     * Returns an array with keys:
     *   success            bool
     *   result_code        int|null
     *   result_desc        string
     *   merchant_request_id string|null
     *   checkout_request_id string|null
     *   mpesa_receipt       string|null   (M-Pesa transaction ID)
     *   phone              string|null
     *   amount             float|null
     *   raw                array          (original payload)
     */
    public function parseCallback(array $payload): array
    {
        $body   = $payload['Body']                       ?? [];
        $stkCb  = $body['stkCallback']                  ?? [];
        $code   = $stkCb['ResultCode']                  ?? null;
        $desc   = $stkCb['ResultDesc']                  ?? 'Unknown';
        $mId    = $stkCb['MerchantRequestID']           ?? null;
        $ckId   = $stkCb['CheckoutRequestID']           ?? null;
        $items  = $stkCb['CallbackMetadata']['Item']    ?? [];

        // Extract metadata items
        $meta = [];
        foreach ($items as $item) {
            $meta[$item['Name']] = $item['Value'] ?? null;
        }

        return [
            'success'             => (int) $code === 0,
            'result_code'         => $code !== null ? (int) $code : null,
            'result_desc'         => $desc,
            'merchant_request_id' => $mId,
            'checkout_request_id' => $ckId,
            'mpesa_receipt'       => $meta['MpesaReceiptNumber'] ?? null,
            'phone'               => isset($meta['PhoneNumber']) ? (string) $meta['PhoneNumber'] : null,
            'amount'              => isset($meta['Amount'])       ? (float) $meta['Amount']       : null,
            'raw'                 => $payload,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Generate a unique payment reference string.
     * Kept with the same signature as the old PaystackService method
     * so all call-sites work without change.
     */
    public function generateReference(string $prefix = 'TL'): string
    {
        return $prefix . '-' . strtoupper(Str::random(12)) . '-' . time();
    }

    /**
     * Normalise a Kenyan phone number to 254XXXXXXXXX format.
     * Accepts: 07XXXXXXXX, +254XXXXXXXXX, 254XXXXXXXXX, 01XXXXXXXX
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
     * True when no real credentials have been configured yet (placeholder values).
     * With real Daraja sandbox credentials the service will make real API calls
     * to the Safaricom sandbox — this is correct behaviour.
     */
    public function isSandboxPlaceholder(): bool
    {
        return empty($this->consumerKey)
            || empty($this->shortcode)
            || empty($this->passkey)
            || str_contains(strtolower($this->consumerKey), 'replace')
            || str_contains($this->consumerKey, 'xxxx');
    }
}
