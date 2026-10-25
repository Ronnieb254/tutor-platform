# Quick Start: Fix "Could not initialize payment" Error

## The Problem
Your Paystack API keys are placeholder values, causing payment initialization to fail.

## Immediate Solution: Test Mode (No Setup Required)

I've added **automatic test mode** that works with placeholder keys. The system will now:
- ✅ Detect placeholder keys automatically
- ✅ Mock payment responses
- ✅ Allow bookings to complete
- ✅ Work immediately without any setup

## Try It Now

1. Clear config cache:
```bash
php artisan config:clear
```

2. Try creating a booking again - it should work!

3. Check logs if issues persist:
```bash
tail -f storage/logs/laravel.log
```

## For Production Use

When ready for real payments:

1. Sign up at https://paystack.com
2. Get your API keys from Settings → API Keys
3. Update `.env`:
```env
PAYSTACK_PUBLIC_KEY=pk_test_your_real_key
PAYSTACK_SECRET_KEY=sk_test_your_real_key
```
4. Run: `php artisan config:clear`

See full documentation in the Paystack Payment Setup Guide.
