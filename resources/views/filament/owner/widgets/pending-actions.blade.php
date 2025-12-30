<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('owner.widgets.pending_actions') }}
        </x-slot>

        <x-slot name="headerEnd">
            @if($urgentActions > 0)
                <x-filament::badge color="danger">
                    {{ $urgentActions }} {{ __('owner.widgets.urgent') }}
                </x-filament::badge>
            @elseif($totalActions > 0)
                <x-filament::badge color="warning">
                    {{ $totalActions }} {{ __('owner.widgets.pending') }}
                </x-filament::badge>
            @else
                <x-filament::badge color="success">
                    {{ __('owner.widgets.all_clear') }}
                </x-filament::badge>
            @endif
        </x-slot>

        @if($actions->isNotEmpty())
            <div class="space-y-3">
                @foreach($actions->take(5) as $action)
                    <div class="border-l-4 border-{{ $action->color }}-500 bg-{{ $action->color }}-50 dark:bg-{{ $action->color }}-900/20 rounded-r-lg p-3">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <x-dynamic-component
                                    :component="$action->icon"
                                    class="w-5 h-5 text-{{ $action->color }}-600 dark:text-{{ $action->color }}-400"
                                />
                            </div>

                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $action->title }}
                                </h4>
                                <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                    {{ $action->description }}
                                </p>

                                <div class="mt-2">
                                    <x-filament::link
                                        href="{{ $action->action_url }}"
                                        color="{{ $action->color }}"
                                        size="xs"
                                    >
                                        {{ __('owner.widgets.take_action') }}
                                    </x-filament::link>
                                </div>
                            </div>

                            @if($action->priority === 'urgent')
                                <x-filament::badge color="danger" size="xs">
                                    {{ __('owner.widgets.urgent') }}
                                </x-filament::badge>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($totalActions > 5)
                <div class="pt-4 mt-4 text-center border-t">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('owner.widgets.more_actions_pending', ['count' => $totalActions - 5]) }}
                    </p>
                </div>
            @endif

            <!-- Action type summary -->
            <div class="pt-4 mt-4 border-t">
                <div class="grid grid-cols-2 gap-2 text-xs">
                    @if($counts['booking_confirmation'] > 0)
                        <div class="flex items-center gap-2">
                            <x-heroicon-m-calendar class="w-4 h-4 text-warning-500" />
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ $counts['booking_confirmation'] }} {{ __('owner.widgets.bookings_to_confirm') }}
                            </span>
                        </div>
                    @endif

                    @if($counts['payment_follow_up'] > 0)
                        <div class="flex items-center gap-2">
                            <x-heroicon-m-banknotes class="w-4 h-4 text-warning-500" />
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ $counts['payment_follow_up'] }} {{ __('owner.widgets.payments_pending') }}
                            </span>
                        </div>
                    @endif

                    @if($counts['review_response'] > 0)
                        <div class="flex items-center gap-2">
                            <x-heroicon-m-star class="w-4 h-4 text-info-500" />
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ $counts['review_response'] }} {{ __('owner.widgets.reviews_to_respond') }}
                            </span>
                        </div>
                    @endif

                    @if($counts['support_ticket'] > 0)
                        <div class="flex items-center gap-2">
                            <x-heroicon-m-chat-bubble-left-right class="w-4 h-4 text-info-500" />
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ $counts['support_ticket'] }} {{ __('owner.widgets.tickets_open') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="py-8 text-center">
                <x-heroicon-o-check-circle class="w-16 h-16 mx-auto mb-3 text-success-500" />
                <h3 class="mb-1 text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('owner.widgets.no_pending_actions') }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('owner.widgets.all_caught_up') }}
                </p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
