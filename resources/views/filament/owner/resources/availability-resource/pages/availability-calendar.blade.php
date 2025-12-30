<x-filament-panels::page>
    {{-- Hall Filter & Navigation --}}
    <div class="mb-6 space-y-4">
        {{-- Hall Selector --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                    {{ __('owner.availability_resource.calendar.select_hall') }}
                </label>
                <select 
                    wire:model.live="selectedHallId"
                    class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-primary-500 focus:border-primary-500"
                >
                    <option value="">{{ __('owner.availability_resource.calendar.all_halls') }}</option>
                    @foreach($this->getOwnerHalls as $hall)
                        <option value="{{ $hall->id }}">
                            {{ $hall->getTranslation('name', app()->getLocale()) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Calendar Navigation --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <div class="flex items-center justify-between">
                {{-- Previous Month --}}
                <button
                    wire:click="previousMonth"
                    type="button"
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                >
                    <x-heroicon-o-chevron-left class="w-5 h-5 text-gray-600 dark:text-gray-400 rtl:rotate-180" />
                </button>

                {{-- Month/Year Display --}}
                <div class="flex items-center gap-2">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                        {{ $this->monthName }}
                    </h2>
                    <button
                        wire:click="goToToday"
                        type="button"
                        class="px-2 py-1 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors"
                    >
                        {{ __('owner.availability.today') }}
                    </button>
                </div>

                {{-- Next Month --}}
                <button
                    wire:click="nextMonth"
                    type="button"
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                >
                    <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-600 dark:text-gray-400 rtl:rotate-180" />
                </button>
            </div>
        </div>
    </div>

    {{-- Legend --}}
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
            {{ __('owner.availability.legend') }}
        </h3>
        <div class="flex flex-wrap gap-3 sm:gap-4">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 sm:w-4 sm:h-4 rounded-full bg-green-500"></span>
                <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ __('owner.availability.available') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 sm:w-4 sm:h-4 rounded-full bg-red-500"></span>
                <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ __('owner.availability.blocked') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 sm:w-4 sm:h-4 rounded-full bg-blue-500"></span>
                <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ __('owner.availability.booked') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 sm:w-4 sm:h-4 rounded-full bg-yellow-500"></span>
                <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ __('owner.availability.maintenance') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 sm:w-4 sm:h-4 rounded-full bg-gray-400"></span>
                <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ __('owner.availability.past') }}</span>
            </div>
        </div>
    </div>

    {{-- Bulk Operations Panel (when dates selected) --}}
    @if(count($selectedDates) > 0 && $selectedHallId)
        <div class="mb-6 bg-primary-50 dark:bg-primary-900/20 rounded-xl border border-primary-200 dark:border-primary-800 p-4">
            {{-- Selection Info --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <div class="flex items-center gap-2">
                    <x-heroicon-s-check-circle class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                    <span class="text-sm font-medium text-primary-700 dark:text-primary-300">
                        {{ __('owner.availability.dates_selected', ['count' => count($selectedDates)]) }}
                    </span>
                </div>
                <button
                    wire:click="clearSelection"
                    type="button"
                    class="text-sm text-primary-600 dark:text-primary-400 hover:underline"
                >
                    {{ __('owner.availability.clear_selection') }}
                </button>
            </div>

            {{-- Time Slot Selection --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('owner.availability.time_slots') }}
                </label>
                <div class="flex flex-wrap gap-2">
                    @php
                        $slotOptions = [
                            'morning' => __('owner.slots.morning'),
                            'afternoon' => __('owner.slots.afternoon'),
                            'evening' => __('owner.slots.evening'),
                            'full_day' => __('owner.slots.full_day'),
                        ];
                    @endphp
                    @foreach($slotOptions as $slotKey => $slotLabel)
                        <label class="inline-flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <input 
                                type="checkbox" 
                                value="{{ $slotKey }}"
                                wire:model.live="selectedSlots"
                                class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500"
                            >
                            <span class="text-xs sm:text-sm text-gray-700 dark:text-gray-300">{{ $slotLabel }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Block Reason --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('owner.availability.block_reason') }}
                </label>
                <select 
                    wire:model="blockReason" 
                    class="w-full sm:w-auto rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"
                >
                    <option value="blocked">{{ __('owner.availability.reasons.blocked') }}</option>
                    <option value="maintenance">{{ __('owner.availability.reasons.maintenance') }}</option>
                    <option value="holiday">{{ __('owner.availability.reasons.holiday') }}</option>
                    <option value="private_event">{{ __('owner.availability.reasons.private_event') }}</option>
                    <option value="renovation">{{ __('owner.availability.reasons.renovation') }}</option>
                    <option value="other">{{ __('owner.availability.reasons.other') }}</option>
                </select>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap gap-2 mb-4">
                <button
                    wire:click="blockSelected"
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
                >
                    <x-heroicon-o-x-circle class="w-4 h-4" />
                    {{ __('owner.availability.block_selected') }}
                </button>
                
                <button
                    wire:click="unblockSelected"
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors"
                >
                    <x-heroicon-o-check-circle class="w-4 h-4" />
                    {{ __('owner.availability.unblock_selected') }}
                </button>
            </div>

            {{-- Custom Price Section --}}
            <div class="pt-4 border-t border-primary-200 dark:border-primary-700">
                <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                    <div class="flex-1 max-w-xs">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('owner.availability.custom_price') }}
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 flex items-center pl-3 rtl:pr-3 text-gray-500 dark:text-gray-400 text-sm">
                                OMR
                            </span>
                            <input 
                                type="number" 
                                wire:model="customPriceInput"
                                step="0.001"
                                min="0"
                                placeholder="0.000"
                                class="w-full pl-12 rtl:pr-12 rtl:pl-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"
                            >
                        </div>
                    </div>
                    <button
                        wire:click="setCustomPrice"
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-colors"
                    >
                        <x-heroicon-o-currency-dollar class="w-4 h-4" />
                        {{ __('owner.availability.set_price') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Calendar Grid --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        {{-- Day Headers (Desktop) --}}
        <div class="hidden sm:grid grid-cols-7 bg-gray-50 dark:bg-gray-700/50">
            @foreach($this->dayNames as $dayName)
                <div class="py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600">
                    {{ $dayName['full'] }}
                </div>
            @endforeach
        </div>

        {{-- Day Headers (Mobile) --}}
        <div class="grid grid-cols-7 sm:hidden bg-gray-50 dark:bg-gray-700/50">
            @foreach($this->dayNames as $dayName)
                <div class="py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600">
                    {{ $dayName['short'] }}
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
                <div class="min-h-[80px] sm:min-h-[120px] p-1 sm:p-2 border-b border-r border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30"></div>
            @endfor

            {{-- Calendar Days --}}
            @foreach($this->calendarData as $day)
                @php
                    $isSelected = in_array($day['date'], $selectedDates);
                    $dayData = $day['data'];
                    $isSummaryView = isset($dayData['summary']) && $dayData['summary'];
                @endphp
                
                <div 
                    wire:click="toggleDateSelection('{{ $day['date'] }}')"
                    class="min-h-[80px] sm:min-h-[120px] p-1 sm:p-2 border-b border-r border-gray-200 dark:border-gray-700 cursor-pointer transition-all duration-150
                        {{ $day['isPast'] ? 'bg-gray-100 dark:bg-gray-900/50 opacity-60 cursor-not-allowed' : '' }}
                        {{ $day['isToday'] ? 'bg-primary-50 dark:bg-primary-900/20 ring-2 ring-primary-400 ring-inset' : '' }}
                        {{ $day['isWeekend'] && !$day['isPast'] && !$day['isToday'] ? 'bg-orange-50 dark:bg-orange-900/10' : '' }}
                        {{ $isSelected ? 'bg-primary-100 dark:bg-primary-900/30 ring-2 ring-primary-500 ring-inset' : '' }}
                        {{ !$day['isPast'] ? 'hover:bg-gray-50 dark:hover:bg-gray-700/50' : '' }}"
                >
                    {{-- Day Number --}}
                    <div class="flex items-center justify-between mb-1 sm:mb-2">
                        <span class="text-xs sm:text-sm font-semibold {{ $day['isToday'] ? 'text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300' }}">
                            {{ $day['day'] }}
                        </span>
                        @if($isSelected)
                            <x-heroicon-s-check-circle class="w-4 h-4 text-primary-500" />
                        @endif
                    </div>

                    @if($isSummaryView)
                        {{-- Multi-hall Summary View --}}
                        <div class="space-y-1">
                            @if($dayData['available'] > 0)
                                <div class="flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                    <span class="text-[10px] sm:text-xs text-gray-600 dark:text-gray-400">{{ $dayData['available'] }}</span>
                                </div>
                            @endif
                            @if($dayData['booked'] > 0)
                                <div class="flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    <span class="text-[10px] sm:text-xs text-gray-600 dark:text-gray-400">{{ $dayData['booked'] }}</span>
                                </div>
                            @endif
                            @if($dayData['blocked'] > 0)
                                <div class="flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                    <span class="text-[10px] sm:text-xs text-gray-600 dark:text-gray-400">{{ $dayData['blocked'] }}</span>
                                </div>
                            @endif
                        </div>
                    @elseif(isset($dayData['slots']))
                        {{-- Single Hall Slots View --}}
                        <div class="space-y-0.5 sm:space-y-1">
                            @php
                                $slotLabels = [
                                    'morning' => app()->getLocale() === 'ar' ? 'ص' : 'M',
                                    'afternoon' => app()->getLocale() === 'ar' ? 'ظ' : 'A',
                                    'evening' => app()->getLocale() === 'ar' ? 'م' : 'E',
                                    'full_day' => app()->getLocale() === 'ar' ? 'ك' : 'F',
                                ];
                            @endphp
                            @foreach($dayData['slots'] as $slotKey => $slot)
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
                                @endphp
                                <button
                                    type="button"
                                    wire:click.stop="toggleSlot('{{ $day['date'] }}', '{{ $slotKey }}')"
                                    class="w-full px-1 py-0.5 text-[10px] sm:text-xs font-medium text-white rounded transition-opacity hover:opacity-80 {{ $slotColor }} {{ $day['isPast'] ? 'cursor-not-allowed' : '' }}"
                                    {{ $day['isPast'] ? 'disabled' : '' }}
                                    title="{{ __('owner.slots.' . $slotKey) }}{{ $slot['custom_price'] ? ' - OMR ' . number_format($slot['custom_price'], 3) : '' }}"
                                >
                                    <span class="flex items-center justify-between">
                                        <span>{{ $slotLabels[$slotKey] }}</span>
                                        @if($slot['custom_price'])
                                            <span class="hidden sm:inline text-[9px]">{{ number_format($slot['custom_price'], 0) }}</span>
                                        @endif
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            {{-- Empty cells for days after the last of the month --}}
            @php
                $lastDayOfMonth = $this->calendarData->last()['dayOfWeek'] ?? 6;
            @endphp
            @for($i = $lastDayOfMonth + 1; $i < 7; $i++)
                <div class="min-h-[80px] sm:min-h-[120px] p-1 sm:p-2 border-b border-r border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30"></div>
            @endfor
        </div>
    </div>

    {{-- Quick Actions (Mobile Fixed Bottom Bar) --}}
    @if($selectedHallId && count($selectedDates) === 0)
        <div class="fixed bottom-0 left-0 right-0 sm:hidden bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 shadow-lg z-50">
            <div class="flex items-center justify-between gap-2">
                <button
                    wire:click="selectAllDates"
                    type="button"
                    class="flex-1 px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg"
                >
                    {{ __('owner.availability.select_all_future') }}
                </button>
            </div>
        </div>
    @endif

    {{-- Pricing Info --}}
    @if($selectedHallId)
        @php
            $selectedHall = $this->getOwnerHalls->firstWhere('id', $selectedHallId);
        @endphp
        @if($selectedHall)
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    {{ __('owner.availability.pricing_info') }} - {{ $selectedHall->getTranslation('name', app()->getLocale()) }}
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('owner.availability.base_price') }}</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ number_format($selectedHall->price_per_slot ?? 0, 3) }} OMR
                        </p>
                    </div>
                    @if($selectedHall->pricing_override)
                        @php
                            $slotNames = [
                                'morning' => __('owner.slots.morning'),
                                'afternoon' => __('owner.slots.afternoon'),
                                'evening' => __('owner.slots.evening'),
                                'full_day' => __('owner.slots.full_day'),
                            ];
                        @endphp
                        @foreach($selectedHall->pricing_override as $slot => $price)
                            @if($price)
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $slotNames[$slot] ?? $slot }}</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ number_format((float) $price, 3) }} OMR
                                    </p>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        @endif
    @endif

    {{-- Mobile Bottom Spacing --}}
    <div class="h-20 sm:h-0"></div>
</x-filament-panels::page>
