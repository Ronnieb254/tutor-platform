<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @deprecated  Paystack has been removed. All payments now use Daraja M-Pesa.
 *              This stub exists only to prevent "class not found" errors if any
 *              cached routes/config still reference it. It can be deleted once
 *              the application cache is cleared and routes are re-cached.
 *
 * @see DarajaCallbackController
 */
class PaystackWebhookController extends Controller
{
    public function handle(Request $request)
    {
        return response()->json(['status' => 'deprecated', 'message' => 'Paystack has been removed. Use /api/daraja/callback.'], 410);
    }
}
