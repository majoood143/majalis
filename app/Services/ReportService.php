<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PayoutStatus;
use App\Models\Booking;
use App\Models\Hall;
use App\Models\HallOwner;
use App\Models\OwnerPayout;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * ReportService - Centralized Report Generation
 *
 * Provides methods for generating various reports for both
 * admin and owner dashboards including:
 * - Revenue reports
 * - Booking analytics
 * - Owner performance
 * - Commission reports
 * - Trend analysis
 *
 * @package App\Services
 */
class ReportService
{
    /**
     * Get admin dashboard summary statistics.
     *
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array<string, mixed>
     */
    public function getAdminDashboardStats(
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): array {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

        // Base query for date range
        $bookingsQuery = Booking::whereBetween('booking_date', [$startDate, $endDate]);
        $paymentsQuery = Payment::whereBetween('created_at', [$startDate, $endDate]);

        return [
            // Revenue Stats
            'total_revenue' => (float) $bookingsQuery->clone()
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            'total_commission' => (float) $bookingsQuery->clone()
                ->where('payment_status', 'paid')
                ->sum('commission_amount'),
            'total_owner_payout' => (float) $bookingsQuery->clone()
                ->where('payment_status', 'paid')
                ->sum('owner_payout'),

            // Booking Stats
            'total_bookings' => $bookingsQuery->clone()->count(),
            'confirmed_bookings' => $bookingsQuery->clone()->where('status', 'confirmed')->count(),
            'completed_bookings' => $bookingsQuery->clone()->where('status', 'completed')->count(),
            'pending_bookings' => $bookingsQuery->clone()->where('status', 'pending')->count(),
            'cancelled_bookings' => $bookingsQuery->clone()->where('status', 'cancelled')->count(),

            // Payment Stats
            'successful_payments' => $paymentsQuery->clone()->where('status', 'completed')->count(),
            'failed_payments' => $paymentsQuery->clone()->where('status', 'failed')->count(),
            'refunded_amount' => (float) $paymentsQuery->clone()
                ->where('status', 'refunded')
                ->sum('refund_amount'),

            // Entity Counts
            'total_halls' => Hall::where('is_active', true)->count(),
            'total_owners' => HallOwner::where('is_verified', true)->count(),
            'total_customers' => User::whereHas('bookings')->count(),

            // Payout Stats
            'pending_payouts' => OwnerPayout::where('status', PayoutStatus::PENDING)->count(),
            'pending_payout_amount' => (float) OwnerPayout::where('status', PayoutStatus::PENDING)
                ->sum('net_payout'),

            // Period Info
            'period_start' => $startDate->format('Y-m-d'),
            'period_end' => $endDate->format('Y-m-d'),
        ];
    }

    /**
     * Get owner dashboard summary statistics.
     *
     * @param int $ownerId
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array<string, mixed>
     */
    public function getOwnerDashboardStats(
        int $ownerId,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): array {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

        // Get owner's hall IDs
        $hallIds = Hall::where('owner_id', $ownerId)->pluck('id');

        // Base query for date range
        $bookingsQuery = Booking::whereIn('hall_id', $hallIds)
            ->whereBetween('booking_date', [$startDate, $endDate]);

        return [
            // Revenue Stats
            'total_revenue' => (float) $bookingsQuery->clone()
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            'total_earnings' => (float) $bookingsQuery->clone()
                ->where('payment_status', 'paid')
                ->sum('owner_payout'),
            'platform_commission' => (float) $bookingsQuery->clone()
                ->where('payment_status', 'paid')
                ->sum('commission_amount'),

            // Booking Stats
            'total_bookings' => $bookingsQuery->clone()->count(),
            'confirmed_bookings' => $bookingsQuery->clone()->where('status', 'confirmed')->count(),
            'completed_bookings' => $bookingsQuery->clone()->where('status', 'completed')->count(),
            'pending_bookings' => $bookingsQuery->clone()->where('status', 'pending')->count(),
            'cancelled_bookings' => $bookingsQuery->clone()->where('status', 'cancelled')->count(),

            // Hall Stats
            'total_halls' => $hallIds->count(),
            'active_halls' => Hall::whereIn('id', $hallIds)->where('is_active', true)->count(),

            // Payout Stats
            'pending_payouts' => (float) OwnerPayout::where('owner_id', $ownerId)
                ->where('status', PayoutStatus::PENDING)
                ->sum('net_payout'),
            'completed_payouts' => (float) OwnerPayout::where('owner_id', $ownerId)
                ->where('status', PayoutStatus::COMPLETED)
                ->sum('net_payout'),

            // Performance
            'average_booking_value' => $bookingsQuery->clone()
                ->where('payment_status', 'paid')
                ->avg('total_amount') ?? 0,
            'total_guests' => (int) $bookingsQuery->clone()
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('number_of_guests'),

            // Period Info
            'period_start' => $startDate->format('Y-m-d'),
            'period_end' => $endDate->format('Y-m-d'),
        ];
    }

