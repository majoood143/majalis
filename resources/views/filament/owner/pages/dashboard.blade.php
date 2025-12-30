<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <x-filament::card>
            <h2 class="text-lg font-semibold">Total Halls</h2>
            <p class="text-3xl font-bold">{{ auth()->user()->halls()->count() ?? 0 }}</p>
        </x-filament::card>

        <x-filament::card>
            <h2 class="text-lg font-semibold">Active Bookings</h2>
            <p class="text-3xl font-bold">0</p>
        </x-filament::card>

        <x-filament::card>
            <h2 class="text-lg font-semibold">This Month Revenue</h2>
            <p class="text-3xl font-bold">OMR 0.000</p>
        </x-filament::card>
    </div>
</x-filament-panels::page>
