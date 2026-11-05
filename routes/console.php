<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Session lifecycle scheduled tasks ─────────────────────────────────────────

// Confirm bookings whose payment went through but booking is still pending
// (catches Daraja callbacks that may have been missed)
Schedule::command('bookings:confirm-paid')->everyFiveMinutes();

// Finalise confirmed sessions whose time window has fully elapsed
// (classify delivery, credit tutor wallet, process refunds)
Schedule::command('sessions:finalise')->everyFiveMinutes();
