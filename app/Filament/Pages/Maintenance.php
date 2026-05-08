<?php

namespace App\Filament\Pages;

use Exception;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class Maintenance extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog';
    protected string $view = 'filament.pages.maintenance';
    protected static string | \UnitEnum | null $navigationGroup = 'System';
    protected static ?int $navigationSort = 100;
    protected static ?string $title = 'System Maintenance';
    protected static ?string $navigationLabel = 'Maintenance';

    public $output = '';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    // Add methods that will handle form submission
    public function runCommand($command)
    {
        $output = new BufferedOutput();

        try {
            Artisan::call($command, [], $output);
            $this->output = "✅ Command executed successfully: php artisan {$command}\n\n" . $output->fetch();
            $this->dispatch('notify', title: 'Success', message: "{$command} completed successfully", status: 'success');
        } catch (Exception $e) {
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
            } catch (Exception $e) {
                $this->output .= "❌ Error: " . $e->getMessage() . "\n\n";
            }
        }

        $this->output .= "🎉 All commands completed!";
        $this->dispatch('notify', title: 'Success', message: 'All caches cleared successfully', status: 'success');
    }
}
