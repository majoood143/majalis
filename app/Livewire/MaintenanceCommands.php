<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class MaintenanceCommands extends Component
{
    public $output = '';
    public $commandHistory = [];
    public $systemInfo = [];

    public function mount()
    {
        $this->loadSystemInfo();
        $this->commandHistory = session()->get('command_history', []);
    }

    public function loadSystemInfo()
    {
        $this->systemInfo = [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'timezone' => config('app.timezone'),
            'maintenance_mode' => app()->isDownForMaintenance(),
            'queue_driver' => config('queue.default'),
            'queue_connection' => config('queue.connections.' . config('queue.default') . '.driver', 'unknown'),
        ];
    }

    // 1. Basic Cache Commands
    public function runCommand($command)
    {
        $startTime = microtime(true);

        try {
            $output = new BufferedOutput();
            Artisan::call($command, [], $output);
            $result = $output->fetch();

            // Add to history
            $this->addToHistory($command, 'success', round(microtime(true) - $startTime, 2));

            $this->output = "✅ Command executed successfully: php artisan {$command}\n\n" . $result;
            $this->dispatch('notify', title: 'Success', message: "{$command} completed", status: 'success');
        } catch (\Exception $e) {
            $this->addToHistory($command, 'error', round(microtime(true) - $startTime, 2));
            $this->output = "❌ Error executing command: php artisan {$command}\n\nError: " . $e->getMessage();
            $this->dispatch('notify', title: 'Error', message: "Failed to execute {$command}", status: 'danger');
        }
    }

    public function runAllCommands()
    {
        $this->output = "🚀 Starting batch cache clearing process...\n\n";

        $commands = [
            'cache:clear',
            'config:clear',
            'route:clear',
            'view:clear'
        ];

        foreach ($commands as $command) {
            $this->output .= "Running: php artisan {$command}...\n";

            try {
                $output = new BufferedOutput();
                Artisan::call($command, [], $output);
                $this->output .= "✅ Success: " . $output->fetch() . "\n\n";
                $this->addToHistory($command, 'success', 0);
            } catch (\Exception $e) {
                $this->output .= "❌ Error: " . $e->getMessage() . "\n\n";
                $this->addToHistory($command, 'error', 0);
            }
        }

        $this->output .= "🎉 All commands completed!";
        $this->dispatch('notify', title: 'Success', message: 'All caches cleared', status: 'success');
    }

    // 2. Database Commands
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
            Artisan::call('migrate:fresh --seed');
            $this->output = "✅ Database refreshed and seeded successfully!\n\n" . Artisan::output();
            $this->addToHistory('migrate:fresh --seed', 'success', 0);
            $this->dispatch('notify', title: 'Success', message: 'Database refreshed', status: 'success');
        } catch (\Exception $e) {
            $this->output = "❌ Error: " . $e->getMessage();
            $this->addToHistory('migrate:fresh --seed', 'error', 0);
        }
    }

    // 3. Quick Actions
    public function clearLogs()
    {
        $logPath = storage_path('logs/laravel.log');

        if (file_exists($logPath)) {
            file_put_contents($logPath, '');
            $this->output = "🗑️ Laravel log file cleared successfully!";
            $this->addToHistory('clear_logs', 'success', 0);
            $this->dispatch('notify', title: 'Success', message: 'Logs cleared', status: 'success');
        } else {
            $this->output = "📝 Log file not found at: " . $logPath;
            $this->addToHistory('clear_logs', 'error', 0);
        }
    }

    public function storageLink()
    {
        try {
            Artisan::call('storage:link');
            $this->output = "🔗 Storage link created successfully!\nSymbolic link created for public/storage → storage/app/public";
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
            Artisan::call('down --secret=' . $secret);
            $this->output = "⚠️ Maintenance mode enabled.\nSecret URL: " . url('/' . $secret);
            $this->addToHistory('down', 'success', 0);
            $this->dispatch('notify', title: 'Warning', message: 'Maintenance mode enabled', status: 'warning');
        }

        // Refresh system info
        $this->loadSystemInfo();
    }

    public function optimize()
    {
        $this->output = "🔧 Running optimization commands...\n\n";

        $commands = [
            'optimize:clear' => 'optimize:clear',
            'optimize' => 'optimize',
            'package:discover' => 'package:discover'
        ];

        foreach ($commands as $name => $command) {
            $this->output .= "Running: php artisan {$command}...\n";
            try {
                $output = new BufferedOutput();
                Artisan::call($command, [], $output);
                $this->output .= "✅ Success: " . $output->fetch() . "\n\n";
                $this->addToHistory($command, 'success', 0);
            } catch (\Exception $e) {
                $this->output .= "⚠️ Note: " . $e->getMessage() . "\n\n";
                $this->addToHistory($command, 'warning', 0);
            }
        }

        $this->output .= "🎯 Optimization complete!";
        $this->dispatch('notify', title: 'Success', message: 'Optimization completed', status: 'success');
    }

    // 4. Helper Methods
    private function addToHistory($command, $status, $executionTime)
    {
        $this->commandHistory[] = [
            'command' => $command,
            'timestamp' => now()->toDateTimeString(),
            'execution_time' => $executionTime,
            'status' => $status
        ];

        // Keep only last 10 commands
        if (count($this->commandHistory) > 10) {
            array_shift($this->commandHistory);
        }

        session()->put('command_history', $this->commandHistory);
    }

    public function getLogSize()
    {
        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            return round(filesize($logPath) / 1024 / 1024, 2);
        }
        return 0;
    }

    public function getCacheSize()
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

    // 5. Additional Commands
    public function clearViewCache()
    {
        $this->runCommand('view:clear');
    }

    public function clearConfigCache()
    {
        $this->runCommand('config:clear');
    }

    public function clearRouteCache()
    {
        $this->runCommand('route:clear');
    }

    public function clearAppCache()
    {
        $this->runCommand('cache:clear');
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
            // Hide sensitive values (very basic protection)
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

    public function render()
    {
        return view('livewire.maintenance-commands', [
            'logSize' => $this->getLogSize(),
            'cacheSize' => $this->getCacheSize(),
        ]);
    }

    // Add these methods to your MaintenanceCommands Livewire component

    // Queue Management Methods
    public function restartQueue()
    {
        try {
            // Stop any running queue workers
            Artisan::call('queue:restart');
            $this->output = "🔄 Queue workers restarted successfully!\n";
            $this->output .= "All queue workers will gracefully restart on their next job.";
            $this->addToHistory('queue:restart', 'success', 0);
            $this->dispatch('notify', title: 'Success', message: 'Queue restarted', status: 'success');
        } catch (\Exception $e) {
            $this->output = "❌ Error restarting queue: " . $e->getMessage();
            $this->addToHistory('queue:restart', 'error', 0);
        }
    }

    public function clearFailedJobs()
    {
        try {
            $count = \Illuminate\Support\Facades\DB::table('failed_jobs')->count();
            \Illuminate\Support\Facades\DB::table('failed_jobs')->truncate();

            $this->output = "🗑️ Cleared {$count} failed jobs from the queue!\n";
            $this->output .= "Failed jobs table has been emptied.";
            $this->addToHistory('clear_failed_jobs', 'success', 0);
            $this->dispatch('notify', title: 'Success', message: "Cleared {$count} failed jobs", status: 'success');
        } catch (\Exception $e) {
            $this->output = "❌ Error clearing failed jobs: " . $e->getMessage();
            $this->addToHistory('clear_failed_jobs', 'error', 0);
        }
    }

    public function retryFailedJobs()
    {
        try {
            Artisan::call('queue:retry all');
            $output = Artisan::output();
            $this->output = "🔄 Retrying all failed jobs...\n\n" . $output;
            $this->addToHistory('queue:retry all', 'success', 0);
            $this->dispatch('notify', title: 'Success', message: 'Failed jobs retried', status: 'success');
        } catch (\Exception $e) {
            $this->output = "❌ Error retrying failed jobs: " . $e->getMessage();
            $this->addToHistory('queue:retry all', 'error', 0);
        }
    }

    public function flushFailedJobs()
    {
        try {
            Artisan::call('queue:flush');
            $this->output = "🗑️ Flushed all failed jobs!\n";
            $this->output .= "All failed jobs have been permanently deleted.";
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
            Artisan::call('queue:work --once');
            $output = Artisan::output();

            if (str_contains($output, 'Processing job')) {
                $this->output = "✅ Queue worker is functional!\n\n" . $output;
            } else {
                $this->output = "⚠️ Queue status check completed.\n\n" . $output;
            }

            $this->addToHistory('queue:status', 'success', 0);
        } catch (\Exception $e) {
            $this->output = "❌ Error checking queue status: " . $e->getMessage();
            $this->addToHistory('queue:status', 'error', 0);
        }
    }

    // For daemon queue worker (with caution)
    public function startDaemonQueue()
    {
        $this->output = "⚠️ WARNING: Starting daemon queue worker...\n\n";
        $this->output .= "This will start a long-running queue worker process.\n";
        $this->output .= "Make sure to monitor it and stop it when needed.\n\n";
        $this->output .= "Command to run manually:\n";
        $this->output .= "php artisan queue:work --daemon\n\n";
        $this->output .= "To stop: Press Ctrl+C or kill the process.\n";
        $this->output .= "For production, consider using Supervisor instead.";

        $this->dispatch(
            'notify',
            title: 'Manual Action Required',
            message: 'Daemon queue worker needs to be started manually',
            status: 'warning'
        );
    }

    // Get queue statistics
    public function getQueueStats()
    {
        try {
            $pending = \Illuminate\Support\Facades\DB::table('jobs')->count();
            $failed = \Illuminate\Support\Facades\DB::table('failed_jobs')->count();

            $this->output = "📊 Queue Statistics:\n\n";
            $this->output .= "Pending Jobs: {$pending}\n";
            $this->output .= "Failed Jobs: {$failed}\n\n";

            if ($failed > 0) {
                $this->output .= "⚠️ You have {$failed} failed jobs. Consider retrying or clearing them.";
            }

            $this->addToHistory('queue:stats', 'success', 0);
        } catch (\Exception $e) {
            $this->output = "❌ Error getting queue stats: " . $e->getMessage();
            $this->addToHistory('queue:stats', 'error', 0);
        }
    }
}
