<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}

        <div class="flex justify-end gap-3 mt-6">
            <x-filament::button type="submit">
                {{ __('filament.actions.save') }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
