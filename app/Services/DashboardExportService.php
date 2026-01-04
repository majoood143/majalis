<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Hall;
use App\Models\OwnerPayout;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * DashboardExportService - Handle Dashboard Data Exports
 *
 * Provides methods for exporting dashboard data to various formats
 * including CSV, Excel, and PDF for both admin and owner panels.
 *
 * @package App\Services
 */
class DashboardExportService
{
    /**
     * Report service instance.
     *
     * @var ReportService
     */
    protected ReportService $reportService;

    /**
     * Create a new service instance.
     *
     * @param ReportService $reportService
     */
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Export owner dashboard data to CSV.
     *
     * @param int $ownerId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param string $reportType
     * @return StreamedResponse
     */
    public function exportOwnerDashboardCSV(
        int $ownerId,
        Carbon $startDate,
        Carbon $endDate,
        string $reportType = 'summary'
    ): StreamedResponse {
        $data = $this->reportService->generateExportData(
            $reportType,
            $startDate,
            $endDate,
            $ownerId
        );

        $filename = "dashboard_{$reportType}_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}.csv";

        return response()->streamDownload(function () use ($data): void {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Headers
            if (!empty($data)) {
                fputcsv($handle, array_keys($data[0]));
            }

            // Data rows
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export owner dashboard data to PDF.
     *
     * @param int $ownerId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return \Illuminate\Http\Response
     */
    public function exportOwnerDashboardPDF(
        int $ownerId,
        Carbon $startDate,
        Carbon $endDate
    ) {
        $user = Auth::user();
        $hallOwner = $user->hallOwner ?? null;

        $stats = $this->reportService->getOwnerDashboardStats($ownerId, $startDate, $endDate);
        $hallPerformance = $this->reportService->getTopHalls($startDate, $endDate, 100, $ownerId);
        $comparison = $this->reportService->getMonthlyComparison($ownerId);

        $pdf = Pdf::loadView('pdf.reports.owner-dashboard', [
            'stats' => $stats,
            'hallPerformance' => $hallPerformance,
            'comparison' => $comparison,
            'owner' => $user,
            'hallOwner' => $hallOwner,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'generatedAt' => now()->format('d M Y H:i'),
        ]);

        $filename = "owner_dashboard_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}.pdf";

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Export admin dashboard data to CSV.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param string $reportType
     * @return StreamedResponse
     */
    public function exportAdminDashboardCSV(
        Carbon $startDate,
        Carbon $endDate,
        string $reportType = 'summary'
    ): StreamedResponse {
        $data = $this->reportService->generateExportData(
            $reportType,
            $startDate,
            $endDate
        );

        $filename = "admin_dashboard_{$reportType}_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}.csv";

        return response()->streamDownload(function () use ($data): void {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Headers
            if (!empty($data)) {
                fputcsv($handle, array_keys($data[0]));
            }

            // Data rows
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export admin dashboard data to PDF.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return \Illuminate\Http\Response
     */
    public function exportAdminDashboardPDF(Carbon $startDate, Carbon $endDate)
    {
        $stats = $this->reportService->getAdminDashboardStats($startDate, $endDate);
        $topHalls = $this->reportService->getTopHalls($startDate, $endDate, 10);
        $topOwners = $this->reportService->getTopOwners($startDate, $endDate, 10);
        $commissionReport = $this->reportService->getCommissionReport($startDate, $endDate);

        $pdf = Pdf::loadView('pdf.reports.admin-dashboard', [
            'stats' => $stats,
            'topHalls' => $topHalls,
            'topOwners' => $topOwners,
            'commissionReport' => $commissionReport,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'generatedAt' => now()->format('d M Y H:i'),
            'generatedBy' => Auth::user()->name ?? 'System',
        ]);

        $filename = "admin_dashboard_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}.pdf";

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Get quick stats for dashboard widgets.
     *
     * @param int|null $ownerId
     * @return array
     */
    public function getQuickStats(?int $ownerId = null): array
    {
        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();
        $thisMonth = now()->startOfMonth();

        if ($ownerId) {
            $hallIds = Hall::where('owner_id', $ownerId)->pluck('id');

            return [
                'today' => [
                    'bookings' => Booking::whereIn('hall_id', $hallIds)
                        ->whereDate('created_at', $today)
                        ->count(),
                    'revenue' => (float) Booking::whereIn('hall_id', $hallIds)
                        ->whereDate('created_at', $today)
                        ->where('payment_status', 'paid')
                        ->sum('owner_payout'),
                ],
                'this_week' => [
                    'bookings' => Booking::whereIn('hall_id', $hallIds)
                        ->where('created_at', '>=', $thisWeek)
                        ->count(),
                    'revenue' => (float) Booking::whereIn('hall_id', $hallIds)
                        ->where('created_at', '>=', $thisWeek)
                        ->where('payment_status', 'paid')
                        ->sum('owner_payout'),
                ],
                'this_month' => [
                    'bookings' => Booking::whereIn('hall_id', $hallIds)
                        ->where('created_at', '>=', $thisMonth)
                        ->count(),
                    'revenue' => (float) Booking::whereIn('hall_id', $hallIds)
                        ->where('created_at', '>=', $thisMonth)
                        ->where('payment_status', 'paid')
                        ->sum('owner_payout'),
                ],
                'pending_payout' => (float) OwnerPayout::where('owner_id', $ownerId)
                    ->where('status', 'pending')
                    ->sum('net_payout'),
            ];
        }

        // Admin stats
        return [
            'today' => [
                'bookings' => Booking::whereDate('created_at', $today)->count(),
                'revenue' => (float) Booking::whereDate('created_at', $today)
                    ->where('payment_status', 'paid')
                    ->sum('total_amount'),
                'commission' => (float) Booking::whereDate('created_at', $today)
                    ->where('payment_status', 'paid')
                    ->sum('commission_amount'),
            ],
            'this_week' => [
                'bookings' => Booking::where('created_at', '>=', $thisWeek)->count(),
                'revenue' => (float) Booking::where('created_at', '>=', $thisWeek)
                    ->where('payment_status', 'paid')
                    ->sum('total_amount'),
                'commission' => (float) Booking::where('created_at', '>=', $thisWeek)
                    ->where('payment_status', 'paid')
                    ->sum('commission_amount'),
            ],
            'this_month' => [
                'bookings' => Booking::where('created_at', '>=', $thisMonth)->count(),
                'revenue' => (float) Booking::where('created_at', '>=', $thisMonth)
                    ->where('payment_status', 'paid')
                    ->sum('total_amount'),
                'commission' => (float) Booking::where('created_at', '>=', $thisMonth)
                    ->where('payment_status', 'paid')
                    ->sum('commission_amount'),
            ],
            'pending_payouts' => (float) OwnerPayout::where('status', 'pending')
                ->sum('net_payout'),
        ];
    }

    /**
     * Generate chart data for dashboard.
     *
     * @param int|null $ownerId
     * @param int $days
     * @return array
     */
    public function getChartData(?int $ownerId = null, int $days = 30): array
    {
        $startDate = now()->subDays($days);
        $endDate = now();

        $revenueTrend = $this->reportService->getRevenueTrend(
            $startDate,
            $endDate,
            $ownerId,
            $days > 60 ? 'week' : 'day'
        );

        $bookingDistribution = $this->reportService->getBookingStatusDistribution(
            $startDate,
            $endDate,
            $ownerId
        );

        $timeSlots = $this->reportService->getTimeSlotDistribution(
            $startDate,
            $endDate,
            $ownerId
        );

        return [
            'revenue_trend' => [
                'labels' => $revenueTrend->pluck('period')->toArray(),
                'revenue' => $revenueTrend->pluck('revenue')->map(fn ($v) => (float) $v)->toArray(),
                'commission' => $revenueTrend->pluck('commission')->map(fn ($v) => (float) $v)->toArray(),
                'payout' => $revenueTrend->pluck('payout')->map(fn ($v) => (float) $v)->toArray(),
                'bookings' => $revenueTrend->pluck('bookings')->toArray(),
            ],
            'booking_distribution' => [
                'labels' => $bookingDistribution->pluck('status')->map(fn ($s) => ucfirst($s))->toArray(),
                'data' => $bookingDistribution->pluck('count')->toArray(),
            ],
            'time_slots' => [
                'labels' => $timeSlots->pluck('time_slot')->map(fn ($s) => __('slots.' . $s))->toArray(),
                'data' => $timeSlots->pluck('count')->toArray(),
            ],
        ];
    }
}
