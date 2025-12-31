<x-filament-panels::page>
    {{-- Hall Selector & View Mode Toggle --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            {{-- Hall Selector --}}
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('owner.features.manage.select_hall') }}
                </label>
                <div class="flex flex-wrap gap-2">
                    @foreach($this->getOwnerHalls as $hall)
                        <button
                            wire:click="setHall({{ $hall->id }})"
                            type="button"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ $selectedHallId === $hall->id
                                    ? 'bg-primary-600 text-white'
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                        >
                            {{ $hall->getTranslation('name', app()->getLocale()) }}
                            <span class="ml-1 text-xs opacity-75">
                                ({{ count($hall->features ?? []) }})
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- View Mode Toggle --}}
            <div class="flex items-center gap-2">
                <button
                    wire:click="toggleViewMode"
                    type="button"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                >
                    @if($viewMode === 'single')
                        <x-heroicon-o-table-cells class="w-4 h-4" />
                        {{ __('owner.features.manage.matrix_view') }}
                    @else
                        <x-heroicon-o-squares-2x2 class="w-4 h-4" />
                        {{ __('owner.features.manage.single_view') }}
                    @endif
                </button>
            </div>
        </div>
    </div>

    @if($viewMode === 'single' && $this->selectedHall)
        {{-- Single Hall View --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Feature Selection Grid --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('owner.features.manage.available_features') }}
                    </h3>
                    <div class="flex items-center gap-2">
                        <button
                            wire:click="addAllFeatures"
                            type="button"
                            class="text-sm text-success-600 hover:text-success-700 dark:text-success-400"
                        >
                            {{ __('owner.features.actions.select_all') }}
                        </button>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <button
                            wire:click="removeAllFeatures"
                            type="button"
                            class="text-sm text-danger-600 hover:text-danger-700 dark:text-danger-400"
                        >
                            {{ __('owner.features.actions.deselect_all') }}
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($this->getAllFeatures as $feature)
                        @php
                            $isSelected = in_array($feature->id, $this->hallFeatures);
                            $icon = $feature->icon ?? 'heroicon-o-check-badge';
                            if (!str_starts_with($icon, 'heroicon-')) {
                                $icon = 'heroicon-o-' . $icon;
                            }
                        @endphp
                        <button
                            wire:click="toggleFeature({{ $feature->id }})"
                            type="button"
                            class="flex items-center gap-3 p-3 rounded-xl border-2 transition-all text-left
                                {{ $isSelected
                                    ? 'border-success-500 bg-success-50 dark:bg-success-900/20'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-700' }}"
                        >
                            {{-- Icon --}}
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center
                                {{ $isSelected
                                    ? 'bg-success-100 dark:bg-success-900/30'
                                    : 'bg-gray-100 dark:bg-gray-700' }}">
                                <x-dynamic-component 
                                    :component="$icon" 
                                    class="w-5 h-5 {{ $isSelected ? 'text-success-600 dark:text-success-400' : 'text-gray-500 dark:text-gray-400' }}" 
                                />
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-sm text-gray-900 dark:text-white truncate">
                                    {{ $feature->getTranslation('name', app()->getLocale()) }}
                                </div>
                                @if($feature->getTranslation('description', app()->getLocale()))
                                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                        {{ Str::limit($feature->getTranslation('description', app()->getLocale()), 40) }}
                                    </div>
                                @endif
                            </div>

                            {{-- Checkbox indicator --}}
                            <div class="flex-shrink-0">
                                @if($isSelected)
                                    <x-heroicon-s-check-circle class="w-6 h-6 text-success-500" />
                                @else
                                    <div class="w-6 h-6 rounded-full border-2 border-gray-300 dark:border-gray-600"></div>
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Sidebar: Stats & Actions --}}
            <div class="space-y-6">
                {{-- Current Hall Stats --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ $this->selectedHall->getTranslation('name', app()->getLocale()) }}
                    </h3>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('owner.features.stats.selected') }}
                            </span>
                            <span class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                {{ count($this->hallFeatures) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('owner.features.stats.available') }}
                            </span>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $this->getAllFeatures->count() }}
                            </span>
                        </div>
                        <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('owner.features.stats.coverage') }}
                                </span>
                                <span class="text-lg font-bold text-success-600 dark:text-success-400">
                                    {{ $this->getAllFeatures->count() > 0 
                                        ? round((count($this->hallFeatures) / $this->getAllFeatures->count()) * 100) 
                                        : 0 }}%
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div 
                                class="bg-success-500 h-2 rounded-full transition-all duration-300"
                                style="width: {{ $this->getAllFeatures->count() > 0 
                                    ? (count($this->hallFeatures) / $this->getAllFeatures->count()) * 100 
                                    : 0 }}%"
                            ></div>
                        </div>
                    </div>
                </div>

                {{-- Copy Features --}}
                @if($this->getOwnerHalls->count() > 1)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-6">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                            {{ __('owner.features.actions.copy_from') }}
                        </h4>
                        <div class="space-y-2">
                            @foreach($this->getOwnerHalls as $hall)
                                @if($hall->id !== $selectedHallId)
                                    <button
                                        wire:click="copyFeaturesFrom({{ $hall->id }})"
                                        type="button"
                                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-left"
                                    >
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ $hall->getTranslation('name', app()->getLocale()) }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ count($hall->features ?? []) }} {{ __('owner.features.items') }}
                                        </span>
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Legend --}}
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('owner.features.legend.title') }}
                    </h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2">
                            <x-heroicon-s-check-circle class="w-5 h-5 text-success-500" />
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ __('owner.features.legend.selected') }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 dark:border-gray-600"></div>
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ __('owner.features.legend.not_selected') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif($viewMode === 'matrix')
        {{-- Matrix View --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider sticky left-0 bg-gray-50 dark:bg-gray-700/50 z-10">
                                {{ __('owner.features.manage.feature') }}
                            </th>
                            @foreach($this->getOwnerHalls as $hall)
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                    {{ $hall->getTranslation('name', app()->getLocale()) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->getAllFeatures as $feature)
                            @php
                                $icon = $feature->icon ?? 'heroicon-o-check-badge';
                                if (!str_starts_with($icon, 'heroicon-')) {
                                    $icon = 'heroicon-o-' . $icon;
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3 sticky left-0 bg-white dark:bg-gray-800 z-10">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                            <x-dynamic-component :component="$icon" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $feature->getTranslation('name', app()->getLocale()) }}
                                        </span>
                                    </div>
                                </td>
                                @foreach($this->getOwnerHalls as $hall)
                                    @php
                                        $isChecked = isset($this->featureMatrix[$hall->id]['features'][$feature->id]) 
                                            && $this->featureMatrix[$hall->id]['features'][$feature->id];
                                    @endphp
                                    <td class="px-4 py-3 text-center">
                                        <button
                                            wire:click="toggleMatrixFeature({{ $hall->id }}, {{ $feature->id }})"
                                            type="button"
                                            class="mx-auto w-8 h-8 rounded-lg flex items-center justify-center transition-colors
                                                {{ $isChecked
                                                    ? 'bg-success-100 dark:bg-success-900/30 text-success-600 dark:text-success-400'
                                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                                        >
                                            @if($isChecked)
                                                <x-heroicon-s-check class="w-5 h-5" />
                                            @else
                                                <x-heroicon-o-plus class="w-4 h-4" />
                                            @endif
                                        </button>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        {{-- No Hall Selected --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 text-center">
            <x-heroicon-o-building-office-2 class="w-16 h-16 mx-auto text-gray-400 mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                {{ __('owner.features.manage.no_hall_selected') }}
            </h3>
            <p class="text-gray-500 dark:text-gray-400">
                {{ __('owner.features.manage.select_hall_prompt') }}
            </p>
        </div>
    @endif
</x-filament-panels::page>
