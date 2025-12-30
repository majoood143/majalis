<x-filament-panels::page>
    {{-- Calendar Navigation --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4 rtl:space-x-reverse">
            <x-filament::button
                wire:click="previousMonth"
                icon="heroicon-o-chevron-left"
                color="gray"
                size="sm"
            />
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                {{ $this->monthName }}
            </h2>
            <x-filament::button
                wire:click="nextMonth"
                icon="heroicon-o-chevron-right"
                color="gray"
                size="sm"
            />
        </div>

        <div class="flex items-center space-x-2 rtl:space-x-reverse">
            <x-filament::button
                wire:click="goToToday"
                color="gray"
                size="sm"
            >
                {{ __('owner.availability.today') }}
            </x-filament::button>
        </div>
    </div>

    {{-- Legend --}}
    <div class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('owner.availability.legend') }}</h3>
        <div class="flex flex-wrap gap-4">
            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                <span class="w-4 h-4 rounded bg-green-500"></span>
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('owner.availability.available') }}</span>
            </div>
            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                <span class="w-4 h-4 rounded bg-red-500"></span>
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('owner.availability.blocked') }}</span>
            </div>
            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                <span class="w-4 h-4 rounded bg-blue-500"></span>
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('owner.availability.booked') }}</span>
            </div>
            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                <span class="w-4 h-4 rounded bg-yellow-500"></span>
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('owner.availability.maintenance') }}</span>
            </div>
            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                <span class="w-4 h-4 rounded bg-gray-400"></span>
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('owner.availability.past') }}</span>
            </div>
        </div>
    </div>

    {{-- Bulk Operations Panel --}}
    @if(count($selectedDates) > 0)
        <div class="mb-6 p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-200 dark:border-primary-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-primary-700 dark:text-primary-300">
                    {{ __('owner.availability.dates_selected', ['count' => count($selectedDates)]) }}
                </h3>
                <x-filament::button
                    wire:click="clearSelection"
                    color="gray"
                    size="xs"
                >
                    {{ __('owner.availability.clear_selection') }}
                </x-filament::button>
            </div>
            
            {{-- Block Reason Selector --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('owner.availability.block_reason') }}
                </label>
                <select wire:model="blockReason" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="blocked">{{ __('owner.availability.reasons.blocked') }}</option>
                    <option value="maintenance">{{ __('owner.availability.reasons.maintenance') }}</option>
                    <option value="holiday">{{ __('owner.availability.reasons.holiday') }}</option>
                    <option value="private_event">{{ __('owner.availability.reasons.private_event') }}</option>
                    <option value="renovation">{{ __('owner.availability.reasons.renovation') }}</option>
                    <option value="other">{{ __('owner.availability.reasons.other') }}</option>
                </select>
            </div>
            
            <div class="flex flex-wrap gap-2 mb-4">
                <x-filament::button
                    wire:click="blockSelected"
                    color="danger"
                    size="sm"
                    icon="heroicon-o-x-circle"
                >
                    {{ __('owner.availability.block_selected') }}
                </x-filament::button>
                
                <x-filament::button
                    wire:click="unblockSelected"
                    color="success"
                    size="sm"
                    icon="heroicon-o-check-circle"
                >
                    {{ __('owner.availability.unblock_selected') }}
                </x-filament::button>
            </div>

            {{-- Custom Price Section --}}
            <div class="pt-4 border-t border-primary-200 dark:border-primary-700">
                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('owner.availability.custom_price') }}
                        </label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm">
                                OMR
                            </span>
                            <input 
                                type="number" 
                                wire:model="customPriceInput"
                                step="0.001"
                                min="0"
                                class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"
                                placeholder="0.000"
                            >
                        </div>
                    </div>
                    <x-filament::button
                        wire:click="setCustomPrice"
                        color="warning"
                        size="sm"
                        icon="heroicon-o-currency-dollar"
                    >
                        {{ __('owner.availability.set_price') }}
                    </x-filament::button>
                    <x-filament::button
                        wire:click="clearCustomPrice"
                        color="gray"
                        size="sm"
                        icon="heroicon-o-x-mark"
                    >
                        {{ __('owner.availability.clear_price') }}
                    </x-filament::button>
                </div>
            </div>
        </div>
    @endif

    {{-- Calendar Grid --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        {{-- Day Headers --}}
        <div class="grid grid-cols-7 bg-gray-50 dark:bg-gray-700">
            @php
                $dayNames = app()->getLocale() === 'ar' 
                    ? ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت']
                    : ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            @endphp
            @foreach($dayNames as $dayName)
                <div class="p-3 text-center text-sm font-medium text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600">
                    {{ $dayName }}
                </div>
            @endforeach
        </div>

        {{-- Calendar Days --}}
        <div class="grid grid-cols-7">
            {{-- Empty cells for days before the first of the month --}}
            @php
                $firstDayOfMonth = $this->calendarData->first()['dayOfWeek'] ?? 0;
            @endphp
            @for($i = 0; $i < $firstDayOfMonth; $i++)
                <div class="min-h-[120px] p-2 border-b border-r border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900"></div>
            @endfor

            {{-- Calendar Days --}}
            @foreach($this->calendarData as $day)
                @php
                    $isSelected = in_array($day['date'], $selectedDates);
                @endphp
                <div 
                    class="min-h-[120px] p-2 border-b border-r border-gray-200 dark:border-gray-700 cursor-pointer transition-colors
                        {{ $day['isPast'] ? 'bg-gray-100 dark:bg-gray-900 opacity-60' : '' }}
                        {{ $day['isToday'] ? 'bg-primary-50 dark:bg-primary-900/20' : '' }}
                        {{ $day['isWeekend'] && !$day['isPast'] ? 'bg-orange-50 dark:bg-orange-900/10' : '' }}
                        {{ $isSelected ? 'ring-2 ring-primary-500 ring-inset' : '' }}"
                    wire:click="toggleDateSelection('{{ $day['date'] }}')"
                >
                    {{-- Day Number --}}
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold {{ $day['isToday'] ? 'text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300' }}">
                            {{ $day['day'] }}
                        </span>
                        @if($isSelected)
                            <x-heroicon-s-check-circle class="w-4 h-4 text-primary-500" />
                        @endif
                    </div>

                    {{-- Time Slots --}}
                    <div class="space-y-1">
                        @foreach($day['slots'] as $slotKey => $slot)
                            @php
                                $slotColor = 'bg-green-500';
                                if (!$slot['available']) {
                                    $slotColor = match($slot['reason']) {
                                        'booked' => 'bg-blue-500',
                                        'maintenance' => 'bg-yellow-500',
                                        default => 'bg-red-500',
                                    };
                                }
                                if ($day['isPast']) {
                                    $slotColor = 'bg-gray-400';
                                }
                                
                                $slotLabels = [
                                    'morning' => app()->getLocale() === 'ar' ? 'ص' : 'M',
                                    'afternoon' => app()->getLocale() === 'ar' ? 'ظ' : 'A',
                                    'evening' => app()->getLocale() === 'ar' ? 'م' : 'E',
                                    'full_day' => app()->getLocale() === 'ar' ? 'ك' : 'F',
                                ];
                                
                                $slotFullNames = [
                                    'morning' => app()->getLocale() === 'ar' ? 'صباحي (8:00 ص - 12:00 م)' : 'Morning (8:00 AM - 12:00 PM)',
                                    'afternoon' => app()->getLocale() === 'ar' ? 'ظهري (1:00 م - 5:00 م)' : 'Afternoon (1:00 PM - 5:00 PM)',
                                    'evening' => app()->getLocale() === 'ar' ? 'مسائي (6:00 م - 11:00 م)' : 'Evening (6:00 PM - 11:00 PM)',
                                    'full_day' => app()->getLocale() === 'ar' ? 'يوم كامل' : 'Full Day',
                                ];
                            @endphp
                            <button
                                type="button"
                                wire:click.stop="toggleSlot('{{ $day['date'] }}', '{{ $slotKey }}')"
                                class="w-full px-1 py-0.5 text-xs font-medium text-white rounded transition-opacity hover:opacity-80 {{ $slotColor }} {{ $day['isPast'] ? 'cursor-not-allowed' : '' }}"
                                {{ $day['isPast'] ? 'disabled' : '' }}
                                title="{{ $slotFullNames[$slotKey] ?? $slotKey }}{{ $slot['custom_price'] ? ' - OMR ' . number_format($slot['custom_price'], 3) : '' }}"
                            >
                                <span class="flex items-center justify-between">
                                    <span>{{ $slotLabels[$slotKey] }}</span>
                                    @if($slot['custom_price'])
                                        <span class="text-[10px]">{{ number_format($slot['custom_price'], 0) }}</span>
                                    @endif
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Empty cells for days after the last of the month --}}
            @php
                $lastDayOfMonth = $this->calendarData->last()['dayOfWeek'] ?? 6;
            @endphp
            @for($i = $lastDayOfMonth + 1; $i < 7; $i++)
                <div class="min-h-[120px] p-2 border-b border-r border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900"></div>
            @endfor
        </div>
    </div>

    {{-- Slot Selection for Bulk Operations --}}
    <div class="mt-6 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('owner.availability.slot_filter') }}</h3>
        <div class="flex flex-wrap gap-3">
            @php
                $timeSlots = [
                    'morning' => app()->getLocale() === 'ar' ? 'صباحاً (8 ص - 12 م)' : 'Morning (8 AM - 12 PM)',
                    'afternoon' => app()->getLocale() === 'ar' ? 'ظهراً (1 م - 5 م)' : 'Afternoon (1 PM - 5 PM)',
                    'evening' => app()->getLocale() === 'ar' ? 'مساءً (6 م - 11 م)' : 'Evening (6 PM - 11 PM)',
                    'full_day' => app()->getLocale() === 'ar' ? 'يوم كامل' : 'Full Day',
                ];
            @endphp
            @foreach($timeSlots as $slotKey => $slotLabel)
                <label class="inline-flex items-center cursor-pointer">
                    <input 
                        type="checkbox" 
                        value="{{ $slotKey }}"
                        wire:model.live="selectedSlots"
                        class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-700"
                    >
                    <span class="ml-2 rtl:mr-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ $slotLabel }}
                    </span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mt-6 flex flex-wrap gap-3">
        <x-filament::button
            wire:click="selectAllDates"
            color="gray"
            size="sm"
            icon="heroicon-o-check"
        >
            {{ __('owner.availability.select_all_future') }}
        </x-filament::button>
    </div>

    {{-- Pricing Info --}}
    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('owner.availability.pricing_info') }}</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-3 bg-white dark:bg-gray-800 rounded-lg">
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('owner.availability.base_price') }}</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ number_format($record->price_per_slot, 3) }} {{ __('OMR') }}
                </p>
            </div>
            @if($record->pricing_override)
                @php
                    $slotNames = [
                        'morning' => app()->getLocale() === 'ar' ? 'صباحاً' : 'Morning',
                        'afternoon' => app()->getLocale() === 'ar' ? 'ظهراً' : 'Afternoon',
                        'evening' => app()->getLocale() === 'ar' ? 'مساءً' : 'Evening',
                        'full_day' => app()->getLocale() === 'ar' ? 'يوم كامل' : 'Full Day',
                    ];
                @endphp
                @foreach($record->pricing_override as $slot => $price)
                    @if($price)
                        <div class="text-center p-3 bg-white dark:bg-gray-800 rounded-lg">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $slotNames[$slot] ?? $slot }}
                            </p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ number_format((float) $price, 3) }} {{ __('OMR') }}
                            </p>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</x-filament-panels::page>
