<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\GuestSession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * CleanupGuestSessions Command
 *
 * Artisan command to clean up expired guest booking sessions.
 * Should be scheduled to run daily or hourly via Laravel Scheduler.
 *
 * Usage:
 * - Manual: php artisan guest:cleanup-sessions
 * - With options: php artisan guest:cleanup-sessions --days=7 --dry-run
 *
 * Scheduler (app/Console/Kernel.php):
 * ```php
 * $schedule->command('guest:cleanup-sessions')->daily();
 * ```
 *
 * @package App\Console\Commands
 * @version 1.0.0
 */
class CleanupGuestSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guest:cleanup-sessions
                            {--days=7 : Delete sessions older than this many days}
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired guest booking sessions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('Guest Sessions Cleanup');
        $this->line('─────────────────────');
        $this->newLine();

        // Find expired sessions
        $expiredCount = GuestSession::expired()->count();
        
        // Find old completed/cancelled sessions
        $oldDate = now()->subDays($days);
        $oldCompletedCount = GuestSession::whereIn('status', ['completed', 'cancelled', 'expired'])
            ->where('updated_at', '<', $oldDate)
            ->count();

        // Find orphaned sessions (no booking created, expired)
        $orphanedCount = GuestSession::whereNull('booking_id')
            ->where('expires_at', '<', now())
            ->count();

        $this->table(
            ['Category', 'Count'],
            [
                ['Expired Sessions', $expiredCount],
                ["Old Sessions (>{$days} days)", $oldCompletedCount],
                ['Orphaned Sessions', $orphanedCount],
            ]
        );

        $totalToDelete = $expiredCount + $oldCompletedCount;

        if ($totalToDelete === 0) {
            $this->info('No sessions to clean up.');
            return Command::SUCCESS;
        }

        if ($dryRun) {
            $this->warn("Dry run mode: Would delete {$totalToDelete} sessions.");
            return Command::SUCCESS;
        }

        if (!$force && !$this->confirm("Delete {$totalToDelete} sessions?")) {
            $this->info('Cleanup cancelled.');
            return Command::SUCCESS;
        }

        // Perform cleanup
        $this->newLine();
        $this->info('Cleaning up sessions...');

        // Mark expired sessions
        $markedExpired = GuestSession::expired()
            ->whereNotIn('status', ['expired', 'completed', 'cancelled'])
            ->update(['status' => 'expired']);

        $this->line("  ✓ Marked {$markedExpired} sessions as expired");

        // Delete old sessions
        $deleted = GuestSession::whereIn('status', ['completed', 'cancelled', 'expired'])
            ->where('updated_at', '<', $oldDate)
            ->delete();

        $this->line("  ✓ Deleted {$deleted} old sessions");

        // Delete orphaned sessions
        $deletedOrphaned = GuestSession::whereNull('booking_id')
            ->where('expires_at', '<', now())
            ->delete();

        $this->line("  ✓ Deleted {$deletedOrphaned} orphaned sessions");

        $this->newLine();
        $this->info("Cleanup complete. Total deleted: " . ($deleted + $deletedOrphaned));

        // Log the cleanup
        Log::info('Guest sessions cleanup completed', [
            'marked_expired' => $markedExpired,
            'deleted_old' => $deleted,
            'deleted_orphaned' => $deletedOrphaned,
            'days_threshold' => $days,
        ]);

        return Command::SUCCESS;
    }
}
