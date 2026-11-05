<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\SessionLifecycleService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FinaliseCompletedSessions extends Command
{
    protected $signature   = 'sessions:finalise';
    protected $description = 'Finalise confirmed sessions whose scheduled end time has passed the 5-minute closing buffer.';

    public function __construct(protected SessionLifecycleService $lifecycle)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Looking for sessions to finalise…');

        // Sessions whose scheduled_at + duration_minutes + 5-min buffer have elapsed
        $closingCutoff = now()->subMinutes(5);

        $due = Booking::where('status', 'confirmed')
            ->whereRaw(
                "DATE_ADD(scheduled_at, INTERVAL (duration_minutes + 5) MINUTE) <= ?",
                [$closingCutoff->toDateTimeString()]
            )
            ->with(['student', 'tutor', 'subject'])
            ->get();

        if ($due->isEmpty()) {
            $this->info('No sessions ready to finalise.');
            return 0;
        }

        $this->info("Found {$due->count()} session(s) to finalise.");

        foreach ($due as $booking) {
            try {
                $this->lifecycle->finaliseSession($booking);
                $this->info("✓ Finalised {$booking->uuid} → {$booking->fresh()->delivery_status}");
            } catch (\Throwable $e) {
                $this->error("✗ Failed {$booking->uuid}: {$e->getMessage()}");
                Log::error("sessions:finalise failed for {$booking->uuid}: {$e->getMessage()}");
            }
        }

        $this->info('Done.');
        return 0;
    }
}
