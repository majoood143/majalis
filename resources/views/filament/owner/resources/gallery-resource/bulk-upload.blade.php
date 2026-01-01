<x-filament-panels::page>
    <form wire:submit="upload">
        {{ $this->form }}
        
        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" size="lg">
                <x-heroicon-o-arrow-up-tray class="w-5 h-5 mr-2" />
                Upload Images
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
