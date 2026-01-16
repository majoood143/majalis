<x-filament-panels::page>
    {{-- Calendar Legend --}}
    <div class="p-4 mb-4 bg-white shadow-sm dark:bg-gray-800 rounded-xl">
        <h3 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('owner.availability.legend') }}
        </h3>
        <div class="flex flex-wrap gap-3 sm:gap-6">
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded" style="background-color: #22c55e;"></span>
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('owner.availability.available') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded" style="background-color: #ef4444;"></span>
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('owner.availability.blocked') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded" style="background-color: #3b82f6;"></span>
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('owner.availability.booked') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded" style="background-color: #f59e0b;"></span>
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('owner.availability.maintenance') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded" style="background-color: #a855f7;"></span>
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('owner.availability.reasons.holiday') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded" style="background-color: #9ca3af;"></span>
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('owner.availability.past') }}</span>
            </div>
        </div>
    </div>

    {{-- Instructions Panel --}}
    <div class="p-4 mb-4 border border-blue-200 bg-blue-50 dark:bg-blue-900/20 rounded-xl dark:border-blue-800">
        <div class="flex items-start gap-3">
            <x-heroicon-o-information-circle class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" />
            <div class="text-sm text-blue-700 dark:text-blue-300">
                <p class="mb-1 font-medium">{{ __('owner.fullcalendar.instructions.title') }}</p>
                <ul class="space-y-1 text-blue-600 list-disc list-inside dark:text-blue-400">
                    <li>{{ __('owner.fullcalendar.instructions.click_date') }}</li>
                    <li>{{ __('owner.fullcalendar.instructions.click_event') }}</li>
                    <li>{{ __('owner.fullcalendar.instructions.drag_event') }}</li>
                    <li>{{ __('owner.fullcalendar.instructions.filter_hall') }}</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- The Calendar Widget --}}
    {{-- @livewire(\App\Filament\Owner\Widgets\AvailabilityCalendarWidget::class) --}}
</x-filament-panels::page>
