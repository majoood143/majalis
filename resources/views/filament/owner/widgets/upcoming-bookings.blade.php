<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('owner.widgets.upcoming_bookings') }}
        </x-slot>

        <x-slot name="headerEnd">
            <x-filament::badge color="info">
                {{ $bookings->count() }} {{ __('owner.widgets.bookings') }}
            </x-filament::badge>
        </x-slot>

        @if ($todayBookings->isNotEmpty())
            <div class="mb-4">
                <h3 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    {{ __('owner.widgets.today') }} - {{ now()->format('l, M j') }}
                </h3>
                <div class="space-y-2">
                    @foreach ($todayBookings as $booking)
                        <div class="p-3 border-l-4 rounded-lg bg-primary-50 dark:bg-primary-900/20 border-primary-500">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="font-semibold text-primary-700 dark:text-primary-400">
                                        {{-- {{ $booking->hall->name[app()->getLocale()] ?? $booking->hall->name['en'] }} --}}
                                        {{ is_array($booking->hall->name) ? $booking->hall->name[app()->getLocale()] ?? $booking->hall->name['en'] : $booking->hall->name }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="inline-flex items-center gap-1">
                                            <x-heroicon-m-clock class="w-4 h-4" />
                                            {{ __("owner.slots.{$booking->time_slot}") }}
                                        </span>
                                        <span class="mx-2">•</span>
                                        <!-- Replace customer references with correct field names -->
                                        <span class="inline-flex items-center gap-1">
                                            <x-heroicon-m-user class="w-4 h-4" />
                                            {{ $booking->user->name ?? $booking->customer_name }} {{-- ✅ Fixed --}}
                                        </span>
                                    </div>
                                </div>
                                <x-filament::badge color="success">
                                    OMR {{ number_format($booking->total_amount, 3) }}
                                </x-filament::badge>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($tomorrowBookings->isNotEmpty())
            <div class="mb-4">
                <h3 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    {{ __('owner.widgets.tomorrow') }} - {{ now()->addDay()->format('l, M j') }}
                </h3>
                <div class="space-y-2">
                    @foreach ($tomorrowBookings as $booking)
                        <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/20">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="font-semibold text-gray-700 dark:text-gray-300">
                                        {{-- {{ $booking->hall->name[app()->getLocale()] ?? $booking->hall->name['en'] }} --}}
                                        {{ is_array($booking->hall->name) ? $booking->hall->name[app()->getLocale()] ?? $booking->hall->name['en'] : $booking->hall->name }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="inline-flex items-center gap-1">
                                            <x-heroicon-m-clock class="w-4 h-4" />
                                            {{ __("owner.slots.{$booking->time_slot}") }}
                                        </span>
                                        <span class="mx-2">•</span>
                                        <!-- Replace customer references with correct field names -->
                                        <span class="inline-flex items-center gap-1">
                                            <x-heroicon-m-user class="w-4 h-4" />
                                            {{ $booking->user->name ?? $booking->customer_name }} {{-- ✅ Fixed --}}
                                        </span>
                                    </div>
                                </div>
                                <x-filament::badge color="gray">
                                    OMR {{ number_format($booking->total_amount, 3) }}
                                </x-filament::badge>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($bookings->isEmpty())
            <div class="py-6 text-center">
                <x-heroicon-o-calendar-days class="w-12 h-12 mx-auto mb-3 text-gray-400" />
                <p class="text-gray-500 dark:text-gray-400">
                    {{ __('owner.widgets.no_upcoming_bookings') }}
                </p>
            </div>
        @endif

        <div class="pt-4 mt-4 border-t">
            <x-filament::link href="{{ route('filament.owner.resources.bookings.index') }}" color="primary"
                icon="heroicon-m-arrow-right" icon-position="after">
                {{ __('owner.widgets.view_all_bookings') }}
            </x-filament::link>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
