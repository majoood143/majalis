<x-filament-panels::page>
    {{-- Filter Form --}}
    <div class="mb-6">
        {{ $this->form }}
    </div>

    {{-- Tab Navigation --}}
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8 rtl:space-x-reverse" aria-label="Tabs">
            @foreach ([
                'overview' => ['icon' => 'heroicon-o-squares-2x2', 'label' => __('owner.reports.tabs.overview')],
                'earnings' => ['icon' => 'heroicon-o-banknotes', 'label' => __('owner.reports.tabs.earnings')],
                'bookings' => ['icon' => 'heroicon-o-calendar-days', 'label' => __('owner.reports.tabs.bookings')],
                'halls' => ['icon' => 'heroicon-o-building-office', 'label' => __('owner.reports.tabs.halls')],
            ] as $tab => $data)
                <button
                    wire:click="setActiveTab('{{ $tab }}')"
                    class="group inline-flex items-center border-b-2 py-4 px-1 text-sm font-medium transition-colors
                        {{ $activeTab === $tab
                            ? 'border-primary-500 text-primary-600 dark:text-primary-400'
                            : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-dynamic-component
                        :component="$data['icon']"
                        class="ltr:-ml-0.5 ltr:mr-2 rtl:-mr-0.5 rtl:ml-2 h-5 w-5
                            {{ $activeTab === $tab ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}"
                    />
                    {{ $data['label'] }}
                </button>
            @endforeach
        </nav>
    </div>

    {{-- Tab Content --}}
    <div class="space-y-6">
        {{-- Overview Tab --}}
        @if ($activeTab === 'overview')
            {{-- Main Stats Grid --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Total Earnings --}}
                <x-filament::section>
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-success-100 p-3 dark:bg-success-500/20">
                            <x-heroicon-o-banknotes class="h-6 w-6 text-success-600 dark:text-success-400" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('owner.reports.stats.total_earnings') }}
                            </p>
                            <p class="text-2xl font-bold text-success-600 dark:text-success-400">
                                {{ number_format($this->dashboardStats['total_earnings'], 3) }} OMR
                            </p>
                        </div>
                    </div>
                </x-filament::section>

                {{-- Total Revenue --}}
                <x-filament::section>
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-primary-100 p-3 dark:bg-primary-500/20">
                            <x-heroicon-o-currency-dollar class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('owner.reports.stats.total_revenue') }}
                            </p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ number_format($this->dashboardStats['total_revenue'], 3) }} OMR
                            </p>
                        </div>
                    </div>
                </x-filament::section>

                {{-- Total Bookings --}}
                <x-filament::section>
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-info-100 p-3 dark:bg-info-500/20">
                            <x-heroicon-o-calendar-days class="h-6 w-6 text-info-600 dark:text-info-400" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('owner.reports.stats.total_bookings') }}
                            </p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ number_format($this->dashboardStats['total_bookings']) }}
                            </p>
                        </div>
                    </div>
                </x-filament::section>

                {{-- Pending Payouts --}}
                <x-filament::section>
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-warning-100 p-3 dark:bg-warning-500/20">
                            <x-heroicon-o-clock class="h-6 w-6 text-warning-600 dark:text-warning-400" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('owner.reports.stats.pending_payout') }}
                            </p>
                            <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">
                                {{ number_format($this->dashboardStats['pending_payouts'], 3) }} OMR
                            </p>
                        </div>
                    </div>
                </x-filament::section>
            </div>

            {{-- Quick Stats --}}
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ([
                    ['label' => __('owner.reports.stats.confirmed'), 'value' => $this->dashboardStats['confirmed_bookings'], 'color' => 'success'],
                    ['label' => __('owner.reports.stats.completed'), 'value' => $this->dashboardStats['completed_bookings'], 'color' => 'primary'],
                    ['label' => __('owner.reports.stats.pending'), 'value' => $this->dashboardStats['pending_bookings'], 'color' => 'warning'],
                    ['label' => __('owner.reports.stats.cancelled'), 'value' => $this->dashboardStats['cancelled_bookings'], 'color' => 'danger'],
                    ['label' => __('owner.reports.stats.total_guests'), 'value' => $this->dashboardStats['total_guests'], 'color' => 'info'],
                    ['label' => __('owner.reports.stats.avg_booking'), 'value' => number_format($this->dashboardStats['average_booking_value'], 3), 'color' => 'gray', 'suffix' => ' OMR'],
                ] as $stat)
                    <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                        <p class="mt-1 text-xl font-semibold text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400">
                            {{ is_numeric($stat['value']) ? number_format($stat['value']) : $stat['value'] }}{{ $stat['suffix'] ?? '' }}
                        </p>
                    </div>
                @endforeach
            </div>

            {{-- Monthly Comparison --}}
            @if (!empty($this->monthlyComparison))
                <x-filament::section>
                    <x-slot name="heading">{{ __('owner.reports.sections.monthly_comparison') }}</x-slot>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        {{-- Revenue Change --}}
                        <div class="flex items-center gap-4">
                            <div class="rounded-full p-3 {{ $this->monthlyComparison['revenue_change'] >= 0 ? 'bg-success-100 dark:bg-success-500/20' : 'bg-danger-100 dark:bg-danger-500/20' }}">
                                @if ($this->monthlyComparison['revenue_change'] >= 0)
                                    <x-heroicon-o-arrow-trending-up class="h-6 w-6 text-success-600 dark:text-success-400" />
                                @else
                                    <x-heroicon-o-arrow-trending-down class="h-6 w-6 text-danger-600 dark:text-danger-400" />
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('owner.reports.comparison.earnings_change') }}
                                </p>
                                <p class="text-xl font-bold {{ $this->monthlyComparison['revenue_change'] >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                    {{ $this->monthlyComparison['revenue_change'] >= 0 ? '+' : '' }}{{ $this->monthlyComparison['revenue_change'] }}%
                                </p>
                                <p class="text-xs text-gray-400">{{ __('owner.reports.comparison.vs_last_month') }}</p>
                            </div>
                        </div>

                        {{-- Bookings Change --}}
                        <div class="flex items-center gap-4">
                            <div class="rounded-full p-3 {{ $this->monthlyComparison['bookings_change'] >= 0 ? 'bg-success-100 dark:bg-success-500/20' : 'bg-danger-100 dark:bg-danger-500/20' }}">
                                @if ($this->monthlyComparison['bookings_change'] >= 0)
                                    <x-heroicon-o-arrow-trending-up class="h-6 w-6 text-success-600 dark:text-success-400" />
                                @else
                                    <x-heroicon-o-arrow-trending-down class="h-6 w-6 text-danger-600 dark:text-danger-400" />
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('owner.reports.comparison.bookings_change') }}
                                </p>
                                <p class="text-xl font-bold {{ $this->monthlyComparison['bookings_change'] >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                    {{ $this->monthlyComparison['bookings_change'] >= 0 ? '+' : '' }}{{ $this->monthlyComparison['bookings_change'] }}%
                                </p>
                                <p class="text-xs text-gray-400">{{ __('owner.reports.comparison.vs_last_month') }}</p>
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            @endif
        @endif

        {{-- Earnings Tab --}}
        @if ($activeTab === 'earnings')
            <x-filament::section>
                <x-slot name="heading">{{ __('owner.reports.charts.earnings_trend') }}</x-slot>
                <div class="h-80">
                    <canvas id="earningsChart"></canvas>
                </div>
            </x-filament::section>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                {{-- Earnings Summary --}}
                <x-filament::section>
                    <x-slot name="heading">{{ __('owner.reports.sections.earnings_summary') }}</x-slot>
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('owner.reports.fields.total_revenue') }}</dt>
                            <dd class="font-semibold text-gray-900 dark:text-white">
                                {{ number_format($this->dashboardStats['total_revenue'], 3) }} OMR
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('owner.reports.fields.platform_fee') }}</dt>
                            <dd class="font-semibold text-danger-600 dark:text-danger-400">
                                -{{ number_format($this->dashboardStats['platform_commission'], 3) }} OMR
                            </dd>
                        </div>
                        <hr class="border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between">
                            <dt class="font-medium text-gray-700 dark:text-gray-300">{{ __('owner.reports.fields.net_earnings') }}</dt>
                            <dd class="text-xl font-bold text-success-600 dark:text-success-400">
                                {{ number_format($this->dashboardStats['total_earnings'], 3) }} OMR
                            </dd>
                        </div>
                        <hr class="border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('owner.reports.fields.paid_out') }}</dt>
                            <dd class="font-semibold text-primary-600 dark:text-primary-400">
                                {{ number_format($this->dashboardStats['completed_payouts'], 3) }} OMR
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('owner.reports.fields.pending_payout') }}</dt>
                            <dd class="font-semibold text-warning-600 dark:text-warning-400">
                                {{ number_format($this->dashboardStats['pending_payouts'], 3) }} OMR
                            </dd>
                        </div>
                    </dl>
                </x-filament::section>

                {{-- Time Slot Distribution --}}
                <x-filament::section>
                    <x-slot name="heading">{{ __('owner.reports.charts.time_slots') }}</x-slot>
                    <div class="h-64">
                        <canvas id="timeSlotChart"></canvas>
                    </div>
                </x-filament::section>
            </div>
        @endif

        {{-- Bookings Tab --}}
        @if ($activeTab === 'bookings')
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                {{-- Booking Status Chart --}}
                <x-filament::section>
                    <x-slot name="heading">{{ __('owner.reports.charts.booking_status') }}</x-slot>
                    <div class="h-64">
                        <canvas id="bookingStatusChart"></canvas>
                    </div>
                </x-filament::section>

                {{-- Booking Stats --}}
                <x-filament::section>
                    <x-slot name="heading">{{ __('owner.reports.sections.booking_summary') }}</x-slot>
                    <div class="space-y-4">
                        @foreach ([
                            ['status' => 'confirmed', 'color' => 'success', 'icon' => 'heroicon-o-check-circle'],
                            ['status' => 'completed', 'color' => 'primary', 'icon' => 'heroicon-o-check-badge'],
                            ['status' => 'pending', 'color' => 'warning', 'icon' => 'heroicon-o-clock'],
                            ['status' => 'cancelled', 'color' => 'danger', 'icon' => 'heroicon-o-x-circle'],
                        ] as $item)
                            @php
                                $count = $this->dashboardStats[$item['status'] . '_bookings'];
                                $total = $this->dashboardStats['total_bookings'];
                                $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-2">
                                        <x-dynamic-component
                                            :component="$item['icon']"
                                            class="h-5 w-5 text-{{ $item['color'] }}-500"
                                        />
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ __('owner.reports.status.' . $item['status']) }}
                                        </span>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $count }} ({{ $percentage }}%)</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                                    <div
                                        class="h-2 rounded-full bg-{{ $item['color'] }}-500"
                                        style="width: {{ $percentage }}%"
                                    ></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            </div>

            {{-- Guest Statistics --}}
            <x-filament::section>
                <x-slot name="heading">{{ __('owner.reports.sections.guest_stats') }}</x-slot>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                            {{ number_format($this->dashboardStats['total_guests']) }}
                        </p>
                        <p class="text-sm text-gray-500">{{ __('owner.reports.stats.total_guests') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $this->dashboardStats['total_bookings'] > 0 ? number_format($this->dashboardStats['total_guests'] / $this->dashboardStats['total_bookings'], 0) : 0 }}
                        </p>
                        <p class="text-sm text-gray-500">{{ __('owner.reports.stats.avg_guests_per_booking') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-success-600 dark:text-success-400">
                            {{ number_format($this->dashboardStats['average_booking_value'], 3) }}
                        </p>
                        <p class="text-sm text-gray-500">{{ __('owner.reports.stats.avg_booking_value') }} (OMR)</p>
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- Halls Tab --}}
        @if ($activeTab === 'halls')
            <x-filament::section>
                <x-slot name="heading">{{ __('owner.reports.sections.hall_performance') }}</x-slot>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="py-3 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
                                <th class="py-3 text-left font-medium text-gray-500 dark:text-gray-400">{{ __('owner.reports.table.hall') }}</th>
                                <th class="py-3 text-center font-medium text-gray-500 dark:text-gray-400">{{ __('owner.reports.table.bookings') }}</th>
                                <th class="py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('owner.reports.table.revenue') }}</th>
                                <th class="py-3 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('owner.reports.table.avg_booking') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($this->hallPerformance as $index => $hall)
                                @php
                                    $hallName = is_array($hall->name) ? ($hall->name[app()->getLocale()] ?? $hall->name['en'] ?? '') : $hall->name;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="py-3 text-gray-500">{{ $index + 1 }}</td>
                                    <td class="py-3 font-medium text-gray-900 dark:text-white">{{ $hallName }}</td>
                                    <td class="py-3 text-center">
                                        <span class="inline-flex items-center rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-800 dark:bg-primary-500/20 dark:text-primary-400">
                                            {{ $hall->bookings_count }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right font-semibold text-success-600 dark:text-success-400">
                                        {{ number_format((float) $hall->total_revenue, 3) }} OMR
                                    </td>
                                    <td class="py-3 text-right text-gray-600 dark:text-gray-400">
                                        {{ number_format((float) $hall->avg_booking_value, 3) }} OMR
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500">
                                        {{ __('owner.reports.no_data') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($this->hallPerformance->isNotEmpty())
                            <tfoot class="border-t-2 border-gray-300 dark:border-gray-600">
                                <tr class="font-semibold">
                                    <td colspan="2" class="py-3 text-gray-700 dark:text-gray-300">{{ __('owner.reports.table.total') }}</td>
                                    <td class="py-3 text-center text-gray-900 dark:text-white">
                                        {{ $this->hallPerformance->sum('bookings_count') }}
                                    </td>
                                    <td class="py-3 text-right text-success-600 dark:text-success-400">
                                        {{ number_format((float) $this->hallPerformance->sum('total_revenue'), 3) }} OMR
                                    </td>
                                    <td class="py-3 text-right text-gray-600 dark:text-gray-400">—</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </x-filament::section>

            {{-- Hall Quick Stats --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <x-filament::section>
                    <div class="text-center">
                        <x-heroicon-o-building-office class="mx-auto h-8 w-8 text-primary-500" />
                        <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $this->dashboardStats['total_halls'] }}
                        </p>
                        <p class="text-sm text-gray-500">{{ __('owner.reports.stats.total_halls') }}</p>
                    </div>
                </x-filament::section>

                <x-filament::section>
                    <div class="text-center">
                        <x-heroicon-o-check-badge class="mx-auto h-8 w-8 text-success-500" />
                        <p class="mt-2 text-2xl font-bold text-success-600 dark:text-success-400">
                            {{ $this->dashboardStats['active_halls'] }}
                        </p>
                        <p class="text-sm text-gray-500">{{ __('owner.reports.stats.active_halls') }}</p>
                    </div>
                </x-filament::section>

                <x-filament::section>
                    <div class="text-center">
                        <x-heroicon-o-chart-bar class="mx-auto h-8 w-8 text-info-500" />
                        <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                            @php
                                $avgPerHall = $this->dashboardStats['total_halls'] > 0
                                    ? $this->dashboardStats['total_bookings'] / $this->dashboardStats['total_halls']
                                    : 0;
                            @endphp
                            {{ number_format($avgPerHall, 1) }}
                        </p>
                        <p class="text-sm text-gray-500">{{ __('owner.reports.stats.avg_bookings_per_hall') }}</p>
                    </div>
                </x-filament::section>
            </div>
        @endif
    </div>

    {{-- Charts Script --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('livewire:navigated', initOwnerCharts);
            document.addEventListener('DOMContentLoaded', initOwnerCharts);

            function initOwnerCharts() {
                // Destroy existing charts
                Chart.helpers.each(Chart.instances, (instance) => instance.destroy());

                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#9ca3af' : '#6b7280';
                const gridColor = isDark ? '#374151' : '#e5e7eb';

                // Earnings Chart
                const earningsCtx = document.getElementById('earningsChart');
                if (earningsCtx) {
                    new Chart(earningsCtx, {
                        type: 'line',
                        data: {
                            labels: @json($this->revenueTrend['labels'] ?? []),
                            datasets: [
                                {
                                    label: '{{ __("owner.reports.charts.revenue") }}',
                                    data: @json($this->revenueTrend['revenue'] ?? []),
                                    borderColor: '#6366f1',
                                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                    fill: true,
                                    tension: 0.4,
                                },
                                {
                                    label: '{{ __("owner.reports.charts.earnings") }}',
                                    data: @json($this->revenueTrend['payout'] ?? []),
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    fill: true,
                                    tension: 0.4,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { labels: { color: textColor } }
                            },
                            scales: {
                                x: { ticks: { color: textColor }, grid: { color: gridColor } },
                                y: { ticks: { color: textColor }, grid: { color: gridColor } }
                            }
                        }
                    });
                }

                // Booking Status Chart
                const bookingCtx = document.getElementById('bookingStatusChart');
                if (bookingCtx) {
                    new Chart(bookingCtx, {
                        type: 'doughnut',
                        data: {
                            labels: @json($this->bookingDistribution['labels'] ?? []),
                            datasets: [{
                                data: @json($this->bookingDistribution['data'] ?? []),
                                backgroundColor: ['#10b981', '#6366f1', '#f59e0b', '#ef4444'],
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom', labels: { color: textColor } }
                            }
                        }
                    });
                }

                // Time Slot Chart
                const timeSlotCtx = document.getElementById('timeSlotChart');
                if (timeSlotCtx) {
                    new Chart(timeSlotCtx, {
                        type: 'bar',
                        data: {
                            labels: @json($this->timeSlotDistribution['labels'] ?? []),
                            datasets: [{
                                label: '{{ __("owner.reports.charts.bookings") }}',
                                data: @json($this->timeSlotDistribution['data'] ?? []),
                                backgroundColor: ['#6366f1', '#8b5cf6', '#a855f7', '#c084fc'],
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                x: { ticks: { color: textColor }, grid: { display: false } },
                                y: { ticks: { color: textColor }, grid: { color: gridColor } }
                            }
                        }
                    });
                }
            }

            // Reinitialize on tab change
            Livewire.on('activeTabUpdated', () => setTimeout(initOwnerCharts, 100));
        </script>
    @endpush
</x-filament-panels::page>
