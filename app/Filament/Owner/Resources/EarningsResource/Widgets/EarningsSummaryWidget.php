<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\EarningsResource\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * EarningsSummaryWidget
 *
 * Displays summary statistics for owner earnings at the top of the
 * earnings list page. Shows key financial metrics.
 *
 * Metrics displayed:
 * - Total Earnings (net payout)
 * - This Month's Earnings
 * - Average Per Booking
 * - Commission Paid
 *
 * @package App\Filament\Owner\Resources\EarningsResource\Widgets
 */
class EarningsSummaryWidget extends BaseWidget
{
    /**
     * Widget column span.
     *
     * @var int|string|array
     */
    protected int|string|array $columnSpan = 'full';

    /**
     * Polling interval (auto-refresh).
     *
     * @var string|null
     */
    protected static ?string $pollingInterval = '60s';

    /**
     * Get the stats to display.
     *
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $user = Auth::user();

        // Base query for owner's paid bookings
        $baseQuery = Booking::whereHas('hall', function ($q) use ($user): void {
            $q->where('owner_id', $user->id);
        })
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid');

        // Total earnings (all time)
        $totalEarnings = (float) (clone $baseQuery)->sum('owner_payout');

        // This month earnings
        $monthEarnings = (float) (clone $baseQuery)
            ->whereMonth('booking_date', now()->month)
            ->whereYear('booking_date', now()->year)
            ->sum('owner_payout');

        // Last month earnings for comparison
        $lastMonthEarnings = (float) (clone $baseQuery)
            ->whereMonth('booking_date', now()->subMonth()->month)
            ->whereYear('booking_date', now()->subMonth()->year)
            ->sum('owner_payout');

        // Calculate month-over-month change
        $monthChange = $lastMonthEarnings > 0
            ? (($monthEarnings - $lastMonthEarnings) / $lastMonthEarnings) * 100
            : ($monthEarnings > 0 ? 100 : 0);

        // Total bookings count
        $totalBookings = (int) (clone $baseQuery)->count();

        // Average earnings per booking
        $avgPerBooking = $totalBookings > 0 ? $totalEarnings / $totalBookings : 0;

        // Total commission paid
        $totalCommission = (float) (clone $baseQuery)->sum('commission_amount');

        // Gross revenue
        $grossRevenue = (float) (clone $baseQuery)->sum('total_amount');

        // This week earnings
        $weekEarnings = (float) (clone $baseQuery)
            ->whereBetween('booking_date', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])
            ->sum('owner_payout');

        // Create stats array
        return [
            // Total Net Earnings
            Stat::make(
                __('owner.earnings.stat_total'),
                number_format($totalEarnings, 3) . ' OMR'
            )
                ->description(__('owner.earnings.stat_total_desc', ['count' => $totalBookings]))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart($this->getEarningsChartData($user)),

            // This Month
            Stat::make(
                __('owner.earnings.stat_month'),
                number_format($monthEarnings, 3) . ' OMR'
            )
                ->description(
                    $monthChange >= 0
                        ? __('owner.earnings.stat_increase', ['percent' => number_format(abs($monthChange), 1)])
                        : __('owner.earnings.stat_decrease', ['percent' => number_format(abs($monthChange), 1)])
                )
                ->descriptionIcon($monthChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthChange >= 0 ? 'success' : 'danger')
                ->chart($this->getMonthlyChartData($user)),

            // This Week
            Stat::make(
                __('owner.earnings.stat_week'),
                number_format($weekEarnings, 3) . ' OMR'
            )
                ->description(__('owner.earnings.stat_week_desc'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            // Average Per Booking
            Stat::make(
                __('owner.earnings.stat_average'),
                number_format($avgPerBooking, 3) . ' OMR'
            )
                ->description(__('owner.earnings.stat_average_desc'))
                ->descriptionIcon('heroicon-m-calculator')
                ->color('primary'),

            // Gross Revenue
            Stat::make(
                __('owner.earnings.stat_gross'),
                number_format($grossRevenue, 3) . ' OMR'
            )
                ->description(__('owner.earnings.stat_gross_desc'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('gray'),

            // Total Commission
            Stat::make(
                __('owner.earnings.stat_commission'),
                number_format($totalCommission, 3) . ' OMR'
            )
                ->description(__('owner.earnings.stat_commission_desc', [
                    'percent' => $grossRevenue > 0
                        ? number_format(($totalCommission / $grossRevenue) * 100, 1)
                        : 0,
                ]))
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('warning'),
        ];
    }

    /**
     * Get earnings chart data for the last 7 days.
     *
     * @param \App\Models\User $user
     * @return array<int>
     */
    protected function getEarningsChartData($user): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);

            $dayEarnings = (float) Booking::whereHas('hall', function ($q) use ($user): void {
                $q->where('owner_id', $user->id);
            })
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('payment_status', 'paid')
                ->whereDate('booking_date', $date)
                ->sum('owner_payout');

            $data[] = (int) round($dayEarnings);
        }

        return $data;
    }

    /**
     * Get monthly chart data for the last 6 months.
     *
     * @param \App\Models\User $user
     * @return array<int>
     */
    protected function getMonthlyChartData($user): array
    {
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);

            $monthEarnings = (float) Booking::whereHas('hall', function ($q) use ($user): void {
                $q->where('owner_id', $user->id);
            })
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('payment_status', 'paid')
                ->whereMonth('booking_date', $month->month)
                ->whereYear('booking_date', $month->year)
                ->sum('owner_payout');

            $data[] = (int) round($monthEarnings);
        }

        return $data;
    }
}
