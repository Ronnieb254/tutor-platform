<?php

/**
 * Safaricom Daraja API – M-Pesa configuration.
 *
 * These values are already set in .env for both sandbox and production.
 *
 *   DARAJA_ENVIRONMENT=sandbox          # or production
 *   DARAJA_CONSUMER_KEY=xxx
 *   DARAJA_CONSUMER_SECRET=xxx
 *   DARAJA_SHORTCODE=174379             # sandbox default; your paybill/till in production
 *   DARAJA_PASSKEY=xxx
 *   DARAJA_CALLBACK_URL=https://yourdomain.com/api/daraja/callback
 *   DARAJA_INITIATOR_NAME=testapi       # for B2C / reversals (optional for STK Push)
 *   DARAJA_INITIATOR_PASSWORD=xxx
 *   DARAJA_PARTY_A=600988
 *   DARAJA_PARTY_B=600000
 *   DARAJA_TEST_PHONE=254708374149      # sandbox test phone
 */

return [
    'environment'       => env('DARAJA_ENVIRONMENT', 'sandbox'),
    'consumer_key'      => env('DARAJA_CONSUMER_KEY', ''),
    'consumer_secret'   => env('DARAJA_CONSUMER_SECRET', ''),
    'shortcode'         => env('DARAJA_SHORTCODE', '174379'),
    'passkey'           => env('DARAJA_PASSKEY', ''),
    'callback_url'      => env('DARAJA_CALLBACK_URL', ''),
    'initiator_name'    => env('DARAJA_INITIATOR_NAME', ''),
    'initiator_password'=> env('DARAJA_INITIATOR_PASSWORD', ''),
    'party_a'           => env('DARAJA_PARTY_A', ''),
    'party_b'           => env('DARAJA_PARTY_B', ''),
    'test_phone'        => env('DARAJA_TEST_PHONE', ''),
];
