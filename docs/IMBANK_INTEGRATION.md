# I&M Bank Payment API Gateway Integration

This document describes the integration of I&M Bank Payment API Gateway to replace Safaricom Daraja for payment processing.

## Overview

I&M Bank Payment API Gateway provides a unified interface for multiple payment methods:
- M-Pesa (via I&M Gateway)
- Airtel Money
- Visa/Mastercard payments
- PesaLink transfers
- Bank transfers (EFT, RTGS)

## Features

✅ **Multiple Payment Methods**: Support for mobile money, cards, and bank transfers  
✅ **Secure Processing**: Two-factor authentication and webhook signature verification  
✅ **Real-time Status**: Instant payment notifications via callbacks  
✅ **Auto-reversals**: Automatic reversal handling for failed transactions  
✅ **Transaction Tracking**: Complete audit trail with transaction IDs and receipts

## Setup Instructions

### 1. Contact I&M Bank

To get started, contact I&M Bank Business Solutions:
- **Email**: customercare@imbank.co.ke
- **Phone**: 0719 088 000, 0732 100 000
- **Website**: https://www.imbankgroup.com/ke/business-solutions/paymentapigateway/

Request the following:
1. API credentials (API Key, API Secret, Client ID, Client Secret)
2. Merchant ID
3. API documentation with endpoints
4. Sandbox environment access for testing
5. Webhook signature verification method (if available)

### 2. Configure Environment Variables

Add your I&M Bank credentials to `.env`:

```env
IMBANK_ENVIRONMENT=sandbox
IMBANK_API_KEY=your_api_key_here
IMBANK_API_SECRET=your_api_secret_here
IMBANK_CLIENT_ID=your_client_id_here
IMBANK_CLIENT_SECRET=your_client_secret_here
IMBANK_MERCHANT_ID=your_merchant_id_here
IMBANK_PAYBILL_NUMBER=your_paybill_number
```

### 3. Run Database Migration

```bash
php artisan migrate
```

This adds the following fields to the `payments` table:
- `payment_gateway` - Identifies the gateway used (imbank/daraja)
- `imbank_transaction_id` - I&M Bank transaction ID
- `imbank_reference` - Internal payment reference
- `imbank_receipt_number` - Receipt number for successful payments
- `payment_method` - Specific method used (mpesa, card, etc.)
- `gateway_response` - Raw API response for debugging

### 4. Update Payment Logic

Replace Daraja payment calls with I&M Bank service:

**Before (Daraja):**
```php
use App\Services\DarajaService;

$daraja = app(DarajaService::class);
$response = $daraja->initializeStkPush($amount, $phone, $reference, $description);
```

**After (I&M Bank):**
```php
use App\Services\IMBankService;

$imbank = app(IMBankService::class);
$response = $imbank->initiatePayment(
    'mpesa',        // Payment method
    $amount,        // Amount in KSh
    $phone,         // Customer phone
    $reference,     // Internal reference
    $description,   // Payment description
    []              // Additional metadata
);
```

### 5. Configure Webhook URLs

Provide these callback URLs to I&M Bank for their configuration:

```
Production:
- Callback URL: https://yourdomain.com/api/imbank/callback
- Result URL: https://yourdomain.com/api/imbank/result
- Timeout URL: https://yourdomain.com/api/imbank/timeout

Sandbox:
- Callback URL: https://test.yourdomain.com/api/imbank/callback
- Result URL: https://test.yourdomain.com/api/imbank/result
- Timeout URL: https://test.yourdomain.com/api/imbank/timeout
```

## Usage Examples

### Initiating M-Pesa Payment

```php
use App\Services\IMBankService;

$imbank = app(IMBankService::class);

try {
    $response = $imbank->initiatePayment(
        method: 'mpesa',
        amount: 600.00,
        phone: '254712345678',
        reference: 'BKG-' . $booking->id,
        description: 'Tutoring session payment',
        metadata: [
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
        ]
    );

    // Store transaction details
    $payment->imbank_transaction_id = $response['transaction_id'];
    $payment->imbank_reference = $reference;
    $payment->payment_gateway = 'imbank';
    $payment->payment_method = 'mpesa';
    $payment->save();

    return response()->json([
        'status' => 'success',
        'message' => 'Payment initiated. Please check your phone.',
        'transaction_id' => $response['transaction_id'],
    ]);

} catch (\Exception $e) {
    Log::error('Payment initiation failed', ['error' => $e->getMessage()]);
    
    return response()->json([
        'status' => 'error',
        'message' => 'Payment initiation failed',
    ], 500);
}
```

### Initiating Card Payment

```php
$response = $imbank->initiatePayment(
    method: 'card',
    amount: 1200.00,
    phone: '',  // Not required for card payments
    reference: 'BKG-' . $booking->id,
    description: 'Online tutoring payment',
    metadata: ['booking_id' => $booking->id]
);

// Redirect user to payment page
if (isset($response['payment_url'])) {
    return redirect($response['payment_url']);
}
```

