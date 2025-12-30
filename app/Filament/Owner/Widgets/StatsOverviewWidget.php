<?php

declare(strict_types=1);

namespace App\Filament\Owner\Widgets;

use App\Models\Booking;
use App\Models\Hall;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    /**
     * Widget polling interval
     */
    protected static ?string $pollingInterval = '30s';

    /**
     * Widget column span
     */
    protected int | string | array $columnSpan = 'full';


    /**
     * Filter properties
     */
    //#[Url]
    public ?string $dateRange = 'month';

    //#[Url]
    public ?int $hallId = null;

    /**
     * Get available filters
     */
    public function getFilters(): array
    {
        return [
            'start_date' => match ($this->dateRange) {
                'week' => now()->startOfWeek(),
                'month' => now()->startOfMonth(),
                'year' => now()->startOfYear(),
                default => now()->startOfMonth(),
            },
            'end_date' => match ($this->dateRange) {
                'week' => now()->endOfWeek(),
                'month' => now()->endOfMonth(),
                'year' => now()->endOfYear(),
                default => now()->endOfMonth(),
            },
            'hall_id' => $this->hallId,
        ];
    }
    /**
     * Get widget stats
     */
    protected function getStats(): array
    {
        $user = Auth::user();
        $filters = $this->getFilters();

        // Date range from filters or default to current month
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();
        $hallId = $filters['hall_id'] ?? null;

        return [
            $this->getTotalRevenueState($user, $startDate, $endDate, $hallId),
            $this->getTotalBookingsStat($user, $startDate, $endDate, $hallId),
            $this->getOccupancyRateStat($user, $startDate, $endDate, $hallId),
            $this->getPendingPaymentsStat($user, $hallId),
        ];
    }

    /**
     * Get total revenue stat
     */
    /**
     * Get total revenue stat
     */
    protected function getTotalRevenueState($user, $startDate, $endDate): Stat
    {
        // ✅ FIXED: Ensure proper type casting with null coalescing
        $revenueQuery = Booking::whereHas('hall', function ($q) use ($user) {
            $q->where('owner_id', $user->id);
        })
            ->where('payment_status', 'paid')
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->sum('total_amount');

        // ✅ FIXED: Double ensure float casting with null safety
        $currentRevenue = (float) ($revenueQuery ?? 0);

        // Calculate previous period for comparison
        $previousStart = Carbon::parse($startDate)->subMonth();
        $previousEnd = Carbon::parse($endDate)->subMonth();

        $previousQuery = Booking::whereHas('hall', function ($q) use ($user) {
            $q->where('owner_id', $user->id);
        })
            ->where('payment_status', 'paid')
            ->whereBetween('booking_date', [$previousStart, $previousEnd])
            ->sum('total_amount');

        // ✅ FIXED: Ensure float casting
        $previousRevenue = (float) ($previousQuery ?? 0);

        $percentageChange = $previousRevenue > 0
            ? round((($currentRevenue - $previousRevenue) / $previousRevenue) * 100, 1)
            : 0;

        $description = $percentageChange > 0
            ? __('owner.stats.revenue_increase', ['percent' => abs($percentageChange)])
            : __('owner.stats.revenue_decrease', ['percent' => abs($percentageChange)]);

        // ✅ FIXED: Format the already-cast float value
        $formattedRevenue = 'OMR ' . number_format($currentRevenue, 3);

        return Stat::make(__('owner.stats.total_revenue'), $formattedRevenue)
            ->description($description)
            ->descriptionIcon($percentageChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($percentageChange > 0 ? 'success' : 'danger')
            ->chart($this->getRevenueChartData($user, $startDate, $endDate));
    }

    /**
     * Get total bookings stat
     */
    protected function getTotalBookingsStat($user, $startDate, $endDate): Stat
    {
        $query = Booking::whereHas('hall', function ($q) use ($user) {
            $q->where('owner_id', $user->id);
        })
            ->whereBetween('booking_date', [$startDate, $endDate]);

        // ✅ FIXED: Ensure integer casting for counts
        $totalBookings = (int) $query->count();
        $confirmedBookings = (int) (clone $query)->where('status', 'confirmed')->count();
        $pendingBookings = (int) (clone $query)->where('status', 'pending')->count();

        $description = __('owner.stats.bookings_breakdown', [
            'confirmed' => $confirmedBookings,
            'pending' => $pendingBookings,
        ]);

        return Stat::make(__('owner.stats.total_bookings'), (string) $totalBookings)
            ->description($description)
            ->descriptionIcon('heroicon-m-calendar-days')
            ->color('info')
            ->chart($this->getBookingsChartData($user, $startDate, $endDate));
    }

    /**
     * Get occupancy rate stat
     */
    protected function getOccupancyRateStat($user, $startDate, $endDate): Stat
    {
        // Calculate total available slots
        $halls = Hall::where('owner_id', $user->id)
            ->where('is_active', true);

        $hallCount = (int) $halls->count();
        $daysInPeriod = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $slotsPerDay = 4; // morning, afternoon, evening, full_day
        $totalSlots = $hallCount * $daysInPeriod * $slotsPerDay;

        // Calculate booked slots
        $bookedSlots = (int) Booking::whereHas('hall', function ($q) use ($user) {
            $q->where('owner_id', $user->id);
        })
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->whereIn('status', ['confirmed', 'completed'])
            ->count();

        $occupancyRate = $totalSlots > 0
            ? round(($bookedSlots / $totalSlots) * 100, 1)
            : 0;

        $color = $occupancyRate >= 70 ? 'success' : ($occupancyRate >= 40 ? 'warning' : 'danger');

        return Stat::make(__('owner.stats.occupancy_rate'), $occupancyRate . '%')
            ->description(__('owner.stats.slots_booked', ['booked' => $bookedSlots, 'total' => $totalSlots]))
            ->descriptionIcon('heroicon-m-chart-bar')
            ->color($color)
            ->chart($this->getOccupancyChartData($user, $startDate, $endDate));
    }

    /**
     * Get pending payments stat
     */
    protected function getPendingPaymentsStat($user): Stat
    {
        // ✅ FIXED: Ensure proper float casting
        $pendingQuery = Booking::whereHas('hall', function ($q) use ($user) {
            $q->where('owner_id', $user->id);
        })
            ->where('payment_status', 'pending')
            ->sum('total_amount');

        $pendingAmount = (float) ($pendingQuery ?? 0);

        $pendingCount = (int) Booking::whereHas('hall', function ($q) use ($user) {
            $q->where('owner_id', $user->id);
        })
            ->where('payment_status', 'pending')
            ->count();

        // ✅ FIXED: Format the already-cast float value
        $formattedAmount = 'OMR ' . number_format($pendingAmount, 3);

        return Stat::make(__('owner.stats.pending_payments'), $formattedAmount)
            ->description(__('owner.stats.pending_count', ['count' => $pendingCount]))
            ->descriptionIcon('heroicon-m-clock')
            ->color('warning');
    }

    /**
     * Get revenue chart data
     */
    protected function getRevenueChartData($user, $startDate, $endDate): array
    {
        // Get last 7 days of revenue
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayQuery = Booking::whereHas('hall', function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            })
                ->where('payment_status', 'paid')
                ->whereDate('booking_date', $date)
                ->sum('total_amount');

            // ✅ FIXED: Ensure float casting for chart data
            $dayRevenue = (float) ($dayQuery ?? 0);
            $data[] = round($dayRevenue, 2);
        }

        return $data;
    }

    /**
     * Get bookings chart data
     */
    protected function getBookingsChartData($user, $startDate, $endDate): array
    {
        // Get last 7 days of bookings
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayBookings = (int) Booking::whereHas('hall', function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            })
                ->whereDate('booking_date', $date)
                ->count();

            $data[] = $dayBookings;
        }

        return $data;
    }

    /**
     * Get occupancy chart data
     */
    protected function getOccupancyChartData($user, $startDate, $endDate): array
    {
        // Return last 7 days occupancy rates
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $bookings = (int) Booking::whereHas('hall', function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            })
                ->whereDate('booking_date', $date)
                ->count();

            $hallCount = (int) Hall::where('owner_id', $user->id)->where('is_active', true)->count();
            $maxBookings = $hallCount * 4; // 4 slots per day

            $rate = $maxBookings > 0 ? round(($bookings / $maxBookings) * 100, 1) : 0;
            $data[] = $rate;
        }

        return $data;
    }

    /**
     * Add filter form to the widget header
     */
    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('filter')
                ->form([
                    \Filament\Forms\Components\Select::make('dateRange')
                        ->options([
                            'week' => 'This Week',
                            'month' => 'This Month',
                            'year' => 'This Year',
                        ])
                        ->default('month'),
                    \Filament\Forms\Components\Select::make('hallId')
                        ->options(
                            Auth::user()->halls()->pluck('name', 'id')
                            //auth()->user()->halls()->pluck('name', 'id')
                        )
                        ->placeholder('All Halls'),
                ])
                ->action(function (array $data) {
                    $this->dateRange = $data['dateRange'];
                    $this->hallId = $data['hallId'];
                }),
        ];
    }
}
