<?php

use App\Jobs\AutoCompleteBookings;
use App\Jobs\SendBookingReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('evolution:cleanup')->daily();

// Expire stale guest booking sessions, and purge old ones weekly
Schedule::command('guest:cleanup-sessions')->hourly()->withoutOverlapping();
Schedule::command('guest:cleanup-sessions --delete-old --days=30')->weekly();

Schedule::job(new SendBookingReminders())
    ->dailyAt('09:00')
    ->name('send-booking-reminders')
    ->withoutOverlapping();

Schedule::job(new AutoCompleteBookings())
    ->dailyAt('00:30')
    ->name('auto-complete-bookings')
    ->withoutOverlapping();
