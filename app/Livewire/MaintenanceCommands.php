<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class MaintenanceCommands extends Component
{
    public $output = '';

    public function runCommand($command)
    {
        $output = new BufferedOutput();
        
        try {
            Artisan::call($command, [], $output);
            $this->output = "✅ Command executed successfully: php artisan {$command}\n\n" . $output->fetch();
            $this->dispatch('notify', title: 'Success', message: "{$command} completed successfully", status: 'success');
        } catch (\Exception $e) {
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
            } catch (\Exception $e) {
                $this->output .= "❌ Error: " . $e->getMessage() . "\n\n";
            }
        }
        
        $this->output .= "🎉 All commands completed!";
        $this->dispatch('notify', title: 'Success', message: 'All caches cleared successfully', status: 'success');
    }

    public function render()
    {
        return view('livewire.maintenance-commands');
    }
}