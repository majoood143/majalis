<x-filament-widgets::widget>
    <x-filament::section 
        :heading="__('owner.widgets.pending_actions')"
        :description="__('owner.widgets.pending_actions_description')"
        collapsible
        collapsed
    >
        <x-slot name="headerEnd">
            <div class="flex items-center gap-2">
                @if($urgentActions > 0)
                    <x-filament::badge 
                        color="danger" 
                        icon="heroicon-o-exclamation-triangle"
                        tooltip="{{ __('owner.widgets.urgent_actions_tooltip', ['count' => $urgentActions]) }}"
                    >
                        {{ $urgentActions }} {{ __('owner.widgets.urgent') }}
                    </x-filament::badge>
                @endif
                
                @if($highPriorityActions > $urgentActions)
                    <x-filament::badge 
                        color="warning"
                        icon="heroicon-o-clock"
                        tooltip="{{ __('owner.widgets.high_priority_tooltip', ['count' => $highPriorityActions]) }}"
                    >
                        {{ $highPriorityActions - $urgentActions }} {{ __('owner.widgets.high') }}
                    </x-filament::badge>
                @endif
                
                @if($totalActions > 0)
                    <x-filament::badge 
                        color="gray"
                        icon="heroicon-o-clipboard-document-list"
                        tooltip="{{ __('owner.widgets.total_actions_tooltip', ['count' => $totalActions]) }}"
                    >
                        {{ $totalActions }} {{ __('owner.widgets.total') }}
                    </x-filament::badge>
                @else
                    <x-filament::badge 
                        color="success"
                        icon="heroicon-o-check-circle"
                    >
                        {{ __('owner.widgets.all_clear') }}
                    </x-filament::badge>
                @endif
            </div>
        </x-slot>

        @if($hasData)
            <!-- Quick Stats Bar -->
            @if(isset($stats['total_pending_amount']) && $stats['total_pending_amount'] > 0)
                <div class="mb-4 p-3 bg-gradient-to-r from-warning-50 to-orange-50 dark:from-warning-900/20 dark:to-orange-900/20 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-currency-dollar class="w-5 h-5 text-warning-600" />
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('owner.widgets.pending_revenue') }}:
                            </span>
                        </div>
                        <span class="text-lg font-bold text-warning-700 dark:text-warning-300">
                            {{ number_format($stats['total_pending_amount'], 3) }} KWD
                        </span>
                    </div>
                </div>
            @endif

            <!-- Actions List -->
            <div class="space-y-3 max-h-80 overflow-y-auto pr-2" id="pending-actions-list">
                @foreach($actions->take(8) as $index => $action)
                    <div 
                        class="border-l-4 border-{{ $action->color }}-500 bg-white dark:bg-gray-900 rounded-r-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200 {{ $action->priority === 'urgent' ? 'animate-pulse' : '' }}"
                        x-data="{ expanded: false }"
                    >
                        <div class="flex items-start gap-3">
                            <!-- Priority Indicator -->
                            <div class="flex-shrink-0">
                                <div class="relative">
                                    <x-dynamic-component
                                        :component="$action->icon"
                                        class="w-6 h-6 text-{{ $action->color }}-600 dark:text-{{ $action->color }}-400"
                                    />
                                    @if($action->priority === 'urgent')
                                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-danger-500 rounded-full animate-ping"></div>
                                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-danger-500 rounded-full"></div>
                                    @endif
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                        {{ $action->title }}
                                    </h4>
                                    <div class="flex items-center gap-2">
                                        @if($action->priority === 'urgent')
                                            <x-filament::badge color="danger" size="xs" icon="heroicon-o-exclamation-triangle">
                                                {{ __('owner.widgets.urgent') }}
                                            </x-filament::badge>
                                        @elseif($action->priority === 'high')
                                            <x-filament::badge color="warning" size="xs" icon="heroicon-o-clock">
                                                {{ __('owner.widgets.high') }}
                                            </x-filament::badge>
                                        @endif
                                        
                                        @if(isset($action->due_date))
                                            <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                                {{ $action->due_date->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <p class="mt-1 text-xs text-gray-600 dark:text-gray-400 line-clamp-2">
                                    {{ $action->description }}
                                </p>

                                <!-- Action Buttons -->
                                <div class="mt-3 flex items-center gap-2">
                                    <x-filament::button
                                        href="{{ $action->action_url }}"
                                        color="{{ $action->color }}"
                                        size="xs"
                                        tag="a"
                                        target="{{ str_contains($action->action_url, '#') ? '_self' : '_blank' }}"
                                    >
                                        {{ __('owner.widgets.take_action') }}
                                    </x-filament::button>
                                    
                                    @if(isset($action->data->id))
                                        <x-filament::button
                                            wire:click="$dispatch('markAsDone', { id: {{ $action->data->id }}, type: '{{ $action->type }}' })"
                                            color="gray"
                                            size="xs"
                                            icon="heroicon-o-check"
                                            title="{{ __('owner.widgets.mark_as_done') }}"
                                        />
                                    @endif
                                    
                                    <button 
                                        x-on:click="expanded = !expanded"
                                        class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                                    >
                                        <span x-show="!expanded">{{ __('owner.widgets.more_info') }}</span>
                                        <span x-show="expanded">{{ __('owner.widgets.less_info') }}</span>
                                    </button>
                                </div>

                                <!-- Expanded Details -->
                                <div x-show="expanded" x-collapse class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                    <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                        @if(isset($action->data->created_at))
                                            <p>{{ __('owner.widgets.created') }}: {{ $action->data->created_at->diffForHumans() }}</p>
                                        @endif
                                        @if(isset($action->amount))
                                            <p>{{ __('owner.widgets.amount') }}: {{ number_format($action->amount, 3) }} KWD</p>
                                        @endif
                                        @if(isset($action->rating))
                                            <p>{{ __('owner.widgets.rating') }}: {{ $action->rating }}/5</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination Indicator -->
            @if($totalActions > 8)
                <div class="pt-4 mt-4 text-center border-t border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('owner.widgets.more_actions_pending', ['count' => $totalActions - 8]) }}
                    </p>
                    <x-filament::button 
                        href="{{ route('filament.owner.pages.dashboard') }}?tab=actions"
                        color="gray"
                        size="sm"
                        class="mt-2"
                    >
                        {{ __('owner.widgets.view_all_actions') }}
                    </x-filament::button>
                </div>
            @endif

            <!-- Statistics Footer -->
            <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-xs">
                    @foreach($counts as $type => $count)
                        @if($count > 0)
                            @php
                                $typeConfig = [
                                    'booking_confirmation' => ['icon' => 'calendar', 'color' => 'warning'],
                                    'payment_follow_up' => ['icon' => 'banknotes', 'color' => 'danger'],
                                    'review_response' => ['icon' => 'star', 'color' => 'info'],
                                    'support_ticket' => ['icon' => 'chat-bubble-left-right', 'color' => 'gray'],
                                    'hall_profile' => ['icon' => 'building-office', 'color' => 'success'],
                                    'availability_update' => ['icon' => 'calendar-days', 'color' => 'purple'],
                                ];
                            @endphp
                            <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-50 dark:bg-gray-800">
                                <x-dynamic-component 
                                    :component="'heroicon-m-' . $typeConfig[$type]['icon']" 
                                    class="w-4 h-4 text-{{ $typeConfig[$type]['color'] }}-500" 
                                />
                                <div class="flex-1">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ __("owner.widgets.{$type}") }}
                                    </span>
                                    <span class="ml-2 font-semibold text-{{ $typeConfig[$type]['color'] }}-600 dark:text-{{ $typeConfig[$type]['color'] }}-400">
                                        {{ $count }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    
                    <!-- Additional Stats -->
                    @if(isset($stats['avg_response_hours']) && $stats['avg_response_hours'] > 0)
                        <div class="flex items-center gap-2 p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                            <x-heroicon-m-clock class="w-4 h-4 text-blue-500" />
                            <div class="flex-1">
                                <span class="text-gray-600 dark:text-gray-400">
                                    {{ __('owner.widgets.avg_response') }}
                                </span>
                                <span class="ml-2 font-semibold text-blue-600 dark:text-blue-400">
                                    {{ $stats['avg_response_hours'] }}h
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Refresh Button -->
            <div class="mt-4 text-center">
                <x-filament::button 
                    wire:click="$refresh"
                    color="gray"
                    size="xs"
                    icon="heroicon-o-arrow-path"
                    :loading="true"
                >
                    {{ __('owner.widgets.refresh_actions') }}
                </x-filament::button>
                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('owner.widgets.auto_refresh') }}
                </span>
            </div>
        @else
            <!-- Empty State -->
            <div class="py-10 text-center">
                <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-success-100 dark:bg-success-900/30 flex items-center justify-center">
                    <x-heroicon-o-check-circle class="w-10 h-10 text-success-600 dark:text-success-400" />
                </div>
                <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ __('owner.widgets.no_pending_actions') }}
                </h3>
                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                    {{ __('owner.widgets.all_caught_up_description') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <x-filament::button 
                        href="{{ route('filament.owner.resources.bookings.index') }}"
                        color="primary"
                        size="sm"
                        icon="heroicon-o-calendar"
                    >
                        {{ __('owner.widgets.view_bookings') }}
                    </x-filament::button>
                    <x-filament::button 
                        href="{{ route('filament.owner.resources.halls.index') }}"
                        color="gray"
                        size="sm"
                        icon="heroicon-o-building-office"
                    >
                        {{ __('owner.widgets.manage_halls') }}
                    </x-filament::button>
                </div>
            </div>
        @endif
    </x-filament::section>

    @script
    <script>
        // Auto-scroll to urgent actions
        document.addEventListener('DOMContentLoaded', function() {
            const urgentAction = document.querySelector('.animate-pulse');
            if (urgentAction) {
                urgentAction.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    </script>
    @endscript
</x-filament-widgets::widget>