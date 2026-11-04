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
 */
Route::get('/daraja/status', [DarajaCallbackController::class, 'status'])
    ->middleware('auth')
    ->name('daraja.status');
