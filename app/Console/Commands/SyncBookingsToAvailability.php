<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\HallAvailability;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * SyncBookingsToAvailability Command
 *
 * One-time command to backfill HallAvailability records for existing bookings.
 *
 * ✅ FIX: Before the BookingObserver was added, bookings did NOT update
 *    the HallAvailability table. This command retroactively syncs all
 *    active bookings (pending/confirmed/paid) so the calendar correctly
 *    shows booked slots in blue.
 *
 * Usage:
 *   php artisan bookings:sync-availability           # Dry run (preview)
 *   php artisan bookings:sync-availability --force    # Actually sync
 *
 * @package App\Console\Commands
 */
class SyncBookingsToAvailability extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'bookings:sync-availability
                            {--force : Actually perform the sync (without this flag, dry-run only)}
                            {--hall= : Sync only a specific hall ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync active bookings to HallAvailability table (backfill booked slots)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $isDryRun = !$this->option('force');
        $hallId = $this->option('hall');

        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE — No changes will be made. Use --force to apply.');
            $this->newLine();
        }

        // Query active bookings
        $query = Booking::query()
            ->whereIn('status', ['pending', 'confirmed', 'paid'])
            ->with('hall');

        if ($hallId) {
            $query->where('hall_id', (int) $hallId);
            $this->info("Filtering by hall ID: {$hallId}");
        }

        $bookings = $query->get();

        $this->info("Found {$bookings->count()} active booking(s) to sync.");
        $this->newLine();

        $synced = 0;
        $skipped = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($bookings->count());
        $progressBar->start();

        foreach ($bookings as $booking) {
            try {
                $slotsToMark = $this->getSlotsToMark($booking->time_slot);

                foreach ($slotsToMark as $slot) {
                    $existing = HallAvailability::where('hall_id', $booking->hall_id)
                        ->where('date', $booking->booking_date->format('Y-m-d'))
                        ->where('time_slot', $slot)
                        ->first();

                    // Skip if already correctly marked as booked
                    if ($existing && !$existing->is_available && $existing->reason === 'booked') {
                        $skipped++;
                        continue;
                    }

                    if (!$isDryRun) {
                        HallAvailability::updateOrCreate(
                            [
                                'hall_id'   => $booking->hall_id,
                                'date'      => $booking->booking_date->format('Y-m-d'),
                                'time_slot' => $slot,
                            ],
                            [
                                'is_available' => false,
                                'reason'       => 'booked',
                                'notes'        => "Booking #{$booking->booking_number} (backfill)",
                            ]
                        );
                    }

                    $synced++;
                }
            } catch (\Throwable $e) {
                $errors++;
                $this->newLine();
                $this->error("Error syncing booking #{$booking->booking_number}: {$e->getMessage()}");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info('📊 Sync Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total bookings processed', $bookings->count()],
                ['Slots synced (updated/created)', $synced],
                ['Slots already correct (skipped)', $skipped],
                ['Errors', $errors],
            ]
        );

        if ($isDryRun && $synced > 0) {
            $this->newLine();
            $this->warn("⚠️  {$synced} slot(s) would be updated. Run with --force to apply changes.");
        }

        if (!$isDryRun && $synced > 0) {
            Log::info('Bookings synced to HallAvailability', [
                'synced'  => $synced,
                'skipped' => $skipped,
                'errors'  => $errors,
            ]);
            $this->newLine();
            $this->info('✅ Sync completed successfully!');
        }

        return self::SUCCESS;
    }

    /**
     * Get slots to mark for a given time slot.
     *
     * @param string $timeSlot
     * @return array<string>
     */
    protected function getSlotsToMark(string $timeSlot): array
    {
        if ($timeSlot === 'full_day') {
            return ['morning', 'afternoon', 'evening', 'full_day'];
        }

        return [$timeSlot];
    }
}
