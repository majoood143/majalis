<x-filament-panels::page>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary-500">
                <x-heroicon-o-cog class="w-6 h-6 text-white" />
            </div>
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                    System Maintenance
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Manage your Filament application with powerful maintenance commands
                </p>
            </div>
        </div>
    </x-slot>

    @livewire('maintenance-commands')
</x-filament-panels::page>
