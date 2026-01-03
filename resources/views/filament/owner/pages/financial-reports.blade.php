<x-filament-panels::page>
    {{-- Filter Form --}}
    <div class="mb-6">
        {{ $this->form }}
    </div>

    {{-- Report Content --}}
    @if(!empty($reportData))
        {{-- Monthly Report --}}
        @if($reportData['type'] === 'monthly')
            <div class="space-y-6">
                {{-- Summary Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    {{-- Total Bookings --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('owner.reports.total_bookings') }}
                        </div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $reportData['summary']['total_bookings'] }}
                        </div>
                    </div>

                    {{-- Gross Revenue --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('owner.reports.gross_revenue') }}
                        </div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($reportData['summary']['gross_revenue'], 3) }} OMR
                        </div>
                    </div>

                    {{-- Hall Revenue --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('owner.reports.hall_revenue') }}
                        </div>
                        <div class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ number_format($reportData['summary']['hall_revenue'], 3) }} OMR
                        </div>
                    </div>

                    {{-- Services Revenue --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('owner.reports.services_revenue') }}
                        </div>
                        <div class="mt-1 text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ number_format($reportData['summary']['services_revenue'], 3) }} OMR
                        </div>
                    </div>

                    {{-- Commission --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('owner.reports.commission') }}
                        </div>
                        <div class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">
                            -{{ number_format($reportData['summary']['total_commission'], 3) }} OMR
                        </div>
                    </div>

                    {{-- Net Earnings --}}
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 shadow-sm border border-green-200 dark:border-green-700">
                        <div class="text-sm font-medium text-green-600 dark:text-green-400">
                            {{ __('owner.reports.net_earnings') }}
                        </div>
                        <div class="mt-1 text-2xl font-bold text-green-700 dark:text-green-300">
                            {{ number_format($reportData['summary']['net_earnings'], 3) }} OMR
                        </div>
                    </div>
                </div>

                {{-- Charts Row --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Daily Revenue Chart --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            {{ __('owner.reports.daily_revenue') }}
                        </h3>
                        <div class="h-64">
                            <canvas id="dailyRevenueChart"></canvas>
                        </div>
                    </div>

                    {{-- Hall Breakdown --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            {{ __('owner.reports.hall_breakdown') }}
                        </h3>
                        @if(count($reportData['hall_breakdown']) > 0)
                            <div class="space-y-3">
                                @foreach($reportData['hall_breakdown'] as $hall)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                {{ $hall['hall_name'] }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $hall['bookings'] }} {{ __('owner.reports.bookings') }}
                                            </div>
                                        </div>
                                        <div class="text-lg font-bold text-green-600 dark:text-green-400">
                                            {{ number_format($hall['revenue'], 3) }} OMR
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                {{ __('owner.reports.no_data') }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Time Slot Breakdown --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('owner.reports.slot_breakdown') }}
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach(['morning', 'afternoon', 'evening', 'full_day'] as $slot)
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $reportData['slot_breakdown'][$slot]['count'] ?? 0 }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __("owner.slots.{$slot}") }}
                                </div>
                                <div class="mt-1 text-sm font-medium text-green-600 dark:text-green-400">
                                    {{ number_format($reportData['slot_breakdown'][$slot]['revenue'] ?? 0, 3) }} OMR
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        {{-- Yearly Report --}}
        @elseif($reportData['type'] === 'yearly')
            <div class="space-y-6">
                {{-- Year Summary Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('owner.reports.total_bookings') }}
                        </div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $reportData['summary']['total_bookings'] }}
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('owner.reports.gross_revenue') }}
                        </div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($reportData['summary']['gross_revenue'], 3) }} OMR
                        </div>
                    </div>

                    <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 shadow-sm border border-green-200 dark:border-green-700">
                        <div class="text-sm font-medium text-green-600 dark:text-green-400">
                            {{ __('owner.reports.net_earnings') }}
                        </div>
                        <div class="mt-1 text-2xl font-bold text-green-700 dark:text-green-300">
                            {{ number_format($reportData['summary']['net_earnings'], 3) }} OMR
                        </div>
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 shadow-sm border border-blue-200 dark:border-blue-700">
                        <div class="text-sm font-medium text-blue-600 dark:text-blue-400">
                            {{ __('owner.reports.avg_monthly') }}
                        </div>
                        <div class="mt-1 text-2xl font-bold text-blue-700 dark:text-blue-300">
                            {{ number_format($reportData['summary']['avg_monthly'], 3) }} OMR
                        </div>
                    </div>
                </div>

                {{-- Monthly Chart --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('owner.reports.monthly_revenue') }}
                    </h3>
                    <div class="h-72">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                </div>

                {{-- Monthly Table --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ __('owner.reports.monthly_breakdown') }}
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('owner.reports.month') }}
                                    </th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('owner.reports.bookings') }}
                                    </th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('owner.reports.gross') }}
                                    </th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('owner.reports.commission') }}
                                    </th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('owner.reports.net') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($reportData['monthly_data'] as $month)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $month['month_full'] }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-600 dark:text-gray-300">
                                            {{ $month['bookings'] }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-600 dark:text-gray-300">
                                            {{ number_format($month['gross'], 3) }} OMR
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right text-red-600 dark:text-red-400">
                                            -{{ number_format($month['commission'], 3) }} OMR
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right font-medium text-green-600 dark:text-green-400">
                                            {{ number_format($month['net'], 3) }} OMR
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-100 dark:bg-gray-700">
                                <tr class="font-bold">
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ __('owner.reports.total') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-white">
                                        {{ $reportData['summary']['total_bookings'] }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                                        {{ number_format($reportData['summary']['gross_revenue'], 3) }} OMR
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right text-red-600 dark:text-red-400">
                                        -{{ number_format($reportData['summary']['total_commission'], 3) }} OMR
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right text-green-600 dark:text-green-400">
                                        {{ number_format($reportData['summary']['net_earnings'], 3) }} OMR
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Payout Summary --}}
                @if(isset($reportData['payout_summary']))
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            {{ __('owner.reports.payout_summary') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <div class="text-sm text-green-600 dark:text-green-400">
                                    {{ __('owner.reports.total_received') }}
                                </div>
                                <div class="text-xl font-bold text-green-700 dark:text-green-300">
                                    {{ number_format($reportData['payout_summary']['total_received'], 3) }} OMR
                                </div>
                                <div class="text-sm text-green-600 dark:text-green-400">
                                    {{ $reportData['payout_summary']['payout_count'] }} {{ __('owner.reports.payouts') }}
                                </div>
                            </div>
                            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                <div class="text-sm text-yellow-600 dark:text-yellow-400">
                                    {{ __('owner.reports.pending') }}
                                </div>
                                <div class="text-xl font-bold text-yellow-700 dark:text-yellow-300">
                                    {{ number_format($reportData['payout_summary']['pending'], 3) }} OMR
                                </div>
                            </div>
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <div class="text-sm text-blue-600 dark:text-blue-400">
                                    {{ __('owner.reports.difference') }}
                                </div>
                                <div class="text-xl font-bold text-blue-700 dark:text-blue-300">
                                    {{ number_format($reportData['summary']['net_earnings'] - $reportData['payout_summary']['total_received'] - $reportData['payout_summary']['pending'], 3) }} OMR
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        {{-- Comparison Report --}}
        @elseif($reportData['type'] === 'comparison')
            <div class="space-y-6">
                {{-- Comparison Cards --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Current Month --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            {{ $reportData['current']['period'] }}
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">{{ __('owner.reports.bookings') }}</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ $reportData['current']['bookings'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">{{ __('owner.reports.gross') }}</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ number_format($reportData['current']['gross'], 3) }} OMR</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">{{ __('owner.reports.commission') }}</span>
                                <span class="font-bold text-red-600 dark:text-red-400">-{{ number_format($reportData['current']['commission'], 3) }} OMR</span>
                            </div>
                            <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                                <span class="font-medium text-gray-900 dark:text-white">{{ __('owner.reports.net') }}</span>
                                <span class="text-xl font-bold text-green-600 dark:text-green-400">{{ number_format($reportData['current']['net'], 3) }} OMR</span>
                            </div>
                        </div>
                    </div>

                    {{-- Previous Month --}}
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
                            {{ $reportData['previous']['period'] }}
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">{{ __('owner.reports.bookings') }}</span>
                                <span class="font-bold text-gray-700 dark:text-gray-300">{{ $reportData['previous']['bookings'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">{{ __('owner.reports.gross') }}</span>
                                <span class="font-bold text-gray-700 dark:text-gray-300">{{ number_format($reportData['previous']['gross'], 3) }} OMR</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">{{ __('owner.reports.commission') }}</span>
                                <span class="font-bold text-red-500 dark:text-red-400">-{{ number_format($reportData['previous']['commission'], 3) }} OMR</span>
                            </div>
                            <div class="flex justify-between items-center pt-4 border-t border-gray-300 dark:border-gray-600">
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('owner.reports.net') }}</span>
                                <span class="text-xl font-bold text-gray-700 dark:text-gray-300">{{ number_format($reportData['previous']['net'], 3) }} OMR</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Change Indicators --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('owner.reports.month_over_month') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Bookings Change --}}
                        <div class="text-center">
                            <div class="text-3xl font-bold {{ $reportData['changes']['bookings'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $reportData['changes']['bookings'] >= 0 ? '+' : '' }}{{ number_format($reportData['changes']['bookings'], 1) }}%
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('owner.reports.bookings_change') }}
                            </div>
                            @if($reportData['changes']['bookings'] >= 0)
                                <x-heroicon-s-arrow-trending-up class="w-8 h-8 mx-auto mt-2 text-green-500" />
                            @else
                                <x-heroicon-s-arrow-trending-down class="w-8 h-8 mx-auto mt-2 text-red-500" />
                            @endif
                        </div>

                        {{-- Gross Change --}}
                        <div class="text-center">
                            <div class="text-3xl font-bold {{ $reportData['changes']['gross'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $reportData['changes']['gross'] >= 0 ? '+' : '' }}{{ number_format($reportData['changes']['gross'], 1) }}%
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('owner.reports.revenue_change') }}
                            </div>
                            @if($reportData['changes']['gross'] >= 0)
                                <x-heroicon-s-arrow-trending-up class="w-8 h-8 mx-auto mt-2 text-green-500" />
                            @else
                                <x-heroicon-s-arrow-trending-down class="w-8 h-8 mx-auto mt-2 text-red-500" />
                            @endif
                        </div>

                        {{-- Net Change --}}
                        <div class="text-center">
                            <div class="text-3xl font-bold {{ $reportData['changes']['net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $reportData['changes']['net'] >= 0 ? '+' : '' }}{{ number_format($reportData['changes']['net'], 1) }}%
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('owner.reports.earnings_change') }}
                            </div>
                            @if($reportData['changes']['net'] >= 0)
                                <x-heroicon-s-arrow-trending-up class="w-8 h-8 mx-auto mt-2 text-green-500" />
                            @else
                                <x-heroicon-s-arrow-trending-down class="w-8 h-8 mx-auto mt-2 text-red-500" />
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        {{-- Hall Report --}}
        @elseif($reportData['type'] === 'hall')
            <div class="space-y-6">
                {{-- Summary --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('owner.reports.total_halls') }}
                        </div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $reportData['summary']['total_halls'] }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('owner.reports.total_bookings') }}
                        </div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $reportData['summary']['total_bookings'] }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('owner.reports.gross_revenue') }}
                        </div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($reportData['summary']['gross_revenue'], 3) }} OMR
                        </div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 shadow-sm border border-green-200 dark:border-green-700">
                        <div class="text-sm font-medium text-green-600 dark:text-green-400">
                            {{ __('owner.reports.net_earnings') }}
                        </div>
                        <div class="mt-1 text-2xl font-bold text-green-700 dark:text-green-300">
                            {{ number_format($reportData['summary']['net_earnings'], 3) }} OMR
                        </div>
                    </div>
                </div>

                {{-- Hall Performance Cards --}}
                @foreach($reportData['hall_performance'] as $hall)
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $hall['hall_name'] }}
                            </h3>
                            <span class="text-xl font-bold text-green-600 dark:text-green-400">
                                {{ number_format($hall['net_earnings'], 3) }} OMR
                            </span>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('owner.reports.bookings') }}</div>
                                <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $hall['bookings_count'] }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('owner.reports.gross') }}</div>
                                <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($hall['gross_revenue'], 3) }} OMR</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('owner.reports.commission') }}</div>
                                <div class="text-lg font-semibold text-red-600 dark:text-red-400">-{{ number_format($hall['commission'], 3) }} OMR</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('owner.reports.avg_booking') }}</div>
                                <div class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{ number_format($hall['avg_booking'], 3) }} OMR</div>
                            </div>
                        </div>

                        {{-- Monthly Trend --}}
                        <div class="h-32">
                            <canvas id="hallChart{{ $hall['hall_id'] }}" 
                                    data-values="{{ json_encode(array_values($hall['monthly_trend'])) }}">
                            </canvas>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @else
        {{-- No Data State --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center shadow-sm border border-gray-200 dark:border-gray-700">
            <x-heroicon-o-chart-bar class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500 mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                {{ __('owner.reports.no_data_title') }}
            </h3>
            <p class="text-gray-500 dark:text-gray-400">
                {{ __('owner.reports.no_data_desc') }}
            </p>
        </div>
    @endif

    {{-- Chart Scripts --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(isset($reportData['daily_data']))
                // Daily Revenue Chart
                const dailyCtx = document.getElementById('dailyRevenueChart');
                if (dailyCtx) {
                    new Chart(dailyCtx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode(array_column($reportData['daily_data'], 'date')) !!},
                            datasets: [{
                                label: '{{ __("owner.reports.net_earnings") }}',
                                data: {!! json_encode(array_column($reportData['daily_data'], 'net')) !!},
                                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                                borderColor: 'rgb(34, 197, 94)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return value.toFixed(3) + ' OMR';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            @endif

            @if(isset($reportData['monthly_data']))
                // Monthly Revenue Chart
                const monthlyCtx = document.getElementById('monthlyRevenueChart');
                if (monthlyCtx) {
                    new Chart(monthlyCtx, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode(array_column($reportData['monthly_data'], 'month')) !!},
                            datasets: [
                                {
                                    label: '{{ __("owner.reports.gross") }}',
                                    data: {!! json_encode(array_column($reportData['monthly_data'], 'gross')) !!},
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    fill: true,
                                    tension: 0.3
                                },
                                {
                                    label: '{{ __("owner.reports.net") }}',
                                    data: {!! json_encode(array_column($reportData['monthly_data'], 'net')) !!},
                                    borderColor: 'rgb(34, 197, 94)',
                                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                    fill: true,
                                    tension: 0.3
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
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return value.toFixed(0) + ' OMR';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            @endif

            // Hall performance charts
            document.querySelectorAll('[id^="hallChart"]').forEach(function(canvas) {
                const values = JSON.parse(canvas.dataset.values || '[]');
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                
                new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Revenue',
                            data: values,
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { display: false },
                            x: { display: true }
                        }
                    }
                });
            });
        });
    </script>
    @endpush
</x-filament-panels::page>
