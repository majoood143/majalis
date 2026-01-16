<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\BookingResource\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * BookingStatsWidget for Owner Panel
 *
 * Displays key booking statistics for the authenticated owner's halls.
 * Shows counts and revenue metrics relevant to hall owners.
 */
class BookingStatsWidget extends StatsOverviewWidget
{
    /**
     * The polling interval in seconds.
     */
    protected static ?string $pollingInterval = '60s';

    /**
     * Number of columns for the widget layout.
     */
    protected int|string|array $columnSpan = 'full';

    /**
     * Get the stats for this widget.
     *
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $ownerId = Auth::id();

        // Base query scoped to owner's halls
        $baseQuery = Booking::query()
            ->whereHas('hall', fn($q) => $q->where('owner_id', $ownerId));

        // Calculate metrics
        $totalBookings = (clone $baseQuery)->count();
        $pendingBookings = (clone $baseQuery)->where('status', 'pending')->count();
        $confirmedBookings = (clone $baseQuery)->where('status', 'confirmed')->count();
        $upcomingBookings = (clone $baseQuery)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('booking_date', '>=', now()->toDateString())
            ->count();

        // Revenue metrics (using owner_payout, not total_amount)
        $totalRevenue = (clone $baseQuery)
            ->whereIn('payment_status', ['paid', 'partial'])
            ->sum('owner_payout');

        $thisMonthRevenue = (clone $baseQuery)
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('owner_payout');

        $lastMonthRevenue = (clone $baseQuery)
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('owner_payout');

        // Calculate trend
        $revenueTrend = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : ($thisMonthRevenue > 0 ? 100 : 0);

        // Pending balance due (advance payments not yet fully paid)
        $pendingBalance = (clone $baseQuery)
            ->where('payment_type', 'advance')
            ->where('balance_due', '>', 0)
            ->whereNull('balance_paid_at')
            ->whereIn('status', ['pending', 'confirmed'])
            ->sum('balance_due');

        // Today's events
        $todayEvents = (clone $baseQuery)
            ->where('booking_date', now()->toDateString())
            ->whereIn('status', ['confirmed', 'pending'])
            ->count();

        return [
            // Pending Bookings (needs attention)
            Stat::make(__('Pending Approval'), (string) $pendingBookings)
                ->description($pendingBookings > 0
                    ? __(':count bookings need your review', ['count' => $pendingBookings])
                    : __('All bookings reviewed'))
                ->descriptionIcon($pendingBookings > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($pendingBookings > 0 ? 'warning' : 'success')
                ->chart($this->getPendingTrend($ownerId)),

            // Upcoming Events
            Stat::make(__('Upcoming Events'), (string) $upcomingBookings)
                ->description($todayEvents > 0
                    ? __(':count event(s) today', ['count' => $todayEvents])
                    : __('Confirmed upcoming bookings'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color($todayEvents > 0 ? 'success' : 'info')
                ->chart($this->getUpcomingTrend($ownerId)),

            // This Month Revenue
            Stat::make(__('This Month Earnings'), 'OMR ' . number_format((float) $thisMonthRevenue, 3))
                ->description($revenueTrend >= 0
                    ? __(':percent% increase from last month', ['percent' => abs($revenueTrend)])
                    : __(':percent% decrease from last month', ['percent' => abs($revenueTrend)]))
                ->descriptionIcon($revenueTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueTrend >= 0 ? 'success' : 'danger')
                ->chart($this->getRevenueTrend($ownerId)),

            // Pending Balance Collection
            Stat::make(__('Balance to Collect'), 'OMR ' . number_format((float) $pendingBalance, 3))
                ->description($pendingBalance > 0
                    ? __('From advance payment bookings')
                    : __('No pending balances'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($pendingBalance > 0 ? 'warning' : 'gray'),
        ];
    }

    /**
     * Get trend data for pending bookings (last 7 days).
     *
     * @param int $ownerId
     * @return array<int>
     */
    private function getPendingTrend(int $ownerId): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Booking::query()
                ->whereHas('hall', fn($q) => $q->where('owner_id', $ownerId))
                ->where('status', 'pending')
                ->whereDate('created_at', $date)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    /**
     * Get trend data for upcoming bookings (next 7 days).
     *
     * @param int $ownerId
     * @return array<int>
     */
    private function getUpcomingTrend(int $ownerId): array
    {
        $data = [];
        for ($i = 0; $i < 7; $i++) {
            $date = now()->addDays($i);
            $count = Booking::query()
                ->whereHas('hall', fn($q) => $q->where('owner_id', $ownerId))
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereDate('booking_date', $date)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    /**
     * Get trend data for revenue (last 7 days).
     *
     * @param int $ownerId
     * @return array<int>
     */
    private function getRevenueTrend(int $ownerId): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = Booking::query()
                ->whereHas('hall', fn($q) => $q->where('owner_id', $ownerId))
                ->whereIn('payment_status', ['paid', 'partial'])
                ->whereDate('created_at', $date)
                ->sum('owner_payout');
            $data[] = (int) $revenue;
        }
        return $data;
    }
}
