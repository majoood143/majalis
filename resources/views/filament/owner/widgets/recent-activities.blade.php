<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('owner.widgets.recent_activities') }}
        </x-slot>

        <x-slot name="headerEnd">
            <div class="flex items-center gap-2">
                <x-filament::badge color="gray">
                    {{ $stats['today'] }} {{ __('owner.widgets.today') }}
                </x-filament::badge>
            </div>
        </x-slot>

        <div class="space-y-4">
            @forelse($groupedActivities as $date => $dateActivities)
                <div>
                    <h4 class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">
                        {{ $date }}
                    </h4>

                    <div class="space-y-2">
                        @foreach($dateActivities as $activity)
                            <div class="flex items-start gap-3 p-2 transition rounded-lg hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full bg-{{ $activity->color }}-100 dark:bg-{{ $activity->color }}-900/30 flex items-center justify-center">
                                        <x-dynamic-component
                                            :component="$activity->icon"
                                            class="w-4 h-4 text-{{ $activity->color }}-600 dark:text-{{ $activity->color }}-400"
                                        />
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $activity->description }}
                                    </p>

                                    @if($activity->details)
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            {{ $activity->details }}
                                        </p>
                                    @endif

                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ $activity->time }}
                                        </span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">•</span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ $activity->causer }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="py-6 text-center">
                    <x-heroicon-o-clock class="w-12 h-12 mx-auto mb-3 text-gray-400" />
                    <p class="text-gray-500 dark:text-gray-400">
                        {{ __('owner.widgets.no_recent_activities') }}
                    </p>
                </div>
            @endforelse
        </div>

        @if($hasMore)
            <div class="pt-4 mt-4 border-t">
                <x-filament::link
                    href="{{ route('filament.owner.pages.activity-log') }}"
                    color="primary"
                    icon="heroicon-m-arrow-right"
                    icon-position="after"
                >
                    {{ __('owner.widgets.view_all_activities') }}
                </x-filament::link>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
