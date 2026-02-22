<x-filament-panels::page>
    {{-- Filter Form --}}
    <div class="mb-6">
        {{ $this->form }}
    </div>

    {{-- Tab Navigation --}}
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <nav class="flex -mb-px space-x-8 rtl:space-x-reverse" aria-label="Tabs">
            @foreach ([
        'overview' => ['icon' => 'heroicon-o-squares-2x2', 'label' => __('owner_report.reports.tabs.overview')],
        'earnings' => ['icon' => 'heroicon-o-banknotes', 'label' => __('owner_report.reports.tabs.earnings')],
        'bookings' => ['icon' => 'heroicon-o-calendar-days', 'label' => __('owner_report.reports.tabs.bookings')],
        'halls' => ['icon' => 'heroicon-o-building-office', 'label' => __('owner_report.reports.tabs.halls')],
    ] as $tab => $data)
                <button wire:click="setActiveTab('{{ $tab }}')"
                    class="group inline-flex items-center border-b-2 py-4 px-1 text-sm font-medium transition-colors
                        {{ $activeTab === $tab
                            ? 'border-primary-500 text-primary-600 dark:text-primary-400'
                            : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    <x-dynamic-component :component="$data['icon']"
                        class="ltr:-ml-0.5 ltr:mr-2 rtl:-mr-0.5 rtl:ml-2 h-5 w-5
                            {{ $activeTab === $tab ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
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
                        <div class="p-3 rounded-full bg-success-100 dark:bg-success-500/20">
                            <x-heroicon-o-banknotes class="w-6 h-6 text-success-600 dark:text-success-400" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('owner_report.reports.stats.total_earnings') }}
                            </p>
                            <p class="text-2xl font-bold text-success-600 dark:text-success-400">
                                {{ number_format($this->dashboardStats['total_earnings'] ?? 0, 3) }} OMR
                            </p>
                        </div>
                    </div>
                </x-filament::section>

                {{-- Total Revenue --}}
                <x-filament::section>
                    <div class="flex items-center gap-4">
                        <div class="p-3 rounded-full bg-primary-100 dark:bg-primary-500/20">
                            <x-heroicon-o-currency-dollar class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('owner_report.reports.stats.total_revenue') }}
                            </p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ number_format($this->dashboardStats['total_revenue'] ?? 0, 3) }} OMR
                            </p>
                        </div>
                    </div>
                </x-filament::section>

                {{-- Total Bookings --}}
                <x-filament::section>
                    <div class="flex items-center gap-4">
                        <div class="p-3 rounded-full bg-info-100 dark:bg-info-500/20">
                            <x-heroicon-o-calendar-days class="w-6 h-6 text-info-600 dark:text-info-400" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('owner_report.reports.stats.total_bookings') }}
                            </p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ number_format($this->dashboardStats['total_bookings'] ?? 0) }}
                            </p>
                        </div>
                    </div>
                </x-filament::section>

                {{-- Pending Payouts --}}
                <x-filament::section>
                    <div class="flex items-center gap-4">
                        <div class="p-3 rounded-full bg-warning-100 dark:bg-warning-500/20">
                            <x-heroicon-o-clock class="w-6 h-6 text-warning-600 dark:text-warning-400" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('owner_report.reports.stats.pending_payout') }}
                            </p>
                            <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">
                                {{ number_format($this->dashboardStats['pending_payouts'] ?? 0, 3) }} OMR
                            </p>
                        </div>
                    </div>
                </x-filament::section>
            </div>

            {{-- Quick Stats --}}
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ([['label' => __('owner_report.reports.stats.confirmed'), 'value' => $this->dashboardStats['confirmed_bookings'] ?? 0, 'color' => 'success'], ['label' => __('owner_report.reports.stats.completed'), 'value' => $this->dashboardStats['completed_bookings'] ?? 0, 'color' => 'primary'], ['label' => __('owner_report.reports.stats.pending'), 'value' => $this->dashboardStats['pending_bookings'] ?? 0, 'color' => 'warning'], ['label' => __('owner_report.reports.stats.cancelled'), 'value' => $this->dashboardStats['cancelled_bookings'] ?? 0, 'color' => 'danger'], ['label' => __('owner_report.reports.stats.total_guests'), 'value' => $this->dashboardStats['total_guests'] ?? 0, 'color' => 'info'], ['label' => __('owner_report.reports.stats.avg_booking'), 'value' => number_format($this->dashboardStats['average_booking_value'] ?? 0, 3), 'color' => 'gray', 'suffix' => ' OMR']] as $stat)
                    <div
                        class="p-4 bg-white shadow-sm rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                        <p
                            class="mt-1 text-xl font-semibold text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400">
                            {{ is_numeric($stat['value']) ? number_format($stat['value']) : $stat['value'] }}{{ $stat['suffix'] ?? '' }}
                        </p>
                    </div>
                @endforeach
            </div>

            {{-- Monthly Comparison --}}
            @if (!empty($this->monthlyComparison))
                <x-filament::section>
                    <x-slot name="heading">{{ __('owner_report.reports.sections.monthly_comparison') }}</x-slot>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        {{-- Revenue Change --}}
                        <div class="flex items-center gap-4">
                            <div
                                class="rounded-full p-3 {{ ($this->monthlyComparison['revenue_change'] ?? 0) >= 0 ? 'bg-success-100 dark:bg-success-500/20' : 'bg-danger-100 dark:bg-danger-500/20' }}">
                                @if (($this->monthlyComparison['revenue_change'] ?? 0) >= 0)
                                    <x-heroicon-o-arrow-trending-up
                                        class="w-6 h-6 text-success-600 dark:text-success-400" />
                                @else
                                    <x-heroicon-o-arrow-trending-down
                                        class="w-6 h-6 text-danger-600 dark:text-danger-400" />
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('owner_report.reports.comparison.earnings_change') }}
                                </p>
                                <p
                                    class="text-xl font-bold {{ ($this->monthlyComparison['revenue_change'] ?? 0) >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                    {{ ($this->monthlyComparison['revenue_change'] ?? 0) >= 0 ? '+' : '' }}{{ $this->monthlyComparison['revenue_change'] ?? 0 }}%
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ __('owner_report.reports.comparison.vs_last_month') }}</p>
                            </div>
                        </div>

                        {{-- Bookings Change --}}
                        <div class="flex items-center gap-4">
                            <div
                                class="rounded-full p-3 {{ ($this->monthlyComparison['bookings_change'] ?? 0) >= 0 ? 'bg-success-100 dark:bg-success-500/20' : 'bg-danger-100 dark:bg-danger-500/20' }}">
                                @if (($this->monthlyComparison['bookings_change'] ?? 0) >= 0)
                                    <x-heroicon-o-arrow-trending-up
                                        class="w-6 h-6 text-success-600 dark:text-success-400" />
                                @else
                                    <x-heroicon-o-arrow-trending-down
                                        class="w-6 h-6 text-danger-600 dark:text-danger-400" />
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('owner_report.reports.comparison.bookings_change') }}
                                </p>
                                <p
                                    class="text-xl font-bold {{ ($this->monthlyComparison['bookings_change'] ?? 0) >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                    {{ ($this->monthlyComparison['bookings_change'] ?? 0) >= 0 ? '+' : '' }}{{ $this->monthlyComparison['bookings_change'] ?? 0 }}%
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ __('owner_report.reports.comparison.vs_last_month') }}</p>
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            @endif
        @endif

        {{-- Earnings Tab --}}
        @if ($activeTab === 'earnings')
            <x-filament::section>
                <x-slot name="heading">{{ __('owner_report.reports.charts.earnings_trend') }}</x-slot>
                {{--
                    FIX: wire:ignore prevents Livewire from re-rendering the canvas
                    which would destroy the Chart.js instance.
                    The canvas ID is used by the script block to find and render charts.
                --}}
                <div class="h-80" wire:ignore>
                    <canvas id="earningsChart"></canvas>
                </div>
                {{-- No Data Placeholder --}}
                @if (empty($this->revenueTrend['labels']))
                    <div class="flex items-center justify-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">{{ __('owner_report.reports.no_data') }}</p>
                    </div>
                @endif
            </x-filament::section>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                {{-- Earnings Summary --}}
                <x-filament::section>
                    <x-slot name="heading">{{ __('owner_report.reports.sections.earnings_summary') }}</x-slot>
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">
                                {{ __('owner_report.reports.fields.total_revenue') }}</dt>
                            <dd class="font-semibold text-gray-900 dark:text-white">
                                {{ number_format($this->dashboardStats['total_revenue'] ?? 0, 3) }} OMR
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">
                                {{ __('owner_report.reports.fields.platform_fee') }}</dt>
                            <dd class="font-semibold text-danger-600 dark:text-danger-400">
                                -{{ number_format($this->dashboardStats['platform_commission'] ?? 0, 3) }} OMR
                            </dd>
                        </div>
                        <hr class="border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between">
                            <dt class="font-medium text-gray-700 dark:text-gray-300">
                                {{ __('owner_report.reports.fields.net_earnings') }}</dt>
                            <dd class="text-xl font-bold text-success-600 dark:text-success-400">
                                {{ number_format($this->dashboardStats['total_earnings'] ?? 0, 3) }} OMR
                            </dd>
                        </div>
                        <hr class="border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">
                                {{ __('owner_report.reports.fields.paid_out') }}</dt>
                            <dd class="font-semibold text-primary-600 dark:text-primary-400">
                                {{ number_format($this->dashboardStats['completed_payouts'] ?? 0, 3) }} OMR
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">
                                {{ __('owner_report.reports.fields.pending_payout') }}</dt>
                            <dd class="font-semibold text-warning-600 dark:text-warning-400">
                                {{ number_format($this->dashboardStats['pending_payouts'] ?? 0, 3) }} OMR
                            </dd>
                        </div>
                    </dl>
                </x-filament::section>

                {{-- Time Slot Distribution --}}
                <x-filament::section>
                    <x-slot name="heading">{{ __('owner_report.reports.charts.time_slots') }}</x-slot>
                    <div class="h-64" wire:ignore>
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
                    <x-slot name="heading">{{ __('owner_report.reports.charts.booking_status') }}</x-slot>
                    <div class="h-64" wire:ignore>
                        <canvas id="bookingStatusChart"></canvas>
                    </div>
                    {{-- No Data Message --}}
                    @if (empty($this->bookingDistribution['data']) || array_sum($this->bookingDistribution['data']) === 0)
                        <div class="py-8 text-center">
                            <x-heroicon-o-chart-pie class="w-12 h-12 mx-auto text-gray-400" />
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('owner_report.reports.no_booking_data') }}
                            </p>
                        </div>
                    @endif
                </x-filament::section>

                {{-- Booking Stats --}}
                <x-filament::section>
                    <x-slot name="heading">{{ __('owner_report.reports.sections.booking_summary') }}</x-slot>
                    <div class="space-y-4">
                        @foreach ([['label' => __('owner_report.reports.status.confirmed'), 'value' => $this->dashboardStats['confirmed_bookings'] ?? 0, 'color' => 'success', 'icon' => 'heroicon-o-check-circle'], ['label' => __('owner_report.reports.status.completed'), 'value' => $this->dashboardStats['completed_bookings'] ?? 0, 'color' => 'primary', 'icon' => 'heroicon-o-check-badge'], ['label' => __('owner_report.reports.status.pending'), 'value' => $this->dashboardStats['pending_bookings'] ?? 0, 'color' => 'warning', 'icon' => 'heroicon-o-clock'], ['label' => __('owner_report.reports.status.cancelled'), 'value' => $this->dashboardStats['cancelled_bookings'] ?? 0, 'color' => 'danger', 'icon' => 'heroicon-o-x-circle']] as $status)
                            <div
                                class="flex items-center justify-between p-3 rounded-lg bg-{{ $status['color'] }}-50 dark:bg-{{ $status['color'] }}-500/10">
                                <div class="flex items-center gap-3">
                                    <x-dynamic-component :component="$status['icon']"
                                        class="w-5 h-5 text-{{ $status['color'] }}-600 dark:text-{{ $status['color'] }}-400" />
                                    <span
                                        class="font-medium text-gray-700 dark:text-gray-300">{{ $status['label'] }}</span>
                                </div>
                                <span
                                    class="text-lg font-bold text-{{ $status['color'] }}-600 dark:text-{{ $status['color'] }}-400">
                                    {{ number_format($status['value']) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            </div>

            {{-- Total Guests --}}
            <x-filament::section>
                <x-slot name="heading">{{ __('owner_report.reports.sections.guest_summary') }}</x-slot>
                <div class="flex items-center gap-4">
                    <div class="p-4 rounded-full bg-info-100 dark:bg-info-500/20">
                        <x-heroicon-o-user-group class="w-8 h-8 text-info-600 dark:text-info-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('owner_report.reports.stats.total_guests') }}
                        </p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($this->dashboardStats['total_guests'] ?? 0) }}
                        </p>
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- Halls Tab --}}
        @if ($activeTab === 'halls')
            {{-- Hall Performance Table --}}
            <x-filament::section>
                <x-slot name="heading">{{ __('owner_report.reports.sections.hall_performance') }}</x-slot>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 font-medium text-center text-gray-500 dark:text-gray-400">
                                    {{ __('owner_report.reports.table.hall') }}
                                </th>
                                <th class="px-4 py-3 font-medium text-center text-gray-500 dark:text-gray-400">
                                    {{ __('owner_report.reports.table.bookings') }}
                                </th>
                                <th class="px-4 py-3 font-medium text-right text-gray-500 dark:text-gray-400">
                                    {{ __('owner_report.reports.table.revenue') }}
                                </th>
                                <th class="px-4 py-3 font-medium text-right text-gray-500 dark:text-gray-400">
                                    {{ __('owner_report.reports.table.earnings') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($this->hallPerformance as $hall)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        {{ is_array($hall->name) ? $hall->name[app()->getLocale()] ?? ($hall->name['en'] ?? '') : $hall->name }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">
                                        {{ number_format($hall->total_bookings ?? 0) }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">
                                        {{ number_format((float) ($hall->total_revenue ?? 0), 3) }} OMR
                                    </td>
                                    <td
                                        class="px-4 py-3 font-semibold text-right text-success-600 dark:text-success-400">
                                        {{ number_format((float) ($hall->total_payout ?? 0), 3) }} OMR
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        {{ __('owner_report.reports.no_halls_data') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>

            {{-- Hall Stats Summary --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <x-filament::section>
                    <div class="text-center">
                        <x-heroicon-o-building-office-2 class="w-8 h-8 mx-auto text-primary-500" />
                        <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $this->dashboardStats['total_halls'] ?? 0 }}
                        </p>
                        <p class="text-sm text-gray-500">{{ __('owner_report.reports.stats.total_halls') }}</p>
                    </div>
                </x-filament::section>

                <x-filament::section>
                    <div class="text-center">
                        <x-heroicon-o-check-badge class="w-8 h-8 mx-auto text-success-500" />
                        <p class="mt-2 text-2xl font-bold text-success-600 dark:text-success-400">
                            {{ $this->dashboardStats['active_halls'] ?? 0 }}
                        </p>
                        <p class="text-sm text-gray-500">{{ __('owner_report.reports.stats.active_halls') }}</p>
                    </div>
                </x-filament::section>

                <x-filament::section>
                    <div class="text-center">
                        <x-heroicon-o-chart-bar class="w-8 h-8 mx-auto text-info-500" />
                        <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                            @php
                                $totalHalls = $this->dashboardStats['total_halls'] ?? 0;
                                $avgPerHall =
                                    $totalHalls > 0 ? ($this->dashboardStats['total_bookings'] ?? 0) / $totalHalls : 0;
                            @endphp
                            {{ number_format($avgPerHall, 1) }}
                        </p>
                        <p class="text-sm text-gray-500">{{ __('owner_report.reports.stats.avg_bookings_per_hall') }}
                        </p>
                    </div>
                </x-filament::section>
            </div>
        @endif
    </div>

    {{--
        ==========================================================================
        CHARTS SCRIPT — FIXED FOR LIVEWIRE 3 / FILAMENT 3.3
        ==========================================================================

        KEY FIXES:
        1. pushOnce loads Chart.js CDN into layout stack (not component DOM)
           — prevents "multiple root elements" Livewire error
           — Chart.js stays in memory across SPA navigations once loaded
        2. script directive runs on every component mount (handles SPA navigation)
        3. $wire.getChartData() fetches FRESH data from server instead of stale data
        4. $wire.on() properly listens for Livewire 3 events with cleanup
        5. Charts properly destroyed on component disconnect (cleanup function)

        WHY THE OLD APPROACH BROKE:
        - Data was baked into JS at render time; never updates on filter changes
        - With Filament SPA navigation (wire:navigate), pushed scripts dont re-execute
    --}}
    @assets
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    @endassets

    @script
        <script>
            /**
             * Owner Reports Charts — Livewire 3 Script Implementation
             *
             * This script uses Livewire 3's script directive which:
             * - Runs every time the component mounts (including SPA navigation)
             * - Provides $wire for direct Livewire component communication
             * - Automatically handles cleanup on component disconnect
             */

            // =====================================================================
            // Safety: ensure Chart.js is loaded (handles SPA edge case where
            // pushOnce hasn't executed yet on first navigation)
            // =====================================================================
            if (typeof Chart === 'undefined') {
                console.warn('[OwnerReports] Chart.js not yet loaded — loading dynamically');
                await new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                    script.onload = resolve;
                    script.onerror = reject;
                    document.head.appendChild(script);
                });
            }

            // =====================================================================
            // Chart instance storage for lifecycle management
            // =====================================================================
            let chartInstances = {
                earnings: null,
                bookingStatus: null,
                timeSlot: null
            };

            /**
             * Get chart color configuration based on current theme (light/dark).
             *
             * @returns {Object} Color configuration for Chart.js
             */
            function getChartColors() {
                const isDark = document.documentElement.classList.contains('dark');
                return {
                    text: isDark ? '#9ca3af' : '#6b7280',
                    grid: isDark ? '#374151' : '#e5e7eb',
                    primary: '#6366f1',
                    primaryBg: 'rgba(99, 102, 241, 0.1)',
                    success: '#10b981',
                    successBg: 'rgba(16, 185, 129, 0.1)',
                    warning: '#f59e0b',
                    danger: '#ef4444',
                    purple: '#8b5cf6',
                    pink: '#a855f7',
                    violet: '#c084fc'
                };
            }

            /**
             * Safely destroy a single chart instance to prevent memory leaks.
             *
             * @param {string} chartKey - Key in chartInstances object
             */
            function destroyChart(chartKey) {
                if (chartInstances[chartKey]) {
                    chartInstances[chartKey].destroy();
                    chartInstances[chartKey] = null;
                }
            }

            /**
             * Destroy all chart instances (used before full reinitialization).
             */
            function destroyAllCharts() {
                Object.keys(chartInstances).forEach(destroyChart);
            }

            /**
             * Initialize the Earnings Trend line chart.
             *
             * FIX: Receives data as parameter (fetched from server via $wire)
             * instead of using stale inline data that was only evaluated once.
             *
             * @param {Object} colors - Chart color configuration
             * @param {Object} chartData - Fresh data from $wire.getChartData()
             */
            function initEarningsChart(colors, chartData) {
                const ctx = document.getElementById('earningsChart');
                if (!ctx) return;

                // Destroy existing instance to prevent "Canvas already in use" error
                destroyChart('earnings');

                // Extract data from server response
                const labels = chartData.revenueTrend?.labels || [];
                const revenueData = chartData.revenueTrend?.revenue || [];
                const payoutData = chartData.revenueTrend?.payout || [];

                // Skip rendering if no data available
                if (!labels.length) {
                    console.log('[OwnerReports] No earnings data available');
                    return;
                }

                chartInstances.earnings = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: '{{ __('owner_report.reports.charts.revenue') }}',
                                data: revenueData,
                                borderColor: colors.primary,
                                backgroundColor: colors.primaryBg,
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 5
                            },
                            {
                                label: '{{ __('owner_report.reports.charts.earnings') }}',
                                data: payoutData,
                                borderColor: colors.success,
                                backgroundColor: colors.successBg,
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 5
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    color: colors.text,
                                    usePointStyle: true,
                                    padding: 20
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' +
                                            new Intl.NumberFormat('en-OM', {
                                                minimumFractionDigits: 3,
                                                maximumFractionDigits: 3
                                            }).format(context.raw) + ' OMR';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: colors.text
                                },
                                grid: {
                                    color: colors.grid,
                                    display: false
                                }
                            },
                            y: {
                                ticks: {
                                    color: colors.text,
                                    callback: function(value) {
                                        return value.toFixed(3) + ' OMR';
                                    }
                                },
                                grid: {
                                    color: colors.grid
                                }
                            }
                        }
                    }
                });

                console.log('[OwnerReports] Earnings chart initialized with', labels.length, 'data points');
            }

            /**
             * Initialize the Booking Status doughnut chart.
             *
             * FIX: Receives data as parameter (fetched from server via $wire).
             *
             * @param {Object} colors - Chart color configuration
             * @param {Object} chartData - Fresh data from $wire.getChartData()
             */
            function initBookingStatusChart(colors, chartData) {
                const ctx = document.getElementById('bookingStatusChart');
                if (!ctx) return;

                // Destroy existing instance
                destroyChart('bookingStatus');

                // Extract data from server response
                const labels = chartData.bookingDistribution?.labels || [];
                const data = chartData.bookingDistribution?.data || [];

                // Skip if no meaningful data
                if (!labels.length || !data.some(v => v > 0)) {
                    console.log('[OwnerReports] No booking status data available');
                    return;
                }

                chartInstances.bookingStatus = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: [
                                colors.success, // Confirmed - green
                                colors.primary, // Completed - indigo
                                colors.warning, // Pending - amber
                                colors.danger // Cancelled - red
                            ],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: colors.text,
                                    usePointStyle: true,
                                    padding: 15,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ?
                                            ((context.raw / total) * 100).toFixed(1) :
                                            0;
                                        return context.label + ': ' + context.raw + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });

                console.log('[OwnerReports] Booking status chart initialized with', labels.length, 'categories');
            }

            /**
             * Initialize the Time Slot distribution bar chart.
             *
             * FIX: Receives data as parameter (fetched from server via $wire).
             *
             * @param {Object} colors - Chart color configuration
             * @param {Object} chartData - Fresh data from $wire.getChartData()
             */
            function initTimeSlotChart(colors, chartData) {
                const ctx = document.getElementById('timeSlotChart');
                if (!ctx) return;

                // Destroy existing instance
                destroyChart('timeSlot');

                // Extract data from server response
                const labels = chartData.timeSlotDistribution?.labels || [];
                const data = chartData.timeSlotDistribution?.data || [];

                // Skip if no data
                if (!labels.length) {
                    console.log('[OwnerReports] No time slot data available');
                    return;
                }

                chartInstances.timeSlot = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: '{{ __('owner_report.reports.charts.bookings') }}',
                            data: data,
                            backgroundColor: [
                                colors.primary, // Morning
                                colors.purple, // Afternoon
                                colors.pink, // Evening
                                colors.violet // Full Day
                            ],
                            borderRadius: 6,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: colors.text
                                },
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                ticks: {
                                    color: colors.text,
                                    stepSize: 1
                                },
                                grid: {
                                    color: colors.grid
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });

                console.log('[OwnerReports] Time slot chart initialized with', labels.length, 'slots');
            }

            /**
             * Initialize all charts for the currently visible tab.
             *
             * FIX: Fetches FRESH data from the server via $wire.getChartData()
             * every time charts are initialized. This ensures filters (date range,
             * hall selection) are always reflected in the charts.
             *
             * Uses requestAnimationFrame to ensure DOM is ready after Livewire
             * morphing completes, then adds an additional small delay for safety.
             */
            async function initOwnerCharts() {
                // Wait for next animation frame to ensure DOM is settled
                await new Promise(resolve => requestAnimationFrame(resolve));

                // Additional delay to ensure Livewire DOM morphing is complete
                await new Promise(resolve => setTimeout(resolve, 100));

                // FIX: Fetch FRESH chart data from the Livewire component
                // This calls the public getChartData() method on Reports.php
                // and returns the latest computed property values
                let chartData;
                try {
                    chartData = await $wire.getChartData();
                } catch (error) {
                    console.error('[OwnerReports] Failed to fetch chart data:', error);
                    return;
                }

                const colors = getChartColors();

                // Initialize charts based on which canvas elements exist in current tab
                if (document.getElementById('earningsChart')) {
                    initEarningsChart(colors, chartData);
                }

                if (document.getElementById('bookingStatusChart')) {
                    initBookingStatusChart(colors, chartData);
                }

                if (document.getElementById('timeSlotChart')) {
                    initTimeSlotChart(colors, chartData);
                }
            }

            // =====================================================================
            // Event Listeners — Livewire 3 $wire.on() syntax with auto-cleanup
            // =====================================================================

            /**
             * Initialize charts when the component first mounts.
             * The script block runs on each mount, so this handles:
             * - Initial page load
             * - SPA navigation (wire:navigate) to/from this page
             */
            console.log('[OwnerReports] Component mounted — initializing charts');
            initOwnerCharts();

            /**
             * Handle tab switching.
             * When user clicks a different tab, Livewire re-renders the DOM
             * and this event fires so we can initialize charts for the new tab.
             *
             * FIX: $wire.on() in the script block automatically cleans up when
             * the component is disconnected (no manual cleanup needed).
             */
            $wire.on('activeTabUpdated', (data) => {
                console.log('[OwnerReports] Tab updated to:', data.tab || data);
                // Destroy old charts before rendering new ones
                destroyAllCharts();
                initOwnerCharts();
            });

            /**
             * Handle data refresh (when filters change).
             * This fires when date range, hall selection, or preset changes
             * via the loadData() method in Reports.php.
             *
             * FIX: Charts are completely destroyed and recreated with fresh
             * data from the server, ensuring all filter changes are reflected.
             */
            $wire.on('chartsDataUpdated', () => {
                console.log('[OwnerReports] Filters changed — refreshing charts');
                destroyAllCharts();
                initOwnerCharts();
            });

            /**
             * Handle theme changes (dark mode toggle).
             * Re-renders charts with updated colors when theme switches.
             */
            const themeObserver = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'class') {
                        console.log('[OwnerReports] Theme changed — reinitializing charts');
                        destroyAllCharts();
                        initOwnerCharts();
                    }
                });
            });

            // Observe dark mode changes on html element
            themeObserver.observe(document.documentElement, {
                attributes: true
            });

            /**
             * Cleanup function — runs when component disconnects.
             * This is the return value of the script block and Livewire 3
             * calls it automatically on component disconnect.
             */
            return () => {
                console.log('[OwnerReports] Component disconnecting — cleaning up');
                destroyAllCharts();
                themeObserver.disconnect();
            };
        </script>
    @endscript


</x-filament-panels::page>
