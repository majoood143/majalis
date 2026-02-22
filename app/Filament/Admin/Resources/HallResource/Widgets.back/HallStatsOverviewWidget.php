<?php

declare(strict_types=1);

/**
 * HallStatsOverviewWidget - Key Performance Metrics for Individual Halls
 *
 * Displays comprehensive statistics for a specific hall including:
 * - Total bookings count with trend comparison
 * - Revenue generated (confirmed/completed bookings)
 * - Average customer rating
 * - Occupancy rate calculation
 *
 * This widget is designed to be used on the ViewHall page to provide
 * hall owners and administrators with quick performance insights.
 *
 * @package App\Filament\Admin\Resources\HallResource\Widgets
 * @version 1.0.0
 * @author Majalis Development Team
 */

namespace App\Filament\Admin\Resources\HallResource\Widgets;

use App\Models\Hall;
use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HallStatsOverviewWidget extends BaseWidget
{
    /**
     * The hall record being viewed.
     * This is automatically injected from the ViewHall page.
     *
     * @var Hall|Model|null
     */
    public ?Model $record = null;

    /**
     * Polling interval for auto-refresh.
     * Set to 60 seconds to balance freshness with performance.
     *
     * @var string|null
     */
    protected static ?string $pollingInterval = '60s';

    /**
     * Widget column span - full width for better visibility.
     *
     * @var int|string|array
     */
    protected int|string|array $columnSpan = 'full';

    /**
     * Get the statistics to display in the widget.
     *
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        // Early return if no record is available
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

    /**
     * Calculate and return total bookings statistic.
     *
     * Includes comparison with previous month for trend analysis.
     *
     * @return Stat
     */
    protected function getTotalBookingsStat(): Stat
    {
        // Current month bookings
        $currentMonthBookings = (int) $this->record->bookings()
            ->whereMonth('booking_date', now()->month)
            ->whereYear('booking_date', now()->year)
            ->count();

        // Previous month bookings for comparison
        $previousMonthBookings = (int) $this->record->bookings()
            ->whereMonth('booking_date', now()->subMonth()->month)
            ->whereYear('booking_date', now()->subMonth()->year)
            ->count();

        // Calculate percentage change
        $percentageChange = $previousMonthBookings > 0
            ? round((($currentMonthBookings - $previousMonthBookings) / $previousMonthBookings) * 100, 1)
            : ($currentMonthBookings > 0 ? 100 : 0);

        // Total all-time bookings
        $totalBookings = (int) $this->record->bookings()->count();

        // Build the description text
        $description = $percentageChange >= 0
            ? __(':percent% increase this month', ['percent' => abs($percentageChange)])
            : __(':percent% decrease this month', ['percent' => abs($percentageChange)]);

        // Generate mini chart data (last 7 days)
        $chartData = $this->getBookingsChartData();

        return Stat::make(__('Total Bookings'), (string) $totalBookings)
            ->description($description)
            ->descriptionIcon($percentageChange >= 0
                ? 'heroicon-m-arrow-trending-up'
                : 'heroicon-m-arrow-trending-down')
            ->color($percentageChange >= 0 ? 'success' : 'danger')
            ->chart($chartData);
    }

    /**
     * Calculate and return total revenue statistic.
     *
     * Only counts confirmed and completed bookings with paid status.
     *
     * @return Stat
     */
    protected function getTotalRevenueStat(): Stat
    {
        // Current month revenue
        $currentMonthRevenue = (float) $this->record->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereMonth('booking_date', now()->month)
            ->whereYear('booking_date', now()->year)
            ->sum('total_amount');

        // Previous month revenue for comparison
        $previousMonthRevenue = (float) $this->record->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereMonth('booking_date', now()->subMonth()->month)
            ->whereYear('booking_date', now()->subMonth()->year)
            ->sum('total_amount');

        // Calculate percentage change
        $percentageChange = $previousMonthRevenue > 0
            ? round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 1)
            : ($currentMonthRevenue > 0 ? 100 : 0);

        // Total all-time revenue
        $totalRevenue = (float) $this->record->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->sum('total_amount');

        // Build the description text
        $description = $percentageChange >= 0
            ? __(':percent% increase vs last month', ['percent' => abs($percentageChange)])
            : __(':percent% decrease vs last month', ['percent' => abs($percentageChange)]);

        // Generate mini chart data
        $chartData = $this->getRevenueChartData();

        return Stat::make(__('Total Revenue'), number_format($totalRevenue, 3) . ' OMR')
            ->description($description)
            ->descriptionIcon($percentageChange >= 0
                ? 'heroicon-m-arrow-trending-up'
                : 'heroicon-m-arrow-trending-down')
            ->color($percentageChange >= 0 ? 'success' : 'danger')
            ->chart($chartData);
    }

    /**
     * Calculate and return average rating statistic.
     *
     * Uses approved reviews only for accurate representation.
     *
     * @return Stat
     */
    protected function getAverageRatingStat(): Stat
    {
        // Get approved reviews count
        $reviewsCount = (int) $this->record->reviews()
            ->where('is_approved', true)
            ->count();

        // Calculate average rating
        $averageRating = $reviewsCount > 0
            ? (float) $this->record->reviews()
                ->where('is_approved', true)
                ->avg('rating')
            : 0.0;

        // Determine color based on rating
        $color = match (true) {
            $averageRating >= 4.5 => 'success',
            $averageRating >= 3.5 => 'info',
            $averageRating >= 2.5 => 'warning',
            default => 'danger',
        };

        // Build description with review count
        $description = $reviewsCount > 0
            ? __('Based on :count reviews', ['count' => $reviewsCount])
            : __('No reviews yet');

        return Stat::make(__('Average Rating'), number_format($averageRating, 1) . ' / 5.0')
            ->description($description)
            ->descriptionIcon('heroicon-m-star')
            ->color($color);
    }

    /**
     * Calculate and return occupancy rate statistic.
     *
     * Calculates based on confirmed/completed bookings vs available slots.
     * Assumes 4 time slots per day (morning, afternoon, evening, full_day).
     *
     * @return Stat
     */
    protected function getOccupancyRateStat(): Stat
    {
        // Calculate for current month
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $daysInMonth = (int) now()->daysInMonth;

        // Available slots (4 slots per day for the month)
        $slotsPerDay = 4;
        $totalSlots = $daysInMonth * $slotsPerDay;

        // Booked slots this month
        $bookedSlots = (int) $this->record->bookings()
            ->whereIn('status', ['confirmed', 'completed', 'pending'])
            ->whereBetween('booking_date', [$startOfMonth, $endOfMonth])
            ->count();

        // Calculate occupancy rate
        $occupancyRate = $totalSlots > 0
            ? round(($bookedSlots / $totalSlots) * 100, 1)
            : 0.0;

        // Determine color based on occupancy
        $color = match (true) {
            $occupancyRate >= 70 => 'success',
            $occupancyRate >= 40 => 'warning',
            default => 'danger',
        };

        // Build description
        $description = __(':booked of :total slots this month', [
            'booked' => $bookedSlots,
            'total' => $totalSlots,
        ]);

        return Stat::make(__('Occupancy Rate'), $occupancyRate . '%')
            ->description($description)
            ->descriptionIcon('heroicon-m-calendar-days')
            ->color($color);
    }

    /**
     * Get pending bookings count statistic.
     *
     * @return Stat
     */
    protected function getPendingBookingsStat(): Stat
    {
        $pendingCount = (int) $this->record->bookings()
            ->where('status', 'pending')
            ->count();

        $pendingUpcoming = (int) $this->record->bookings()
            ->where('status', 'pending')
            ->where('booking_date', '>=', now()->toDateString())
            ->count();

        $description = __(':count upcoming', ['count' => $pendingUpcoming]);

        return Stat::make(__('Pending Bookings'), (string) $pendingCount)
            ->description($description)
            ->descriptionIcon('heroicon-m-clock')
            ->color($pendingCount > 0 ? 'warning' : 'gray');
    }

    /**
     * Get completed bookings count statistic.
     *
     * @return Stat
     */
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

        $description = __(':count this month', ['count' => $thisMonthCompleted]);

        return Stat::make(__('Completed Bookings'), (string) $completedCount)
            ->description($description)
            ->descriptionIcon('heroicon-m-check-circle')
            ->color('success');
    }

    /**
     * Generate chart data for bookings over the last 7 days.
     *
     * @return array<int>
     */
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

    /**
     * Generate chart data for revenue over the last 7 days.
     *
     * @return array<float>
     */
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
