<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaystackService
{
    protected string $secretKey;
    protected string $publicKey;
    protected string $baseUrl;
    protected string $currency;

    public function __construct()
    {
        $this->secretKey = config('paystack.secret_key');
        $this->publicKey = config('paystack.public_key');
        $this->baseUrl   = config('paystack.payment_url', 'https://api.paystack.co');
        $this->currency  = config('paystack.currency', 'KES');
    }

    public function initializeTransaction(string $email, float $amount, string $reference, array $metadata = []): array
    {
        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/transaction/initialize", [
                'email'     => $email,
                'amount'    => (int) round($amount * 100), // convert to cents
                'reference' => $reference,
                'currency'  => $this->currency,
                'metadata'  => $metadata,
                'callback_url' => route('paystack.callback'),
            ]);

        return $response->json();
    }

    public function verifyTransaction(string $reference): array
    {
        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/transaction/verify/{$reference}");

        return $response->json();
    }

    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $computed = hash_hmac('sha512', $payload, $this->secretKey);
        return hash_equals($computed, $signature);
    }

    public function generateReference(string $prefix = 'TL'): string
    {
        return $prefix . '-' . strtoupper(Str::random(12)) . '-' . time();
    }
}
