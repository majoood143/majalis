<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SendBookingReminders;
use App\Jobs\AutoCompleteBookings;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {

        // Cleanup expired guest sessions hourly
        $schedule->command('guest:cleanup-sessions')->hourly();

        // Delete old expired sessions weekly
        $schedule->command('guest:cleanup-sessions --delete-old --days=30')->weekly();

        
        // Send booking reminders daily at 9 AM
        $schedule->job(new SendBookingReminders())
            ->dailyAt('09:00')
            ->name('send-booking-reminders')
            ->withoutOverlapping();

        // Auto-complete past bookings daily at midnight
        $schedule->job(new AutoCompleteBookings())
            ->dailyAt('00:30')
            ->name('auto-complete-bookings')
            ->withoutOverlapping();

        // Clean up old temporary files weekly
        $schedule->command('storage:link')->weekly();

        // Generate monthly reports on the 1st of each month
        $schedule->call(function () {
            // Add monthly report generation logic here
        })->monthlyOn(1, '02:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
