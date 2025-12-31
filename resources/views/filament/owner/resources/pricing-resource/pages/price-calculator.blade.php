<x-filament-panels::page>
    {{-- Selection Panel --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            {{ __('owner.pricing.calculator.select_options') }}
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Hall Selection --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('owner.pricing.fields.hall') }}
                </label>
                <select
                    wire:model.live="selectedHallId"
                    wire:change="setHall($event.target.value)"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-primary-500 focus:border-primary-500"
                >
                    <option value="">{{ __('owner.pricing.calculator.select_hall') }}</option>
                    @foreach($this->getOwnerHalls as $hall)
                        <option value="{{ $hall->id }}">
                            {{ $hall->getTranslation('name', app()->getLocale()) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Date Selection --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('owner.pricing.fields.date') }}
                </label>
                <div class="flex items-center gap-2">
                    <button
                        wire:click="previousDay"
                        type="button"
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        <x-heroicon-o-chevron-left class="w-4 h-4 text-gray-500 rtl:rotate-180" />
                    </button>
                    <input
                        type="date"
                        wire:model.live="selectedDate"
                        wire:change="setDate($event.target.value)"
                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-primary-500 focus:border-primary-500"
                    >
                    <button
                        wire:click="nextDay"
                        type="button"
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-500 rtl:rotate-180" />
                    </button>
                </div>
            </div>

            {{-- Time Slot Selection --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('owner.pricing.fields.time_slot') }}
                </label>
                <select
                    wire:model.live="selectedSlot"
                    wire:change="setSlot($event.target.value)"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-primary-500 focus:border-primary-500"
                >
                    <option value="morning">{{ __('owner.slots.morning') }}</option>
                    <option value="afternoon">{{ __('owner.slots.afternoon') }}</option>
                    <option value="evening">{{ __('owner.slots.evening') }}</option>
                    <option value="full_day">{{ __('owner.slots.full_day') }}</option>
                </select>
            </div>

            {{-- Today Button --}}
            <div class="flex items-end">
                <button
                    wire:click="goToToday"
                    type="button"
                    class="w-full px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
                >
                    {{ __('owner.availability.today') }}
                </button>
            </div>
        </div>
    </div>

    @if($this->pricingBreakdown)
        {{-- Main Pricing Breakdown --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Pricing Details --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <x-heroicon-o-calculator class="w-5 h-5 text-primary-500" />
                    {{ __('owner.pricing.calculator.breakdown') }}
                </h3>

                {{-- Date Info --}}
                <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $this->pricingBreakdown['date_info']['day_name'] }}, {{ $this->pricingBreakdown['date_info']['formatted'] }}
                        </span>
                        @if($this->pricingBreakdown['date_info']['is_weekend'])
                            <span class="px-2 py-1 text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 rounded-full">
                                {{ __('owner.pricing.calculator.weekend') }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Base Price --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('owner.pricing.calculator.base_price') }}
                        </span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ number_format($this->pricingBreakdown['base_price'], 3) }} OMR
                        </span>
                    </div>

                    {{-- Slot Override (if different from base) --}}
                    @if($this->pricingBreakdown['slot_override'] && $this->pricingBreakdown['slot_override'] != $this->pricingBreakdown['base_price'])
                        <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('owner.pricing.calculator.slot_price') }} ({{ __("owner.slots.{$selectedSlot}") }})
                            </span>
                            <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                {{ number_format((float) $this->pricingBreakdown['slot_override'], 3) }} OMR
                            </span>
                        </div>
                    @endif

                    {{-- Custom Date Price --}}
                    @if($this->pricingBreakdown['custom_price'])
                        <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-1">
                                <x-heroicon-o-calendar class="w-4 h-4" />
                                {{ __('owner.pricing.calculator.custom_date_price') }}
                            </span>
                            <span class="text-sm font-medium text-purple-600 dark:text-purple-400">
                                {{ number_format($this->pricingBreakdown['custom_price'], 3) }} OMR
                            </span>
                        </div>
                    @endif

                    {{-- Applied Rules --}}
                    @if(count($this->pricingBreakdown['rules_applied']) > 0)
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('owner.pricing.calculator.rules_applied') }}
                            </h4>
                            @foreach($this->pricingBreakdown['rules_applied'] as $rule)
                                <div class="flex items-center justify-between py-2 px-3 mb-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <div class="flex-1">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $rule['name'] }}
                                        </span>
                                        <span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-{{ match($rule['type']) {
                                            'weekend' => 'amber',
                                            'holiday' => 'red',
                                            'seasonal' => 'blue',
                                            default => 'gray'
                                        } }}-100 text-{{ match($rule['type']) {
                                            'weekend' => 'amber',
                                            'holiday' => 'red',
                                            'seasonal' => 'blue',
                                            default => 'gray'
                                        } }}-800 dark:bg-{{ match($rule['type']) {
                                            'weekend' => 'amber',
                                            'holiday' => 'red',
                                            'seasonal' => 'blue',
                                            default => 'gray'
                                        } }}-900/30 dark:text-{{ match($rule['type']) {
                                            'weekend' => 'amber',
                                            'holiday' => 'red',
                                            'seasonal' => 'blue',
                                            default => 'gray'
                                        } }}-400">
                                            {{ $rule['adjustment'] }}
                                        </span>
                                    </div>
                                    <span class="text-sm font-medium {{ $rule['difference'] >= 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                        {{ $rule['difference'] >= 0 ? '+' : '' }}{{ number_format($rule['difference'], 3) }} OMR
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('owner.pricing.calculator.no_rules_applied') }}
                            </span>
                        </div>
                    @endif

                    {{-- Final Price --}}
                    <div class="mt-4 pt-4 border-t-2 border-primary-200 dark:border-primary-800">
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ __('owner.pricing.calculator.final_price') }}
                            </span>
                            <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                {{ number_format($this->pricingBreakdown['final_price'], 3) }} OMR
                            </span>
                        </div>
                        @if(abs($this->pricingBreakdown['total_adjustment']) > 0.001)
                            <div class="mt-1 text-right">
                                <span class="text-sm {{ $this->pricingBreakdown['total_adjustment'] >= 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                    ({{ $this->pricingBreakdown['total_adjustment'] >= 0 ? '+' : '' }}{{ number_format($this->pricingBreakdown['adjustment_percentage'], 1) }}% {{ __('owner.pricing.calculator.from_base') }})
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Slot Comparison --}}
            @if($this->slotComparison)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <x-heroicon-o-clock class="w-5 h-5 text-primary-500" />
                        {{ __('owner.pricing.calculator.slot_comparison') }}
                    </h3>

                    <div class="space-y-3">
                        @foreach($this->slotComparison as $slot => $data)
                            <div 
                                class="p-3 rounded-lg border-2 cursor-pointer transition-all
                                    {{ $selectedSlot === $slot 
                                        ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' 
                                        : 'border-gray-200 dark:border-gray-700 hover:border-primary-300' }}"
                                wire:click="setSlot('{{ $slot }}')"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ __("owner.slots.{$slot}") }}
                                        </span>
                                        @if(!$data['is_available'])
                                            <span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                {{ __('owner.availability.blocked') }}
                                            </span>
                                        @endif
                                        @if($data['has_custom'])
                                            <span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                                {{ __('owner.pricing.calculator.custom') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-bold text-gray-900 dark:text-white">
                                            {{ number_format($data['final_price'], 3) }} OMR
                                        </span>
                                        @if($data['rules_count'] > 0)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $data['rules_count'] }} {{ trans_choice('owner.pricing.calculator.rules', $data['rules_count']) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Week Preview --}}
        @if($this->weekPreview)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <x-heroicon-o-calendar-days class="w-5 h-5 text-primary-500" />
                    {{ __('owner.pricing.calculator.week_preview') }} - {{ __("owner.slots.{$selectedSlot}") }}
                </h3>

                <div class="grid grid-cols-7 gap-2">
                    @foreach($this->weekPreview as $day)
                        <div 
                            class="p-2 sm:p-3 rounded-lg text-center cursor-pointer transition-all
                                {{ $day['date'] === $selectedDate 
                                    ? 'bg-primary-100 dark:bg-primary-900/30 ring-2 ring-primary-500' 
                                    : ($day['is_weekend'] 
                                        ? 'bg-amber-50 dark:bg-amber-900/20' 
                                        : 'bg-gray-50 dark:bg-gray-700/50') }}
                                {{ $day['is_today'] ? 'ring-2 ring-green-500' : '' }}
                                hover:bg-primary-50 dark:hover:bg-primary-900/20"
                            wire:click="setDate('{{ $day['date'] }}')"
                        >
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                {{ $day['day_name'] }}
                            </div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white mb-1">
                                {{ $day['day_number'] }}
                            </div>
                            <div class="text-xs sm:text-sm font-semibold {{ $day['has_rules'] ? 'text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300' }}">
                                {{ number_format($day['final_price'], 0) }}
                            </div>
                            @if($day['has_rules'])
                                <div class="mt-1">
                                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 flex flex-wrap gap-3 text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded bg-amber-100 dark:bg-amber-900/30"></span>
                        {{ __('owner.pricing.calculator.weekend') }}
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                        {{ __('owner.pricing.calculator.has_rules') }}
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded ring-2 ring-green-500 bg-transparent"></span>
                        {{ __('owner.availability.today') }}
                    </div>
                </div>
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 text-center">
            <x-heroicon-o-calculator class="w-16 h-16 mx-auto text-gray-400 mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                {{ __('owner.pricing.calculator.empty_title') }}
            </h3>
            <p class="text-gray-500 dark:text-gray-400">
                {{ __('owner.pricing.calculator.empty_description') }}
            </p>
        </div>
    @endif
</x-filament-panels::page>