    /**
     * Get revenue trend data for charts.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int|null $ownerId Filter by owner
     * @param string $groupBy 'day', 'week', 'month'
     * @return Collection
     */
    public function getRevenueTrend(
        Carbon $startDate,
        Carbon $endDate,
        ?int $ownerId = null,
        string $groupBy = 'day'
    ): Collection {
        $dateFormat = match ($groupBy) {
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $query = Booking::select(
            DB::raw("DATE_FORMAT(booking_date, '{$dateFormat}') as period"),
            DB::raw('SUM(total_amount) as revenue'),
            DB::raw('SUM(commission_amount) as commission'),
            DB::raw('SUM(owner_payout) as payout'),
            DB::raw('COUNT(*) as bookings')
        )
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->groupBy('period')
            ->orderBy('period');

        if ($ownerId) {
            $hallIds = Hall::where('owner_id', $ownerId)->pluck('id');
            $query->whereIn('hall_id', $hallIds);
        }

        return $query->get();
    }

    /**
     * Get booking status distribution.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int|null $ownerId
     * @return Collection
     */
    public function getBookingStatusDistribution(
        Carbon $startDate,
        Carbon $endDate,
        ?int $ownerId = null
    ): Collection {
        $query = Booking::select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->groupBy('status');

        if ($ownerId) {
            $hallIds = Hall::where('owner_id', $ownerId)->pluck('id');
            $query->whereIn('hall_id', $hallIds);
        }

        return $query->get();
    }

    /**
     * Get top performing halls.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int $limit
     * @param int|null $ownerId
     * @return Collection
     */
    public function getTopHalls(
        Carbon $startDate,
        Carbon $endDate,
        int $limit = 10,
        ?int $ownerId = null
    ): Collection {
        $query = Hall::select(
            'halls.id',
            'halls.name',
            'halls.owner_id',
            DB::raw('COUNT(bookings.id) as bookings_count'),
            DB::raw('SUM(bookings.total_amount) as total_revenue'),
            DB::raw('AVG(bookings.total_amount) as avg_booking_value')
        )
            ->leftJoin('bookings', 'halls.id', '=', 'bookings.hall_id')
            ->whereBetween('bookings.booking_date', [$startDate, $endDate])
            ->where('bookings.payment_status', 'paid')
            ->groupBy('halls.id', 'halls.name', 'halls.owner_id')
            ->orderByDesc('total_revenue')
            ->limit($limit);

        if ($ownerId) {
            $query->where('halls.owner_id', $ownerId);
        }

        return $query->get();
    }

    /**
     * Get top performing owners (admin only).
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int $limit
     * @return Collection
     */
    public function getTopOwners(
        Carbon $startDate,
        Carbon $endDate,
        int $limit = 10
    ): Collection {
        return User::select(
            'users.id',
            'users.name',
            'users.email',
            'hall_owners.business_name',
            DB::raw('COUNT(DISTINCT halls.id) as halls_count'),
            DB::raw('COUNT(bookings.id) as bookings_count'),
            DB::raw('SUM(bookings.total_amount) as total_revenue'),
            DB::raw('SUM(bookings.commission_amount) as total_commission'),
            DB::raw('SUM(bookings.owner_payout) as total_payout')
        )
            ->join('hall_owners', 'users.id', '=', 'hall_owners.user_id')
            ->join('halls', 'users.id', '=', 'halls.owner_id')
            ->leftJoin('bookings', function ($join) use ($startDate, $endDate): void {
                $join->on('halls.id', '=', 'bookings.hall_id')
                    ->whereBetween('bookings.booking_date', [$startDate, $endDate])
                    ->where('bookings.payment_status', 'paid');
            })
            ->where('hall_owners.is_verified', true)
            ->groupBy('users.id', 'users.name', 'users.email', 'hall_owners.business_name')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();
    }

    /**
     * Get time slot distribution.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int|null $ownerId
     * @return Collection
     */
    public function getTimeSlotDistribution(
        Carbon $startDate,
        Carbon $endDate,
        ?int $ownerId = null
    ): Collection {
        $query = Booking::select('time_slot', DB::raw('COUNT(*) as count'))
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->whereIn('status', ['confirmed', 'completed'])
            ->groupBy('time_slot');

        if ($ownerId) {
            $hallIds = Hall::where('owner_id', $ownerId)->pluck('id');
            $query->whereIn('hall_id', $hallIds);
        }

        return $query->get();
    }

    /**
     * Get monthly comparison data.
     *
     * @param int|null $ownerId
     * @return array<string, mixed>
     */
    public function getMonthlyComparison(?int $ownerId = null): array
    {
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        $currentStats = $ownerId
            ? $this->getOwnerDashboardStats($ownerId, $currentMonth, now())
            : $this->getAdminDashboardStats($currentMonth, now());

        $lastStats = $ownerId
            ? $this->getOwnerDashboardStats($ownerId, $lastMonth, $lastMonth->copy()->endOfMonth())
            : $this->getAdminDashboardStats($lastMonth, $lastMonth->copy()->endOfMonth());

        // Calculate percentage changes
        $revenueKey = $ownerId ? 'total_earnings' : 'total_revenue';
        $lastRevenue = (float) $lastStats[$revenueKey];
        $currentRevenue = (float) $currentStats[$revenueKey];

        return [
            'current' => $currentStats,
            'previous' => $lastStats,
            'revenue_change' => $lastRevenue > 0
                ? round((($currentRevenue - $lastRevenue) / $lastRevenue) * 100, 1)
                : 0,
            'bookings_change' => $lastStats['total_bookings'] > 0
                ? round((($currentStats['total_bookings'] - $lastStats['total_bookings']) / $lastStats['total_bookings']) * 100, 1)
                : 0,
        ];
    }

    /**
     * Get detailed booking report data.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int|null $ownerId
     * @param int|null $hallId
     * @return Collection
     */
    public function getDetailedBookings(
        Carbon $startDate,
        Carbon $endDate,
        ?int $ownerId = null,
        ?int $hallId = null
    ): Collection {
        $query = Booking::with(['hall', 'user'])
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->orderBy('booking_date', 'desc');

        if ($ownerId) {
            $hallIds = Hall::where('owner_id', $ownerId)->pluck('id');
            $query->whereIn('hall_id', $hallIds);
        }

        if ($hallId) {
            $query->where('hall_id', $hallId);
        }

        return $query->get();
    }

    /**
     * Get commission report for admin.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array<string, mixed>
     */
    public function getCommissionReport(
        Carbon $startDate,
        Carbon $endDate
    ): array {
        $bookings = Booking::whereBetween('booking_date', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->get();

        $byCommissionType = $bookings->groupBy('commission_type')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total_amount' => (float) $group->sum('total_amount'),
                'commission_amount' => (float) $group->sum('commission_amount'),
            ];
        });

