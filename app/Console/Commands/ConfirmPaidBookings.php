<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\BookingService;
use App\Services\DarajaService;
use Illuminate\Console\Command;

class ConfirmPaidBookings extends Command
{
    protected $signature   = 'bookings:confirm-paid';
    protected $description = 'Confirm bookings whose M-Pesa payment is marked successful but the booking is still pending. Also re-queries Daraja for any long-standing pending payments.';

    public function __construct(
        protected DarajaService  $daraja,
        protected BookingService $bookingService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Checking for paid but unconfirmed bookings…');

        // ── 1. Payments already marked success but booking still pending ─────
        $payments = Payment::with('booking')
            ->where('status', 'success')
            ->whereHas('booking', fn($q) => $q->where('status', 'pending'))
            ->get();

        if ($payments->isNotEmpty()) {
            $this->info("Found {$payments->count()} paid booking(s) to confirm.");

            foreach ($payments as $payment) {
                $booking = $payment->booking;
                try {
                    $this->bookingService->confirmBooking($booking);
                    $this->info("✓ Confirmed booking {$booking->uuid} (ref: {$payment->mpesa_reference})");
                } catch (\Exception $e) {
                    $this->error("✗ Failed to confirm booking {$booking->uuid}: {$e->getMessage()}");
                }
            }
        } else {
            $this->info('No paid bookings need confirmation.');
        }

        // ── 2. Re-query Daraja for pending payments older than 3 minutes ─────
        $this->newLine();
        $this->info('Checking pending payments via Daraja STK query…');

        $pending = Payment::with('booking')
            ->where('status', 'pending')
            ->whereNotNull('checkout_request_id')
            ->where('created_at', '>=', now()->subDays(1))
            ->where('created_at', '<=', now()->subMinutes(3)) // give Daraja time to callback
            ->get();

        if ($pending->isEmpty()) {
            $this->info('No pending payments to re-query.');
            return 0;
        }

        $this->info("Found {$pending->count()} pending payment(s) to re-query.");

        foreach ($pending as $payment) {
            try {
                $result     = $this->daraja->queryStkStatus($payment->checkout_request_id);
                $resultCode = $result['ResultCode'] ?? null;

                if ($resultCode === '0' || $resultCode === 0) {
                    $this->info("Payment {$payment->mpesa_reference} confirmed via STK query.");

                    $payment->update(['status' => 'success', 'paid_at' => now()]);

                    if ($payment->booking && $payment->booking->status === 'pending') {
                        $this->bookingService->confirmBooking($payment->booking);
                        $this->info("✓ Confirmed booking {$payment->booking->uuid}");
                    }
                } else {
                    $desc = $result['ResultDesc'] ?? 'unknown';
                    $this->warn("Payment {$payment->mpesa_reference} not yet confirmed: {$desc}");
                }
            } catch (\Exception $e) {
                $this->error("✗ Failed to query payment {$payment->mpesa_reference}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info('Done!');
        return 0;
    }
}
