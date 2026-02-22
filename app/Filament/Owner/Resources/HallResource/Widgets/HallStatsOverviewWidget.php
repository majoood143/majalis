<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\HallResource\Widgets;

use App\Models\Hall;
use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HallStatsOverviewWidget extends BaseWidget
{
    public ?Model $record = null;
    protected static ?string $pollingInterval = '60s';
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        if (!$this->record instanceof Hall) {
            return [];
        }

        return [
            $this->getTotalBookingsStat(),
            $this->getTotalRevenueStat(),
            $this->getAverageRatingStat(),
            $this->getOccupancyRateStat(),
            $this->getPendingBookingsStat(),
            $this->getCompletedBookingsStat(),
        ];
    }

    protected function getTotalBookingsStat(): Stat
    {
        $currentMonthBookings = (int) $this->record->bookings()
            ->whereMonth('booking_date', now()->month)
            ->whereYear('booking_date', now()->year)
            ->count();

        $previousMonthBookings = (int) $this->record->bookings()
            ->whereMonth('booking_date', now()->subMonth()->month)
            ->whereYear('booking_date', now()->subMonth()->year)
            ->count();

        $percentageChange = $previousMonthBookings > 0
            ? round((($currentMonthBookings - $previousMonthBookings) / $previousMonthBookings) * 100, 1)
            : ($currentMonthBookings > 0 ? 100 : 0);

        $totalBookings = (int) $this->record->bookings()->count();

        $description = $percentageChange >= 0
            ? __('widgets.hall-stats-overview.percent_increase_month', ['percent' => abs($percentageChange)])
            : __('widgets.hall-stats-overview.percent_decrease_month', ['percent' => abs($percentageChange)]);

        $chartData = $this->getBookingsChartData();

        return Stat::make(__('widgets.hall-stats-overview.total_bookings'), (string) $totalBookings)
            ->description($description)
            ->descriptionIcon($percentageChange >= 0
                ? 'heroicon-m-arrow-trending-up'
                : 'heroicon-m-arrow-trending-down')
            ->color($percentageChange >= 0 ? 'success' : 'danger')
            ->chart($chartData);
    }

    protected function getTotalRevenueStat(): Stat
    {
        $currentMonthRevenue = (float) $this->record->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereMonth('booking_date', now()->month)
            ->whereYear('booking_date', now()->year)
            ->sum('total_amount');

        $previousMonthRevenue = (float) $this->record->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereMonth('booking_date', now()->subMonth()->month)
            ->whereYear('booking_date', now()->subMonth()->year)
            ->sum('total_amount');

        $percentageChange = $previousMonthRevenue > 0
            ? round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 1)
            : ($currentMonthRevenue > 0 ? 100 : 0);

        $totalRevenue = (float) $this->record->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->sum('total_amount');

        $description = $percentageChange >= 0
            ? __('widgets.hall-stats-overview.percent_increase_vs_last_month', ['percent' => abs($percentageChange)])
            : __('widgets.hall-stats-overview.percent_decrease_vs_last_month', ['percent' => abs($percentageChange)]);

        $chartData = $this->getRevenueChartData();

        return Stat::make(__('widgets.hall-stats-overview.total_revenue'), number_format($totalRevenue, 3) . ' ' . __('common.currency.omr'))
            ->description($description)
            ->descriptionIcon($percentageChange >= 0
                ? 'heroicon-m-arrow-trending-up'
                : 'heroicon-m-arrow-trending-down')
            ->color($percentageChange >= 0 ? 'success' : 'danger')
            ->chart($chartData);
    }

    protected function getAverageRatingStat(): Stat
    {
        $reviewsCount = (int) $this->record->reviews()
            ->where('is_approved', true)
            ->count();

        $averageRating = $reviewsCount > 0
            ? (float) $this->record->reviews()
                ->where('is_approved', true)
                ->avg('rating')
            : 0.0;

        $color = match (true) {
            $averageRating >= 4.5 => 'success',
            $averageRating >= 3.5 => 'info',
            $averageRating >= 2.5 => 'warning',
            default => 'danger',
        };

        $description = $reviewsCount > 0
            ? __('widgets.hall-stats-overview.based_on_reviews', ['count' => $reviewsCount])
            : __('widgets.hall-stats-overview.no_reviews_yet');

        return Stat::make(__('widgets.hall-stats-overview.average_rating'), number_format($averageRating, 1) . ' / 5.0')
            ->description($description)
            ->descriptionIcon('heroicon-m-star')
            ->color($color);
    }

    protected function getOccupancyRateStat(): Stat
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $daysInMonth = (int) now()->daysInMonth;

        $slotsPerDay = 4;
        $totalSlots = $daysInMonth * $slotsPerDay;

        $bookedSlots = (int) $this->record->bookings()
            ->whereIn('status', ['confirmed', 'completed', 'pending'])
            ->whereBetween('booking_date', [$startOfMonth, $endOfMonth])
            ->count();

        $occupancyRate = $totalSlots > 0
            ? round(($bookedSlots / $totalSlots) * 100, 1)
            : 0.0;

        $color = match (true) {
            $occupancyRate >= 70 => 'success',
            $occupancyRate >= 40 => 'warning',
            default => 'danger',
        };

        $description = __('widgets.hall-stats-overview.slots_this_month', [
            'booked' => $bookedSlots,
            'total' => $totalSlots,
        ]);

        return Stat::make(__('widgets.hall-stats-overview.occupancy_rate'), $occupancyRate . '%')
            ->description($description)
            ->descriptionIcon('heroicon-m-calendar-days')
            ->color($color);
    }

    protected function getPendingBookingsStat(): Stat
    {
        $pendingCount = (int) $this->record->bookings()
            ->where('status', 'pending')
            ->count();

        $pendingUpcoming = (int) $this->record->bookings()
            ->where('status', 'pending')
            ->where('booking_date', '>=', now()->toDateString())
            ->count();

        $description = __('widgets.hall-stats-overview.upcoming_count', ['count' => $pendingUpcoming]);

        return Stat::make(__('widgets.hall-stats-overview.pending_bookings'), (string) $pendingCount)
            ->description($description)
            ->descriptionIcon('heroicon-m-clock')
            ->color($pendingCount > 0 ? 'warning' : 'gray');
    }

    protected function getCompletedBookingsStat(): Stat
    {
        $completedCount = (int) $this->record->bookings()
            ->where('status', 'completed')
            ->count();

        $thisMonthCompleted = (int) $this->record->bookings()
            ->where('status', 'completed')
            ->whereMonth('booking_date', now()->month)
            ->whereYear('booking_date', now()->year)
            ->count();

        $description = __('widgets.hall-stats-overview.this_month_count', ['count' => $thisMonthCompleted]);

        return Stat::make(__('widgets.hall-stats-overview.completed_bookings'), (string) $completedCount)
            ->description($description)
            ->descriptionIcon('heroicon-m-check-circle')
            ->color('success');
    }

    protected function getBookingsChartData(): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = (int) $this->record->bookings()
                ->whereDate('booking_date', $date)
                ->count();
            $data[] = $count;
        }

        return $data;
    }

    protected function getRevenueChartData(): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = (float) $this->record->bookings()
                ->whereIn('status', ['confirmed', 'completed'])
                ->whereIn('payment_status', ['paid', 'partial'])
                ->whereDate('booking_date', $date)
                ->sum('total_amount');
            $data[] = round($revenue, 2);
        }

        return $data;
    }
}
