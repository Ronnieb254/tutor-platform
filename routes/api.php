<?php

use App\Http\Controllers\DarajaCallbackController;
use Illuminate\Support\Facades\Route;

/*
 * Daraja M-Pesa callback — excluded from CSRF in bootstrap/app.php.
 * Safaricom POSTs here after the customer completes or dismisses
 * the STK Push prompt.
 */
Route::post('/daraja/callback', [DarajaCallbackController::class, 'handle'])
    ->name('daraja.callback');

/*
 * Polling endpoint — the frontend polls this to know when payment
 * has been confirmed by the Daraja callback.
 * No auth required — reference is unguessable and contains no sensitive data.
 * Auth check is done inside the controller via the payment record ownership.
 */
Route::get('/daraja/status', [DarajaCallbackController::class, 'status'])
    ->name('daraja.status');

/*
|--------------------------------------------------------------------------
| I&M Bank Payment Gateway Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\IMBankPaymentController;

// I&M Bank callback endpoints (excluded from CSRF in bootstrap/app.php)
Route::post('/imbank/callback', [IMBankPaymentController::class, 'callback'])
    ->name('imbank.callback');

Route::post('/imbank/result', [IMBankPaymentController::class, 'result'])
    ->name('imbank.result');

Route::post('/imbank/timeout', [IMBankPaymentController::class, 'timeout'])
    ->name('imbank.timeout');

// Payment status polling endpoint
Route::get('/imbank/status', [IMBankPaymentController::class, 'queryStatus'])
    ->name('imbank.status');


