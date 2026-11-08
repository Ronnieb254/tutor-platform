<?php

return [

    /*
    |--------------------------------------------------------------------------
    | I&M Bank Payment API Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for I&M Bank Payment API Gateway integration.
    | Contact I&M Bank Business Solutions for API credentials and documentation.
    | Email: customercare@imbank.co.ke | Phone: 0719 088 000, 0732 100 000
    |
    */

    // Environment: 'sandbox' or 'production'
    'environment' => env('IMBANK_ENVIRONMENT', 'sandbox'),

    // API Credentials (obtain from I&M Bank)
    'api_key' => env('IMBANK_API_KEY', ''),
    'api_secret' => env('IMBANK_API_SECRET', ''),
    'client_id' => env('IMBANK_CLIENT_ID', ''),
    'client_secret' => env('IMBANK_CLIENT_SECRET', ''),

    // Merchant/Business Details
    'merchant_id' => env('IMBANK_MERCHANT_ID', ''),
    'till_number' => env('IMBANK_TILL_NUMBER', ''),
    'paybill_number' => env('IMBANK_PAYBILL_NUMBER', ''),

    // API Endpoints (will be provided by I&M Bank)
    'base_url' => [
        'sandbox' => env('IMBANK_SANDBOX_URL', 'https://sandbox-api.imbankgroup.com'),
        'production' => env('IMBANK_PRODUCTION_URL', 'https://api.imbankgroup.com'),
    ],

    // Callback URLs
    'callback_url' => env('IMBANK_CALLBACK_URL', env('APP_URL') . '/api/imbank/callback'),
    'result_url' => env('IMBANK_RESULT_URL', env('APP_URL') . '/api/imbank/result'),
    'timeout_url' => env('IMBANK_TIMEOUT_URL', env('APP_URL') . '/api/imbank/timeout'),

    // Payment Options
    'payment_methods' => [
        'mpesa' => true,           // M-Pesa via I&M Gateway
        'airtel_money' => true,    // Airtel Money
        'card' => true,            // Visa/Mastercard
        'pesalink' => true,        // PesaLink
        'rtgs' => false,           // Real Time Gross Settlement (for large amounts)
        'eft' => false,            // Electronic Funds Transfer
    ],

    // Transaction limits
    'limits' => [
        'min_amount' => env('IMBANK_MIN_AMOUNT', 10),
        'max_amount' => env('IMBANK_MAX_AMOUNT', 500000),
    ],

    // Timeout settings (seconds)
    'timeout' => [
        'connection' => 30,
        'request' => 45,
    ],

    // Retry settings
    'retry' => [
        'attempts' => 2,
        'delay' => 2000, // milliseconds
    ],

    // Logging
    'logging' => [
        'enabled' => env('IMBANK_LOGGING_ENABLED', true),
        'channel' => env('IMBANK_LOG_CHANNEL', 'daily'),
    ],

];
