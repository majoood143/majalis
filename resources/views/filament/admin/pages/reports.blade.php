<x-filament-panels::page>
    {{-- Filter Form --}}
    <div class="mb-6">
        {{ $this->form }}
    </div>

    {{-- Tab Navigation --}}
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8 rtl:space-x-reverse" aria-label="Tabs">
            @foreach ([
                'overview' => ['icon' => 'heroicon-o-squares-2x2', 'label' => __('admin.reports.tabs.overview')],
                'revenue' => ['icon' => 'heroicon-o-currency-dollar', 'label' => __('admin.reports.tabs.revenue')],
                'bookings' => ['icon' => 'heroicon-o-calendar-days', 'label' => __('admin.reports.tabs.bookings')],
                'performance' => ['icon' => 'heroicon-o-chart-bar', 'label' => __('admin.reports.tabs.performance')],
                'commission' => ['icon' => 'heroicon-o-banknotes', 'label' => __('admin.reports.tabs.commission')],
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
            {{-- Stats Grid --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Total Revenue --}}
                <x-filament::section>
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-success-100 p-3 dark:bg-success-500/20">
                            <x-heroicon-o-banknotes class="h-6 w-6 text-success-600 dark:text-success-400" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('admin.reports.stats.total_revenue') }}
                            </p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ number_format($this->dashboardStats['total_revenue'], 3) }} OMR
                            </p>
                        </div>
                    </div>
                </x-filament::section>

                {{-- Total Commission --}}
                <x-filament::section>
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-primary-100 p-3 dark:bg-primary-500/20">
                            <x-heroicon-o-currency-dollar class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('admin.reports.stats.platform_commission') }}
                            </p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ number_format($this->dashboardStats['total_commission'], 3) }} OMR
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
                                {{ __('admin.reports.stats.total_bookings') }}
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
                                {{ __('admin.reports.stats.pending_payouts') }}
                            </p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ number_format($this->dashboardStats['pending_payout_amount'], 3) }} OMR
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $this->dashboardStats['pending_payouts'] }} {{ __('admin.reports.stats.payouts') }}
                            </p>
                        </div>
                    </div>
                </x-filament::section>
            </div>

            {{-- Quick Stats Row --}}
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ([
                    ['label' => __('admin.reports.stats.confirmed'), 'value' => $this->dashboardStats['confirmed_bookings'], 'color' => 'success'],
                    ['label' => __('admin.reports.stats.completed'), 'value' => $this->dashboardStats['completed_bookings'], 'color' => 'primary'],
                    ['label' => __('admin.reports.stats.pending'), 'value' => $this->dashboardStats['pending_bookings'], 'color' => 'warning'],
                    ['label' => __('admin.reports.stats.cancelled'), 'value' => $this->dashboardStats['cancelled_bookings'], 'color' => 'danger'],
                    ['label' => __('admin.reports.stats.active_halls'), 'value' => $this->dashboardStats['total_halls'], 'color' => 'info'],
                    ['label' => __('admin.reports.stats.verified_owners'), 'value' => $this->dashboardStats['total_owners'], 'color' => 'gray'],
                ] as $stat)
                    <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                        <p class="mt-1 text-xl font-semibold text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400">
                            {{ number_format($stat['value']) }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Revenue Tab --}}
        @if ($activeTab === 'revenue')
            <x-filament::section>
                <x-slot name="heading">{{ __('admin.reports.charts.revenue_trend') }}</x-slot>
                <div class="h-80">
                    <canvas id="revenueChart"></canvas>
                </div>
            </x-filament::section>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                {{-- Revenue Summary --}}
                <x-filament::section>
                    <x-slot name="heading">{{ __('admin.reports.sections.revenue_summary') }}</x-slot>
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.reports.fields.gross_revenue') }}</dt>
                            <dd class="font-semibold text-gray-900 dark:text-white">
                                {{ number_format($this->dashboardStats['total_revenue'], 3) }} OMR
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.reports.fields.platform_commission') }}</dt>
                            <dd class="font-semibold text-primary-600 dark:text-primary-400">
                                {{ number_format($this->dashboardStats['total_commission'], 3) }} OMR
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.reports.fields.owner_payouts') }}</dt>
                            <dd class="font-semibold text-success-600 dark:text-success-400">
                                {{ number_format($this->dashboardStats['total_owner_payout'], 3) }} OMR
                            </dd>
                        </div>
                        <hr class="border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.reports.fields.refunds') }}</dt>
                            <dd class="font-semibold text-danger-600 dark:text-danger-400">
                                {{ number_format($this->dashboardStats['refunded_amount'], 3) }} OMR
                            </dd>
                        </div>
                    </dl>
                </x-filament::section>

                {{-- Time Slot Distribution --}}
                <x-filament::section>
                    <x-slot name="heading">{{ __('admin.reports.charts.time_slots') }}</x-slot>
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
                    <x-slot name="heading">{{ __('admin.reports.charts.booking_status') }}</x-slot>
                    <div class="h-64">
                        <canvas id="bookingStatusChart"></canvas>
                    </div>
                </x-filament::section>

                {{-- Booking Stats --}}
                <x-filament::section>
                    <x-slot name="heading">{{ __('admin.reports.sections.booking_summary') }}</x-slot>
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
                                            {{ __('admin.reports.status.' . $item['status']) }}
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
        @endif

        {{-- Performance Tab --}}
        @if ($activeTab === 'performance')
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                {{-- Top Halls --}}
                <x-filament::section>
                    <x-slot name="heading">{{ __('admin.reports.sections.top_halls') }}</x-slot>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="py-2 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
                                    <th class="py-2 text-left font-medium text-gray-500 dark:text-gray-400">{{ __('admin.reports.table.hall') }}</th>
                                    <th class="py-2 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('admin.reports.table.bookings') }}</th>
                                    <th class="py-2 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('admin.reports.table.revenue') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse ($this->topHalls as $index => $hall)
                                    @php
                                        $hallName = is_array($hall->name) ? ($hall->name[app()->getLocale()] ?? $hall->name['en'] ?? '') : $hall->name;
                                    @endphp
                                    <tr>
                                        <td class="py-2 text-gray-500">{{ $index + 1 }}</td>
                                        <td class="py-2 font-medium text-gray-900 dark:text-white">{{ $hallName }}</td>
                                        <td class="py-2 text-right text-gray-600 dark:text-gray-400">{{ $hall->bookings_count }}</td>
                                        <td class="py-2 text-right font-semibold text-success-600 dark:text-success-400">
                                            {{ number_format((float) $hall->total_revenue, 3) }} OMR
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-gray-500">
                                            {{ __('admin.reports.no_data') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>

                {{-- Top Owners --}}
                <x-filament::section>
                    <x-slot name="heading">{{ __('admin.reports.sections.top_owners') }}</x-slot>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="py-2 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
                                    <th class="py-2 text-left font-medium text-gray-500 dark:text-gray-400">{{ __('admin.reports.table.owner') }}</th>
                                    <th class="py-2 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('admin.reports.table.halls') }}</th>
                                    <th class="py-2 text-right font-medium text-gray-500 dark:text-gray-400">{{ __('admin.reports.table.revenue') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse ($this->topOwners as $index => $owner)
                                    <tr>
                                        <td class="py-2 text-gray-500">{{ $index + 1 }}</td>
                                        <td class="py-2">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $owner->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $owner->business_name }}</div>
                                        </td>
                                        <td class="py-2 text-right text-gray-600 dark:text-gray-400">{{ $owner->halls_count }}</td>
                                        <td class="py-2 text-right font-semibold text-success-600 dark:text-success-400">
                                            {{ number_format((float) $owner->total_revenue, 3) }} OMR
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-gray-500">
                                            {{ __('admin.reports.no_data') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>
            </div>
        @endif

        {{-- Commission Tab --}}
        @if ($activeTab === 'commission')
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                {{-- Commission Summary --}}
                <x-filament::section class="lg:col-span-2">
                    <x-slot name="heading">{{ __('admin.reports.sections.commission_summary') }}</x-slot>
                    <div class="grid grid-cols-3 gap-6">
                        <div class="text-center">
                            <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                                {{ number_format($this->commissionReport['total_commission'], 3) }}
                            </p>
                            <p class="text-sm text-gray-500">{{ __('admin.reports.fields.total_commission') }} (OMR)</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">
                                {{ number_format($this->commissionReport['total_revenue'], 3) }}
                            </p>
                            <p class="text-sm text-gray-500">{{ __('admin.reports.fields.total_revenue') }} (OMR)</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-success-600 dark:text-success-400">
                                {{ $this->commissionReport['commission_rate'] }}%
                            </p>
                            <p class="text-sm text-gray-500">{{ __('admin.reports.fields.avg_rate') }}</p>
                        </div>
                    </div>
                </x-filament::section>

                {{-- Commission by Type --}}
                <x-filament::section>
                    <x-slot name="heading">{{ __('admin.reports.sections.by_type') }}</x-slot>
                    <div class="space-y-4">
                        @forelse ($this->commissionReport['by_type'] ?? [] as $type => $data)
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ ucfirst($type) }}
                                    </span>
                                    <span class="text-sm text-gray-500">{{ $data['count'] }} bookings</span>
                                </div>
                                <p class="text-lg font-semibold text-primary-600 dark:text-primary-400">
                                    {{ number_format($data['commission_amount'], 3) }} OMR
                                </p>
                            </div>
                        @empty
                            <p class="text-gray-500">{{ __('admin.reports.no_data') }}</p>
                        @endforelse
                    </div>
                </x-filament::section>
            </div>
        @endif
    </div>

    {{-- Charts Script --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('livewire:navigated', initCharts);
            document.addEventListener('DOMContentLoaded', initCharts);

            function initCharts() {
                // Destroy existing charts
                Chart.helpers.each(Chart.instances, (instance) => instance.destroy());

                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#9ca3af' : '#6b7280';
                const gridColor = isDark ? '#374151' : '#e5e7eb';

                // Revenue Chart
                const revenueCtx = document.getElementById('revenueChart');
                if (revenueCtx) {
                    new Chart(revenueCtx, {
                        type: 'line',
                        data: {
                            labels: @json($this->revenueTrend['labels'] ?? []),
                            datasets: [
                                {
                                    label: '{{ __("admin.reports.charts.revenue") }}',
                                    data: @json($this->revenueTrend['revenue'] ?? []),
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    fill: true,
                                    tension: 0.4,
                                },
                                {
                                    label: '{{ __("admin.reports.charts.commission") }}',
                                    data: @json($this->revenueTrend['commission'] ?? []),
                                    borderColor: '#6366f1',
                                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
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
                                label: '{{ __("admin.reports.charts.bookings") }}',
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
            Livewire.on('activeTabUpdated', () => setTimeout(initCharts, 100));
        </script>
    @endpush
</x-filament-panels::page>