        return [
            'total_commission' => (float) $bookings->sum('commission_amount'),
            'total_revenue' => (float) $bookings->sum('total_amount'),
            'commission_rate' => $bookings->sum('total_amount') > 0
                ? round(($bookings->sum('commission_amount') / $bookings->sum('total_amount')) * 100, 2)
                : 0,
            'by_type' => $byCommissionType,
            'bookings_count' => $bookings->count(),
        ];
    }

    /**
     * Generate CSV export data.
     *
     * @param string $reportType
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int|null $ownerId
     * @return array<int, array<string, mixed>>
     */
    public function generateExportData(
        string $reportType,
        Carbon $startDate,
        Carbon $endDate,
        ?int $ownerId = null
    ): array {
        return match ($reportType) {
            'bookings' => $this->exportBookingsData($startDate, $endDate, $ownerId),
            'revenue' => $this->exportRevenueData($startDate, $endDate, $ownerId),
            'halls' => $this->exportHallsData($startDate, $endDate, $ownerId),
            'summary' => $this->exportSummaryData($startDate, $endDate, $ownerId),
            default => [],
        };
    }

    /**
     * Export bookings data.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int|null $ownerId
     * @return array
     */
    protected function exportBookingsData(
        Carbon $startDate,
        Carbon $endDate,
        ?int $ownerId = null
    ): array {
        $bookings = $this->getDetailedBookings($startDate, $endDate, $ownerId);

        return $bookings->map(function ($booking) {
            $hallName = $booking->hall?->name;
            if (is_array($hallName)) {
                $hallName = $hallName[app()->getLocale()] ?? $hallName['en'] ?? '';
            }

            return [
                'booking_number' => $booking->booking_number,
                'hall' => $hallName,
                'customer' => $booking->customer_name,
                'date' => $booking->booking_date->format('Y-m-d'),
                'time_slot' => __('slots.' . $booking->time_slot),
                'guests' => $booking->number_of_guests,
                'total' => number_format((float) $booking->total_amount, 3),
                'commission' => number_format((float) $booking->commission_amount, 3),
                'payout' => number_format((float) $booking->owner_payout, 3),
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
            ];
        })->toArray();
    }

    /**
     * Export revenue data.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int|null $ownerId
     * @return array
     */
    protected function exportRevenueData(
        Carbon $startDate,
        Carbon $endDate,
        ?int $ownerId = null
    ): array {
        $trend = $this->getRevenueTrend($startDate, $endDate, $ownerId, 'day');

        return $trend->map(function ($item) {
            return [
                'date' => $item->period,
                'revenue' => number_format((float) $item->revenue, 3),
                'commission' => number_format((float) $item->commission, 3),
                'payout' => number_format((float) $item->payout, 3),
                'bookings' => $item->bookings,
            ];
        })->toArray();
    }

    /**
     * Export halls performance data.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int|null $ownerId
     * @return array
     */
    protected function exportHallsData(
        Carbon $startDate,
        Carbon $endDate,
        ?int $ownerId = null
    ): array {
        $halls = $this->getTopHalls($startDate, $endDate, 100, $ownerId);

        return $halls->map(function ($hall) {
            $hallName = $hall->name;
            if (is_array($hallName)) {
                $hallName = $hallName[app()->getLocale()] ?? $hallName['en'] ?? '';
            }

            return [
                'hall' => $hallName,
                'bookings' => $hall->bookings_count,
                'revenue' => number_format((float) $hall->total_revenue, 3),
                'avg_booking' => number_format((float) $hall->avg_booking_value, 3),
            ];
        })->toArray();
    }

    /**
     * Export summary data.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int|null $ownerId
     * @return array
     */
    protected function exportSummaryData(
        Carbon $startDate,
        Carbon $endDate,
        ?int $ownerId = null
    ): array {
        $stats = $ownerId
            ? $this->getOwnerDashboardStats($ownerId, $startDate, $endDate)
            : $this->getAdminDashboardStats($startDate, $endDate);

        $rows = [];
        foreach ($stats as $key => $value) {
            if (is_numeric($value)) {
                $rows[] = [
                    'metric' => str_replace('_', ' ', ucfirst($key)),
                    'value' => is_float($value) ? number_format($value, 3) : $value,
                ];
            }
        }

        return $rows;
    }
}
