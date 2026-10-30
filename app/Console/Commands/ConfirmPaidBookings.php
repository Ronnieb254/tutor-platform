<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\BookingService;
use App\Services\PaystackService;
use Illuminate\Console\Command;

class ConfirmPaidBookings extends Command
{
    protected $signature = 'bookings:confirm-paid';
    protected $description = 'Verify and confirm bookings with successful Paystack payments';

    public function __construct(
        protected PaystackService $paystack,
        protected BookingService $bookingService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Checking for paid but unconfirmed bookings...');

        // Find payments that are successful but bookings still pending
        $payments = Payment::with('booking')
            ->where('status', 'success')
            ->whereHas('booking', function ($q) {
                $q->where('status', 'pending');
            })
            ->get();

        if ($payments->isEmpty()) {
            $this->info('No paid bookings need confirmation.');
            return 0;
        }

        $this->info("Found {$payments->count()} paid booking(s) to confirm.");

        foreach ($payments as $payment) {
            $booking = $payment->booking;
            
            try {
                $this->bookingService->confirmBooking($booking);
                $this->info("✓ Confirmed booking {$booking->uuid} (Payment: {$payment->paystack_reference})");
            } catch (\Exception $e) {
                $this->error("✗ Failed to confirm booking {$booking->uuid}: {$e->getMessage()}");
            }
        }

        // Also check for pending payments and verify with Paystack
        $this->info("\nChecking pending payments with Paystack...");
        
        $pendingPayments = Payment::with('booking')
            ->where('status', 'pending')
            ->whereNotNull('paystack_reference')
            ->where('created_at', '>=', now()->subDays(7))
            ->get();

        if ($pendingPayments->isEmpty()) {
            $this->info('No pending payments to verify.');
            return 0;
        }

        $this->info("Found {$pendingPayments->count()} pending payment(s) to verify.");

        foreach ($pendingPayments as $payment) {
            try {
                $verification = $this->paystack->verifyTransaction($payment->paystack_reference);
                
                if (($verification['status'] ?? false) && ($verification['data']['status'] ?? '') === 'success') {
                    $this->info("Payment {$payment->paystack_reference} is actually PAID on Paystack");
                    
                    // Update payment
                    $payment->update([
                        'status' => 'success',
                        'paid_at' => now(),
                    ]);

                    // Confirm booking
                    if ($payment->booking && $payment->booking->status === 'pending') {
                        $this->bookingService->confirmBooking($payment->booking);
                        $this->info("✓ Confirmed booking {$payment->booking->uuid}");
                    }
                } else {
                    $status = $verification['data']['status'] ?? 'unknown';
                    $this->warn("Payment {$payment->paystack_reference} status: {$status}");
                }
            } catch (\Exception $e) {
                $this->error("✗ Failed to verify payment {$payment->paystack_reference}: {$e->getMessage()}");
            }
        }

        $this->info("\nDone!");
        return 0;
    }
}
