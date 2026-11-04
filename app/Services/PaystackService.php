<?php

namespace App\Services;

/**
 * @deprecated  Paystack has been removed. Use DarajaService for all payments.
 *              This stub exists only to prevent "class not found" errors if any
 *              service-container bindings still reference it.
 *
 * @see DarajaService
 */
class PaystackService
{
    public function __construct()
    {
        // No-op stub — Paystack has been removed.
    }

    public function initializeTransaction(): array { return []; }
    public function verifyTransaction(): array { return []; }
    public function verifyWebhookSignature(): bool { return false; }
    public function generateReference(string $prefix = 'TL'): string
    {
        return $prefix . '-' . strtoupper(\Illuminate\Support\Str::random(12)) . '-' . time();
    }
}
