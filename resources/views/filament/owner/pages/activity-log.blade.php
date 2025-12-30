{{-- resources/views/filament/owner/pages/activity-log.blade.php --}}
<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('Activity Log') }}
        </x-slot>

        <x-slot name="headerEnd">
            <div class="flex items-center gap-4">
                <x-filament::badge color="blue" icon="heroicon-o-calendar-days">
                    {{ $stats['today'] }} {{ __('Today') }}
                </x-filament::badge>
                <x-filament::badge color="green" icon="heroicon-o-calendar-days">
                    {{ $stats['week'] }} {{ __('This Week') }}
                </x-filament::badge>
                <x-filament::badge color="purple" icon="heroicon-o-calendar-days">
                    {{ $stats['month'] }} {{ __('This Month') }}
                </x-filament::badge>
            </div>
        </x-slot>

        <div class="mb-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('Showing :count of :total activities', ['count' => min($perPage, $totalActivities), 'total' => $totalActivities]) }}
            </p>
        </div>

        <div class="space-y-6">
            @forelse($groupedActivities as $date => $dateActivities)
                <div>
                    <h4 class="pb-2 mb-3 text-sm font-semibold text-gray-700 border-b dark:text-gray-300">
                        {{ $date }}
                    </h4>

                    <div class="space-y-3">
                        @foreach($dateActivities as $activity)
                            <div class="flex items-start gap-3 p-3 transition border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-900/50 dark:border-gray-800">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-{{ $activity->color }}-100 dark:bg-{{ $activity->color }}-900/30 flex items-center justify-center">
                                        <x-dynamic-component
                                            :component="$activity->icon"
                                            class="w-5 h-5 text-{{ $activity->color }}-600 dark:text-{{ $activity->color }}-400"
                                        />
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $activity->description }}
                                    </p>

                                    @if($activity->details)
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $activity->details }}
                                        </p>
                                    @endif

                                    <div class="flex items-center gap-3 mt-2">
                                        <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                            <x-heroicon-o-clock class="w-3 h-3" />
                                            <span>{{ $activity->time }}</span>
                                        </div>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">•</span>
                                        <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                            <x-heroicon-o-user class="w-3 h-3" />
                                            <span>{{ $activity->causer }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="py-12 text-center">
                    <x-heroicon-o-clock class="w-16 h-16 mx-auto mb-4 text-gray-400" />
                    <h3 class="mb-2 text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('No activities found') }}
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        {{ __('Activities will appear here when actions are performed in the system.') }}
                    </p>
                </div>
            @endforelse
        </div>

        @if($hasMore)
            <div class="pt-6 mt-6 border-t">
                <div class="flex justify-center">
                    <x-filament::button
                        wire:click="loadMore"
                        icon="heroicon-m-arrow-down"
                        color="gray"
                        size="sm"
                    >
                        {{ __('Load More Activities') }}
                    </x-filament::button>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
