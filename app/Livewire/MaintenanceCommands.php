<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\BufferedOutput;
use Illuminate\Support\Str;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class MaintenanceCommands extends Component
{
    public $output = '';
    public $commandHistory = [];
    public $systemInfo = [];
    public $logLines = 100;

    public function mount()
    {
        $this->loadSystemInfo();
        $this->commandHistory = session()->get('command_history', []);
    }

    public function loadSystemInfo()
    {
        $dbDefault   = config('database.default');
        $diskFree    = @disk_free_space('/');
        $diskTotal   = @disk_total_space('/');

        $this->systemInfo = [
            // Application
            'laravel_version'    => app()->version(),
            'php_version'        => PHP_VERSION,
            'environment'        => app()->environment(),
            'timezone'           => config('app.timezone'),
            // Status
            'debug_mode'         => config('app.debug'),
            'maintenance_mode'   => app()->isDownForMaintenance(),
            'queue_driver'       => config('queue.default'),
            'cache_driver'       => config('cache.default'),
            // Infrastructure
            'db_driver'          => $dbDefault,
            'db_database'        => config("database.connections.{$dbDefault}.database", 'N/A'),
            'session_driver'     => config('session.driver'),
            'mail_mailer'        => config('mail.default'),
            // Server
            'server_os'          => PHP_OS,
            'php_memory_limit'   => ini_get('memory_limit'),
            'php_extensions'     => count(get_loaded_extensions()),
            'server_software'    => $_SERVER['SERVER_SOFTWARE'] ?? (PHP_SAPI === 'cli' ? 'CLI/' . PHP_SAPI : 'Unknown'),
            // Disk
            'disk_free_gb'       => $diskFree !== false  ? round($diskFree  / 1073741824, 1) : null,
            'disk_total_gb'      => $diskTotal !== false ? round($diskTotal / 1073741824, 1) : null,
            'disk_used_pct'      => ($diskFree !== false && $diskTotal !== false && $diskTotal > 0)
                                        ? round((1 - $diskFree / $diskTotal) * 100, 1)
                                        : 0,
        ];
    }

    // ─── Cache ───────────────────────────────────────────────────────────────

    public function clearAppCache()
    {
        $this->runCommand('cache:clear');
    }

    public function clearConfigCache()
    {
        $this->runCommand('config:clear');
    }

    public function clearRouteCache()
    {
        $this->runCommand('route:clear');
    }

    public function clearViewCache()
    {
        $this->runCommand('view:clear');
    }

    public function clearAllCaches()
    {
        $this->output = "🚀 Clearing all caches...\n\n";

        foreach (['cache:clear', 'config:clear', 'route:clear', 'view:clear'] as $command) {
            $this->output .= "Running: php artisan {$command}...\n";
            try {
                $out = new BufferedOutput();
                Artisan::call($command, [], $out);
                $this->output .= "✅ " . ($out->fetch() ?: "Done") . "\n\n";
                $this->addToHistory($command, 'success', 0);
            } catch (\Exception $e) {
                $this->output .= "❌ Error: " . $e->getMessage() . "\n\n";
                $this->addToHistory($command, 'error', 0);
            }
        }

        $this->output .= "🎉 All caches cleared!";
        $this->dispatch('notify', title: 'Success', message: 'All caches cleared', status: 'success');
    }

    public function cacheConfig()
    {
        $this->runCommand('config:cache');
    }

    public function cacheRoutes()
    {
        $this->runCommand('route:cache');
    }

    public function cacheViews()
    {
        $this->runCommand('view:cache');
    }

    public function cacheAll()
    {
        $this->output = "⚙️ Building application caches...\n\n";

        foreach (['config:cache', 'route:cache', 'view:cache', 'event:cache'] as $cmd) {
            $this->output .= "Running: php artisan {$cmd}...\n";
            try {
                $out = new BufferedOutput();
                Artisan::call($cmd, [], $out);
                $this->output .= "✅ " . ($out->fetch() ?: "Done") . "\n";
                $this->addToHistory($cmd, 'success', 0);
            } catch (\Exception $e) {
                $this->output .= "⚠️ " . $e->getMessage() . "\n";
            }
        }

        $this->output .= "\n🎯 Caching complete!";
        $this->dispatch('notify', title: 'Success', message: 'Application cached', status: 'success');
    }

    // ─── Optimization ────────────────────────────────────────────────────────

    public function optimize()
    {
        $this->output = "🔧 Running optimization commands...\n\n";

        foreach (['optimize:clear', 'optimize', 'package:discover'] as $command) {
            $this->output .= "Running: php artisan {$command}...\n";
            try {
                $out = new BufferedOutput();
                Artisan::call($command, [], $out);
                $this->output .= "✅ " . ($out->fetch() ?: "Done") . "\n\n";
                $this->addToHistory($command, 'success', 0);
            } catch (\Exception $e) {
                $this->output .= "⚠️ Note: " . $e->getMessage() . "\n\n";
                $this->addToHistory($command, 'warning', 0);
            }
        }

        $this->output .= "🎯 Optimization complete!";
        $this->dispatch('notify', title: 'Success', message: 'Optimization completed', status: 'success');
    }

    // ─── Quick Actions ───────────────────────────────────────────────────────

    public function storageLink()
    {
        try {
            Artisan::call('storage:link');
            $this->output = "🔗 Storage link created successfully!\nSymbolic link: public/storage → storage/app/public";
            $this->addToHistory('storage:link', 'success', 0);
            $this->dispatch('notify', title: 'Success', message: 'Storage link created', status: 'success');
        } catch (\Exception $e) {
            $this->output = "❌ Error creating storage link: " . $e->getMessage();
            $this->addToHistory('storage:link', 'error', 0);
        }
    }

    public function toggleMaintenance()
    {
        if (app()->isDownForMaintenance()) {
            Artisan::call('up');
            $this->output = "✅ Maintenance mode disabled. Site is now live!";
            $this->addToHistory('up', 'success', 0);
            $this->dispatch('notify', title: 'Success', message: 'Maintenance mode disabled', status: 'success');
        } else {
            $secret = Str::random(32);
            Artisan::call('down', ['--secret' => $secret]);
            $this->output = "⚠️ Maintenance mode enabled.\nSecret bypass URL: " . url('/' . $secret);
            $this->addToHistory('down', 'success', 0);
            $this->dispatch('notify', title: 'Warning', message: 'Maintenance mode enabled', status: 'warning');
        }

        $this->loadSystemInfo();
    }

    public function runSchedule()
    {
        try {
            Artisan::call('schedule:run');
            $this->output = "⏰ Schedule executed!\n\n" . Artisan::output();
            $this->addToHistory('schedule:run', 'success', 0);
            $this->dispatch('notify', title: 'Success', message: 'Schedule executed', status: 'success');
        } catch (\Exception $e) {
            $this->output = "❌ Error running schedule: " . $e->getMessage();
            $this->addToHistory('schedule:run', 'error', 0);
        }
    }

    public function viewEnv()
    {
        $envPath = base_path('.env');

        if (file_exists($envPath) && is_readable($envPath)) {
            $contents = file_get_contents($envPath);
            $contents = preg_replace_callback('/(.*?_?(KEY|SECRET|PASSWORD|TOKEN)=)(.*)/mi', function ($matches) {
                return $matches[1] . '********';
            }, $contents);

            $this->output = "📁 .env file (sensitive values hidden):\n\n" . $contents;
            $this->addToHistory('view_env', 'success', 0);
        } else {
            $this->output = "❌ Cannot read .env file";
            $this->addToHistory('view_env', 'error', 0);
        }
    }

    // ─── Database ────────────────────────────────────────────────────────────

    public function checkMigrations()
    {
        try {
            Artisan::call('migrate:status');
            $this->output = Artisan::output();
            $this->addToHistory('migrate:status', 'success', 0);
        } catch (\Exception $e) {
            $this->output = "❌ Error checking migrations: " . $e->getMessage();
            $this->addToHistory('migrate:status', 'error', 0);
        }
    }

    public function runMigrations()
    {
        try {
            Artisan::call('migrate');
            $this->output = "✅ Migrations ran successfully!\n\n" . Artisan::output();
            $this->addToHistory('migrate', 'success', 0);
            $this->dispatch('notify', title: 'Success', message: 'Migrations completed', status: 'success');
        } catch (\Exception $e) {
            $this->output = "❌ Error running migrations: " . $e->getMessage();
            $this->addToHistory('migrate', 'error', 0);
        }
    }

    public function migrateFreshSeed()
    {
        try {
            Artisan::call('migrate:fresh', ['--seed' => true]);
            $this->output = "✅ Database refreshed and seeded successfully!\n\n" . Artisan::output();
            $this->addToHistory('migrate:fresh --seed', 'success', 0);
            $this->dispatch('notify', title: 'Success', message: 'Database refreshed', status: 'success');
        } catch (\Exception $e) {
            $this->output = "❌ Error: " . $e->getMessage();
            $this->addToHistory('migrate:fresh --seed', 'error', 0);
        }
    }

    // ─── Queue ───────────────────────────────────────────────────────────────

    public function getQueueStats()
    {
        try {
            $pending = DB::table('jobs')->count();
            $failed  = DB::table('failed_jobs')->count();

            $this->output  = "📊 Queue Statistics:\n\n";
            $this->output .= "  Pending Jobs : {$pending}\n";
            $this->output .= "  Failed Jobs  : {$failed}\n";

            if ($failed > 0) {
                $this->output .= "\n⚠️  You have {$failed} failed job(s). Consider retrying or flushing them.";
            } else {
                $this->output .= "\n✅ No failed jobs.";
            }

            $this->addToHistory('queue:stats', 'success', 0);
        } catch (\Exception $e) {
            $this->output = "❌ Error getting queue stats: " . $e->getMessage();
            $this->addToHistory('queue:stats', 'error', 0);
        }
    }

    public function restartQueue()
    {
        try {
            Artisan::call('queue:restart');
            $this->output  = "🔄 Queue restart signal sent.\n";
            $this->output .= "Workers will gracefully restart after completing their current job.";
            $this->addToHistory('queue:restart', 'success', 0);
            $this->dispatch('notify', title: 'Success', message: 'Queue restart signal sent', status: 'success');
        } catch (\Exception $e) {
            $this->output = "❌ Error restarting queue: " . $e->getMessage();
            $this->addToHistory('queue:restart', 'error', 0);
        }
    }

    public function retryFailedJobs()
    {
        try {
            Artisan::call('queue:retry', ['id' => ['all']]);
            $output = Artisan::output();
            $this->output = "🔄 Retrying all failed jobs...\n\n" . ($output ?: "Done.");
            $this->addToHistory('queue:retry all', 'success', 0);
            $this->dispatch('notify', title: 'Success', message: 'Failed jobs queued for retry', status: 'success');
        } catch (\Exception $e) {
            $this->output = "❌ Error retrying failed jobs: " . $e->getMessage();
            $this->addToHistory('queue:retry all', 'error', 0);
        }
    }

    public function flushFailedJobs()
    {
        try {
            Artisan::call('queue:flush');
            $this->output  = "🗑️ All failed jobs have been permanently deleted.\n";
            $this->output .= Artisan::output();
            $this->addToHistory('queue:flush', 'success', 0);
            $this->dispatch('notify', title: 'Success', message: 'Failed jobs flushed', status: 'success');
        } catch (\Exception $e) {
            $this->output = "❌ Error flushing failed jobs: " . $e->getMessage();
            $this->addToHistory('queue:flush', 'error', 0);
        }
    }

    public function checkQueueStatus()
    {
        try {
            Artisan::call('queue:work', ['--once' => true]);
            $output = Artisan::output();
            $this->output  = str_contains($output, 'Processing')
                ? "✅ Queue worker is functional!\n\n" . $output
                : "⚠️ Queue check completed (no jobs to process).\n\n" . $output;
            $this->addToHistory('queue:work --once', 'success', 0);
        } catch (\Exception $e) {
            $this->output = "❌ Error checking queue: " . $e->getMessage();
            $this->addToHistory('queue:work --once', 'error', 0);
        }
    }

    public function startDaemonQueue()
    {
        $this->output  = "⚠️  Daemon queue workers cannot be started from the web.\n\n";
        $this->output .= "Run this command on the server:\n\n";
        $this->output .= "  php artisan queue:work --daemon\n\n";
        $this->output .= "For production, manage workers with Supervisor:\n";
        $this->output .= "  supervisorctl start laravel-worker:*\n";

        $this->dispatch('notify', title: 'Info', message: 'See output for manual instructions', status: 'warning');
    }

    // ─── Logs ────────────────────────────────────────────────────────────────

    public function viewLogs()
    {
        $logPath = storage_path('logs/laravel.log');

        if (!file_exists($logPath)) {
            $this->output = "📝 Log file not found at: " . $logPath;
            return;
        }

        $content = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (empty($content)) {
            $this->output = "📭 Log file is empty.";
            return;
        }

        $lines = (int) $this->logLines;
        $total = count($content);
        $slice = array_slice($content, -$lines);

        $this->output  = "📋 Showing last {$lines} of {$total} total lines — laravel.log\n";
        $this->output .= str_repeat('─', 60) . "\n\n";
        $this->output .= implode("\n", $slice);

        $this->addToHistory('view_logs', 'success', 0);
    }

    public function clearLogs()
    {
        $logPath = storage_path('logs/laravel.log');

        if (file_exists($logPath)) {
            file_put_contents($logPath, '');
            $this->output = "🗑️ laravel.log cleared successfully.";
            $this->addToHistory('clear_logs', 'success', 0);
            $this->dispatch('notify', title: 'Success', message: 'Log file cleared', status: 'success');
        } else {
            $this->output = "📝 Log file not found at: " . $logPath;
            $this->addToHistory('clear_logs', 'error', 0);
        }
    }

    // ─── Sessions ────────────────────────────────────────────────────────────

    public function showGuestSessionStats()
    {
        try {
            $stats = \App\Models\GuestSession::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $total = array_sum($stats);
            $this->output  = "📊 Guest Session Statistics:\n\n";
            $this->output .= "  Total: {$total}\n\n";

            foreach (['pending', 'verified', 'booking', 'payment', 'completed', 'expired', 'cancelled'] as $status) {
                $count = $stats[$status] ?? 0;
                $this->output .= "  • " . ucfirst($status) . ": {$count}\n";
            }

            $pastExpiry = \App\Models\GuestSession::where('expires_at', '<=', now())->count();
            $this->output .= "\n⚠️  Sessions past expiry time: {$pastExpiry}";
            $this->addToHistory('guest_session_stats', 'success', 0);
        } catch (\Exception $e) {
            $this->output = "❌ Error: " . $e->getMessage();
            $this->addToHistory('guest_session_stats', 'error', 0);
        }
    }

    public function clearExpiredSessions()
    {
        try {
            $count = \App\Models\GuestSession::where('expires_at', '<=', now())
                ->whereNotIn('status', ['completed'])
                ->count();

            \App\Models\GuestSession::cleanupExpired();

            $deleted = \App\Models\GuestSession::where('status', 'expired')
                ->where('expires_at', '<=', now()->subDays(1))
                ->delete();

            $this->output  = "🧹 Guest session cleanup:\n\n";
            $this->output .= "  • Marked expired : {$count} session(s)\n";
            $this->output .= "  • Deleted old    : {$deleted} session(s)\n";

            $this->addToHistory('clear_expired_sessions', 'success', 0);
            $this->dispatch('notify', title: 'Success', message: "{$count} sessions marked expired", status: 'success');
        } catch (\Exception $e) {
            $this->output = "❌ Error: " . $e->getMessage();
            $this->addToHistory('clear_expired_sessions', 'error', 0);
        }
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function runCommand(string $command)
    {
        $startTime = microtime(true);

        try {
            $out = new BufferedOutput();
            Artisan::call($command, [], $out);
            $result = $out->fetch();

            $elapsed = round(microtime(true) - $startTime, 2);
            $this->addToHistory($command, 'success', $elapsed);
            $this->output = "✅ php artisan {$command}\n\n" . ($result ?: "Completed in {$elapsed}s");
            $this->dispatch('notify', title: 'Success', message: "{$command} completed", status: 'success');
        } catch (\Exception $e) {
            $elapsed = round(microtime(true) - $startTime, 2);
            $this->addToHistory($command, 'error', $elapsed);
            $this->output = "❌ php artisan {$command}\n\nError: " . $e->getMessage();
            $this->dispatch('notify', title: 'Error', message: "Failed: {$command}", status: 'danger');
        }
    }

    private function addToHistory(string $command, string $status, float $executionTime)
    {
        $this->commandHistory[] = [
            'command'        => $command,
            'timestamp'      => now()->toDateTimeString(),
            'execution_time' => $executionTime,
            'status'         => $status,
        ];

        if (count($this->commandHistory) > 15) {
            array_shift($this->commandHistory);
        }

        session()->put('command_history', $this->commandHistory);
    }

    public function clearHistory()
    {
        $this->commandHistory = [];
        session()->forget('command_history');
        $this->dispatch('notify', title: 'Cleared', message: 'Command history cleared', status: 'success');
    }

    public function getLogSize(): float
    {
        $logPath = storage_path('logs/laravel.log');
        return file_exists($logPath) ? round(filesize($logPath) / 1024 / 1024, 2) : 0;
    }

    public function getCacheSize(): float
    {
        $cacheSize = 0;
        $cachePath = storage_path('framework/cache');

        if (is_dir($cachePath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cachePath));
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $cacheSize += $file->getSize();
                }
            }
            return round($cacheSize / 1024 / 1024, 2);
        }

        return 0;
    }

    public function render()
    {
        return view('livewire.maintenance-commands', [
            'logSize'   => $this->getLogSize(),
            'cacheSize' => $this->getCacheSize(),
        ]);
    }
}
