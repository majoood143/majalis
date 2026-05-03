<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;
use Throwable;
use Illuminate\Http\Response;
use App\Models\Booking;
use App\Models\Hall;
use App\Models\OwnerPayout;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
     * FIX: Previously relied solely on Auth::user() to resolve the owner,
     * which can return null when called from Filament/Livewire action contexts
     * where the auth guard (Filament::auth()) differs from Laravel's default
     * Auth facade. This caused "Undefined variable $owner" in the Blade view.
     *
     * Now uses User::find($ownerId) as the PRIMARY source (since $ownerId
     * is already passed as a parameter), with Filament auth and Auth::user()
     * as fallbacks. This makes the method self-contained and session-independent.
     *
     * @param int $ownerId The owner's user ID
     * @param Carbon $startDate Report start date
     * @param Carbon $endDate Report end date
     * @return Response
     *
     * @throws RuntimeException If owner user cannot be resolved
     */
    public function exportOwnerDashboardPDF(
        int $ownerId,
        Carbon $startDate,
        Carbon $endDate
    ) {
        /**
         * FIX: Resolve the owner user reliably using the $ownerId parameter.
         *
         * Previous code:
         *     $user = Auth::user();
         *     $hallOwner = $user->hallOwner ?? null;
         *
         * Auth::user() can return null in Filament action contexts because:
         * 1. Filament panels may use a different auth guard than Laravel's default
         * 2. streamDownload responses in Livewire can lose session context
         * 3. OwnerPanelProvider doesn't explicitly set ->authGuard('web')
         *
         * Using User::find($ownerId) is deterministic since we already have the ID.
         */
        $user = User::find($ownerId)
            ?? $this->resolveAuthUser();

        // Guard: ensure we have a valid user before generating the PDF
        if (!$user) {
            Log::error('Owner PDF export failed: Could not resolve owner user', [
                'owner_id' => $ownerId,
                'auth_id' => Auth::id(),
            ]);

            throw new RuntimeException(
                __('Could not resolve owner. Please try again.')
            );
        }

        // FIX: Null-safe access to hallOwner relationship
        $hallOwner = $user->hallOwner ?? null;

        // Generate report data using the explicit $ownerId
        $stats = $this->reportService->getOwnerDashboardStats($ownerId, $startDate, $endDate);
        $hallPerformance = $this->reportService->getTopHalls($startDate, $endDate, 100, $ownerId);
        $comparison = $this->reportService->getMonthlyComparison($ownerId);

        // Render PDF with all required view variables
        $pdf = Pdf::loadView('pdf.reports.owner-dashboard', [
            'stats' => $stats,
            'hallPerformance' => $hallPerformance,
            'comparison' => $comparison,
            'owner' => $user,           // FIX: Now guaranteed non-null
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
     * @return Response
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
            ];
        }

        return [
            'today' => [
                'bookings' => Booking::whereDate('created_at', $today)->count(),
                'revenue' => (float) Booking::whereDate('created_at', $today)
                    ->where('payment_status', 'paid')
                    ->sum('total_amount'),
            ],
            'this_week' => [
                'bookings' => Booking::where('created_at', '>=', $thisWeek)->count(),
                'revenue' => (float) Booking::where('created_at', '>=', $thisWeek)
                    ->where('payment_status', 'paid')
                    ->sum('total_amount'),
            ],
            'this_month' => [
                'bookings' => Booking::where('created_at', '>=', $thisMonth)->count(),
                'revenue' => (float) Booking::where('created_at', '>=', $thisMonth)
                    ->where('payment_status', 'paid')
                    ->sum('total_amount'),
            ],
        ];
    }

    /**
     * Resolve the authenticated user from multiple auth sources.
     *
     * Tries Filament's auth first (more reliable in panel context),
     * then falls back to Laravel's default Auth facade.
     *
     * @return User|null
     */
    protected function resolveAuthUser(): ?User
    {
        // Try Filament's auth (reliable within panel context)
        try {
            /** @var User|null $filamentUser */
            $filamentUser = filament()->auth()->user();
            if ($filamentUser instanceof User) {
                return $filamentUser;
            }
        } catch (Throwable $e) {
            // Filament not available in this context, continue to fallback
        }

        // Fall back to Laravel's default Auth facade
        $authUser = Auth::user();
        if ($authUser instanceof User) {
            return $authUser;
        }

        return null;
    }
}
