<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IntaSendService
{
    protected string $publishableKey;
    protected string $secretKey;
    protected string $environment;
    protected string $baseUrl;

    public function __construct()
    {
        $this->publishableKey = env('INTASEND_PUBLISHABLE_KEY', '');
        $this->secretKey      = env('INTASEND_SECRET_KEY', '');
        $this->environment    = env('INTASEND_ENVIRONMENT', 'sandbox');

        $this->baseUrl = $this->environment === 'production'
            ? 'https://api.intasend.com'
            : 'https://sandbox.intasend.com';
    }

    /**
     * Create an IntaSend hosted checkout session.
     *
     * @param  float   $amount       Amount in KES
     * @param  User    $user         Paying user
     * @param  string  $reference    Unique payment reference
     * @param  string  $redirectUrl  URL to redirect to after payment
     * @return array{url: string, id: string}
     * @throws \RuntimeException on failure
     */
    public function createCheckout(float $amount, User $user, string $reference, string $redirectUrl): array
    {
        $nameParts  = explode(' ', $user->name, 2);
        $firstName  = $nameParts[0];
        $lastName   = $nameParts[1] ?? '';
        $webhookUrl = rtrim(env('APP_URL', ''), '/') . '/api/intasend/webhook';

        $payload = [
            'public_key'   => $this->publishableKey,
            'currency'     => 'KES',
            'amount'       => (string) round($amount, 2),
            'email'        => $user->email,
            'first_name'   => $firstName,
            'last_name'    => $lastName,
            'api_ref'      => $reference,
            'redirect_url' => $redirectUrl,
            'webhook_url'  => $webhookUrl,
        ];

        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/api/v1/checkout/", $payload);

        Log::info('IntaSend checkout response', [
            'status' => $response->status(),
            'body'   => $response->json(),
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'IntaSend checkout failed: ' . ($response->json('detail') ?? $response->body())
            );
        }

        $data = $response->json();

        return [
            'url' => $data['url'] ?? ($data['checkout_url'] ?? ''),
            'id'  => $data['id'] ?? ($data['invoice_id'] ?? $reference),
        ];
    }

    /**
     * Verify webhook signature using the INTASEND_SECRET_KEY.
     * IntaSend signs the raw request body with HMAC-SHA256.
     */
    public function verifyWebhook(Request $request): bool
    {
        $signature = $request->header('X-IntaSend-Signature');

        if (!$signature || !$this->secretKey) {
            // In sandbox with no key configured, allow all
            return empty($this->secretKey) || $this->environment === 'sandbox';
        }

        $expected = hash_hmac('sha256', $request->getContent(), $this->secretKey);

        return hash_equals($expected, $signature);
    }

    /**
     * Parse an IntaSend webhook payload into a standardised array.
     *
     * @return array{status: string, reference: string, amount: float|null, method: string|null, receipt: string|null}
     */
    public function parseWebhook(array $payload): array
    {
        // Map IntaSend state/status to our canonical values
        $rawStatus = strtolower($payload['state'] ?? $payload['status'] ?? '');
        $status = match ($rawStatus) {
            'complete', 'completed', 'success', 'successful' => 'success',
            'failed', 'cancelled', 'reversed'               => 'failed',
            default                                          => 'pending',
        };

        // Reference: prefer api_ref, fall back to invoice_id
        $reference = $payload['api_ref'] ?? $payload['invoice_id'] ?? null;

        return [
            'status'    => $status,
            'reference' => $reference,
            'amount'    => isset($payload['value']) ? (float) $payload['value'] : null,
            'method'    => $payload['provider'] ?? $payload['payment_method'] ?? null,
            'receipt'   => $payload['receipt_number'] ?? $payload['mpesa_reference'] ?? null,
        ];
    }
}