### Querying Payment Status

```php
$status = $imbank->queryPaymentStatus(
    $payment->imbank_transaction_id,
    $payment->imbank_reference
);

if ($status['status'] === 'completed') {
    $payment->update([
        'status' => 'completed',
        'paid_at' => now(),
    ]);
}
```

### Handling Callbacks

Callbacks are automatically handled by `IMBankPaymentController`. The controller:
1. Validates webhook signature (if provided)
2. Parses the callback data
3. Updates payment status in database
4. Triggers post-payment actions (booking confirmation, emails, etc.)

## Payment Methods

### 1. M-Pesa (via I&M Gateway)

```php
$imbank->initiatePayment('mpesa', $amount, $phone, $reference, $description);
```

### 2. Airtel Money

```php
$imbank->initiatePayment('airtel_money', $amount, $phone, $reference, $description);
```

### 3. Card Payments (Visa/Mastercard)

```php
$imbank->initiatePayment('card', $amount, '', $reference, $description);
```

### 4. PesaLink

```php
$imbank->initiatePayment('pesalink', $amount, $phone, $reference, $description);
```

## Migration from Daraja

### Database Migration

The system supports both Daraja and I&M Bank payments simultaneously. Old Daraja payments remain functional while new payments use I&M Bank.

### Gradual Migration Steps

1. **Phase 1**: Deploy I&M Bank integration alongside Daraja
2. **Phase 2**: Test thoroughly in sandbox environment
3. **Phase 3**: Switch production to I&M Bank
4. **Phase 4**: Keep Daraja code for historical payment records

### Updating Existing Controllers

Find and replace in your payment controllers:

```php
// Old Daraja code
$daraja = app(DarajaService::class);
$response = $daraja->initializeStkPush($amount, $phone, $reference, $desc);

// New I&M Bank code
$imbank = app(IMBankService::class);
$response = $imbank->initiatePayment('mpesa', $amount, $phone, $reference, $desc);
```

## Testing

### Sandbox Mode

In sandbox mode with empty credentials, the service returns mock responses:

```php
// Returns mock successful response without calling actual API
$response = $imbank->initiatePayment('mpesa', 600, '254712345678', 'TEST-001', 'Test payment');
```

### Testing Callbacks

Use tools like Postman or ngrok to test webhook callbacks locally:

```bash
ngrok http 8000
```

Then update `IMBANK_CALLBACK_URL` to your ngrok URL.

## Security Considerations

1. **Webhook Signature Verification**: Always verify webhook signatures in production
2. **HTTPS Only**: All callback URLs must use HTTPS in production
3. **IP Whitelisting**: Configure firewall to only accept webhooks from I&M Bank IPs
4. **Credential Security**: Never commit credentials to version control
5. **Transaction Verification**: Always verify transaction amounts and references

## Troubleshooting

### Common Issues

**1. Payment initiation fails**
- Check API credentials are correct
- Verify merchant account is active
- Check transaction amount is within limits
- Ensure phone number format is correct (254XXXXXXXXX)

**2. Callbacks not received**
- Verify callback URLs are accessible from internet
- Check firewall/security group settings
- Ensure HTTPS is configured properly
- Review I&M Bank webhook logs

**3. Payment status not updating**
- Check callback route is not protected by auth middleware
- Verify webhook signature validation
- Review application logs for errors
- Manually query payment status using `queryPaymentStatus()`

### Logging

All I&M Bank API interactions are logged. Check logs at:
```
storage/logs/laravel.log
```

Search for: `I&M Bank:`

## API Reference

### IMBankService Methods

#### `initiatePayment()`
Initiates a payment request

**Parameters:**
- `method` (string): Payment method ('mpesa', 'airtel_money', 'card', 'pesalink')
- `amount` (float): Amount in KSh
- `phone` (string): Customer phone number (for mobile money)
- `reference` (string): Internal reference/transaction ID
- `description` (string): Payment description
- `metadata` (array): Additional data to store

**Returns:** Array with transaction details

#### `queryPaymentStatus()`
Queries the status of a payment

**Parameters:**
- `transactionId` (string): I&M Bank transaction ID
- `reference` (string): Internal reference (optional)

**Returns:** Array with current status

#### `parseCallback()`
Parses I&M Bank callback data

**Parameters:**
- `body` (array): Raw callback data

**Returns:** Standardized payment data array

## Support

For technical issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Review I&M Bank API documentation
3. Contact I&M Bank support: customercare@imbank.co.ke
4. Review this documentation

## Additional Resources

- [I&M Bank Website](https://www.imbankgroup.com/ke/)
- [I&M Payment API Gateway](https://www.imbankgroup.com/ke/business-solutions/paymentapigateway/)
- Contact: 0719 088 000, 0732 100 000
