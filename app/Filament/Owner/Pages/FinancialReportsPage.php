<?php

declare(strict_types=1);

namespace App\Filament\Owner\Pages;

use App\Models\Booking;
use App\Models\Hall;
use App\Models\OwnerPayout;
use App\Enums\PayoutStatus;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

/**
 * FinancialReportsPage for Owner Panel
 *
 * Comprehensive financial reporting page for hall owners.
 * Provides various report types and export options.
 *
 * Features:
 * - Monthly/Yearly revenue reports
 * - Hall performance comparison
 * - Commission breakdown
 * - Payout history
 * - PDF export
 *
 * @package App\Filament\Owner\Pages
 */
class FinancialReportsPage extends Page implements HasForms
{
    use InteractsWithForms;

    /**
     * The view for this page.
     *
     * @var string
     */
    protected static string $view = 'filament.owner.pages.financial-reports';

    /**
     * The navigation icon.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    /**
     * The navigation group.
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = 'Financial';

    /**
     * The navigation sort order.
     *
     * @var int|null
     */
    protected static ?int $navigationSort = 3;

    /**
     * The slug for the page.
     *
     * @var string|null
     */
    protected static ?string $slug = 'financial-reports';

    /**
     * Selected report type.
     *
     * @var string
     */
    public string $reportType = 'monthly';

    /**
     * Selected year.
     *
     * @var int
     */
    public int $selectedYear;

    /**
     * Selected month.
     *
     * @var int
     */
    public int $selectedMonth;

    /**
     * Selected hall (null for all).
     *
     * @var int|null
     */
    public ?int $selectedHall = null;

    /**
     * Report data.
     *
     * @var array
     */
    public array $reportData = [];

    /**
     * Get the page title.
     *
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('owner.reports.navigation');
    }

    /**
     * Get the page title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return __('owner.reports.title');
    }

    /**
     * Get the page heading.
     *
     * @return string
     */
    public function getHeading(): string
    {
        return __('owner.reports.heading');
    }

    /**
     * Mount the page.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->selectedYear = now()->year;
        $this->selectedMonth = now()->month;
        $this->generateReport();
    }

    /**
     * Get the form schema.
     *
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(4)
                    ->schema([
                        Forms\Components\Select::make('reportType')
                            ->label(__('owner.reports.type'))
                            ->options([
                                'monthly' => __('owner.reports.type_monthly'),
                                'yearly' => __('owner.reports.type_yearly'),
                                'hall' => __('owner.reports.type_hall'),
                                'comparison' => __('owner.reports.type_comparison'),
                            ])
                            ->default('monthly')
                            ->live()
                            ->afterStateUpdated(fn() => $this->generateReport()),

                        Forms\Components\Select::make('selectedYear')
                            ->label(__('owner.reports.year'))
                            ->options(function (): array {
                                $years = [];
                                for ($y = now()->year; $y >= now()->year - 5; $y--) {
                                    $years[$y] = $y;
                                }
                                return $years;
                            })
                            ->default(now()->year)
                            ->live()
                            ->afterStateUpdated(fn() => $this->generateReport()),

                        Forms\Components\Select::make('selectedMonth')
                            ->label(__('owner.reports.month'))
                            ->options([
                                1 => __('owner.months.january'),
                                2 => __('owner.months.february'),
                                3 => __('owner.months.march'),
                                4 => __('owner.months.april'),
                                5 => __('owner.months.may'),
                                6 => __('owner.months.june'),
                                7 => __('owner.months.july'),
                                8 => __('owner.months.august'),
                                9 => __('owner.months.september'),
                                10 => __('owner.months.october'),
                                11 => __('owner.months.november'),
                                12 => __('owner.months.december'),
                            ])
                            ->default(now()->month)
                            ->visible(fn($get): bool => in_array($get('reportType'), ['monthly', 'comparison']))
                            ->live()
                            ->afterStateUpdated(fn() => $this->generateReport()),

                        Forms\Components\Select::make('selectedHall')
                            ->label(__('owner.reports.hall'))
                            ->options(function (): array {
                                return Hall::where('owner_id', Auth::id())
                                    ->get()
                                    ->mapWithKeys(fn($hall): array => [
                                        $hall->id => is_array($hall->name)
                                            ? ($hall->name[app()->getLocale()] ?? $hall->name['en'] ?? $hall->name[array_key_first($hall->name)] ?? '')
                                            : $hall->name,
                                    ])
                                    ->toArray();
                            })
                            ->placeholder(__('owner.reports.all_halls'))
                            ->visible(fn($get): bool => $get('reportType') === 'hall')
                            ->live()
                            ->afterStateUpdated(fn() => $this->generateReport()),
                    ]),
            ]);
    }

    /**
     * Get header actions.
     *
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label(__('owner.reports.refresh'))
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(fn() => $this->generateReport()),

            Action::make('exportPdf')
                ->label(__('owner.reports.export_pdf'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(fn() => $this->exportToPdf()),

            Action::make('exportCsv')
                ->label(__('owner.reports.export_csv'))
                ->icon('heroicon-o-table-cells')
                ->color('primary')
                ->action(fn() => $this->exportToCsv()),
        ];
    }

    // /**
    //  * Generate report based on selected type.
    //  *
    //  * @return void
    //  */
    // public function generateReport(): void
    // {
    //     $user = Auth::user();

    //     $this->reportData = match ($this->reportType) {
    //         'monthly' => $this->generateMonthlyReport($user),
    //         'yearly' => $this->generateYearlyReport($user),
    //         'hall' => $this->generateHallReport($user),
    //         'comparison' => $this->generateComparisonReport($user),
    //         default => [],
    //     };
    // }

    /**
     * Generate report based on selected type.
     *
     * @return void
     */
    public function generateReport(): void
    {
        $user = Auth::user();

        $this->reportData = match ($this->reportType) {
            'monthly' => $this->generateMonthlyReport($user),
            'yearly' => $this->generateYearlyReport($user),
            'hall' => $this->generateHallReport($user),
            'comparison' => $this->generateComparisonReport($user),
            default => [],
        };

        // Dispatch event to re-initialize charts after Livewire updates the DOM
        $this->dispatch('report-updated');
    }

    /**
     * Generate monthly report.
     *
     * @param \App\Models\User $user
     * @return array
     */
    protected function generateMonthlyReport($user): array
    {
        $year = $this->selectedYear;
        $month = $this->selectedMonth;

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Get bookings for the month
        $bookings = Booking::whereHas('hall', function ($q) use ($user): void {
            $q->where('owner_id', $user->id);
        })
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->with(['hall', 'extraServices'])
            ->get();

        // Calculate summary
        $summary = [
            'total_bookings' => $bookings->count(),
            'gross_revenue' => (float) $bookings->sum('total_amount'),
            'hall_revenue' => (float) $bookings->sum('hall_price'),
            'services_revenue' => (float) $bookings->sum('services_total'),
            'total_commission' => (float) $bookings->sum('commission_amount'),
            'net_earnings' => (float) $bookings->sum('owner_payout'),
            'avg_per_booking' => $bookings->count() > 0
                ? (float) $bookings->sum('owner_payout') / $bookings->count()
                : 0,
        ];


        // Daily breakdown
        $dailyData = [];
        for ($day = 1; $day <= $endDate->day; $day++) {
            $dayBookings = $bookings->filter(function ($b) use ($day) {
                return $b->booking_date->day === $day;
            });
            $dailyData[$day] = [
                'date' => Carbon::create($year, $month, $day)->format('Y-m-d'),
                'day' => $day,
                'bookings' => $dayBookings->count(),
                'gross' => (float) $dayBookings->sum('total_amount'),
                'hall_revenue' => (float) $dayBookings->sum('hall_price'),
                'services_revenue' => (float) $dayBookings->sum('services_total'),
                'commission' => (float) $dayBookings->sum('commission_amount'),
                'net' => (float) $dayBookings->sum('owner_payout'),
            ];
        }

        // Hall breakdown
        $hallBreakdown = $this->getHallBreakdown($bookings, $user);

        // Slot breakdown
        $slotBreakdown = $bookings->groupBy('time_slot')->map(function ($group, $slot) {
            return [
                'slot' => $slot,
                'bookings' => $group->count(),
                'revenue' => (float) $group->sum('owner_payout'),
            ];
        })->values()->toArray();

        return [
            'type' => 'monthly',
            'year' => $year,
            'month' => $month,
            'period' => Carbon::create($year, $month, 1)->format('F Y'),
            'summary' => $summary,
            'daily_data' => $dailyData,
            'hall_breakdown' => $hallBreakdown,
            'slot_breakdown' => $slotBreakdown,
        ];
    }

    /**
     * Generate yearly report.
     *
     * @param \App\Models\User $user
     * @return array
     */
    protected function generateYearlyReport($user): array
    {
        $year = $this->selectedYear;

        // Monthly data for the year - build as sequential array
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            $monthBookings = Booking::whereHas('hall', function ($q) use ($user): void {
                $q->where('owner_id', $user->id);
            })
                ->whereBetween('booking_date', [$startDate, $endDate])
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('payment_status', 'paid')
                ->get();

            $bookingsCount = $monthBookings->count();
            $grossRevenue = (float) $monthBookings->sum('total_amount');
            $hallRevenue = (float) $monthBookings->sum('hall_price');
            $servicesRevenue = (float) $monthBookings->sum('services_total');
            $commission = (float) $monthBookings->sum('commission_amount');
            $netEarnings = (float) $monthBookings->sum('owner_payout');

            // Build as sequential array for compatibility with array_column()
            $monthlyData[] = [
                'month' => $month,
                'month_name' => Carbon::create($year, $month, 1)->format('F'), // Full month name
                'month_short' => Carbon::create($year, $month, 1)->format('M'), // Short month name for charts
                'month_full' => Carbon::create($year, $month, 1)->format('F Y'),
                // Multiple aliases for compatibility
                'bookings' => $bookingsCount,
                'bookings_count' => $bookingsCount,
                'gross' => $grossRevenue,
                'gross_revenue' => $grossRevenue,
                'hall_revenue' => $hallRevenue,
                'services_revenue' => $servicesRevenue,
                'commission' => $commission,
                'net' => $netEarnings,
                'net_earnings' => $netEarnings,
            ];
        }

        // Year total
        $yearBookings = Booking::whereHas('hall', function ($q) use ($user): void {
            $q->where('owner_id', $user->id);
        })
            ->whereYear('booking_date', $year)
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->with('hall')
            ->get();

        // Find best performing month
        $bestMonthData = collect($monthlyData)->sortByDesc('net_earnings')->first();

        $summary = [
            'total_bookings' => $yearBookings->count(),
            'gross_revenue' => (float) $yearBookings->sum('total_amount'),
            'hall_revenue' => (float) $yearBookings->sum('hall_price'),
            'services_revenue' => (float) $yearBookings->sum('services_total'),
            'total_commission' => (float) $yearBookings->sum('commission_amount'),
            'net_earnings' => (float) $yearBookings->sum('owner_payout'),
            'best_month' => $bestMonthData ? [
                'month' => $bestMonthData['month'],
                'month_name' => $bestMonthData['month_name'] ?? '',
                'net' => $bestMonthData['net'] ?? 0,
                'net_earnings' => $bestMonthData['net_earnings'] ?? $bestMonthData['net'] ?? 0,
            ] : ['month' => 1, 'net_earnings' => 0],
            'avg_monthly' => (float) $yearBookings->sum('owner_payout') / 12,
            'avg_per_booking' => $yearBookings->count() > 0
                ? (float) $yearBookings->sum('owner_payout') / $yearBookings->count()
                : 0,
        ];

        // Hall breakdown
        $hallBreakdown = $this->getHallBreakdown($yearBookings, $user);

        // Payout summary
        $payouts = OwnerPayout::where('owner_id', $user->id)
            ->whereYear('period_start', $year)
            ->get();

        $payoutSummary = [
            'total_received' => (float) $payouts->where('status', PayoutStatus::COMPLETED)->sum('net_payout'),
            'pending' => (float) $payouts->whereIn('status', [PayoutStatus::PENDING, PayoutStatus::PROCESSING])->sum('net_payout'),
            'payout_count' => $payouts->where('status', PayoutStatus::COMPLETED)->count(),
        ];

        return [
            'type' => 'yearly',
            'year' => $year,
            'period' => (string) $year,
            'summary' => $summary,
            'monthly_data' => $monthlyData, // Now a sequential array
            'hall_breakdown' => $hallBreakdown,
            'payout_summary' => $payoutSummary,
        ];
    }

    /**
     * Generate hall performance report.
     *
     * @param \App\Models\User $user
     * @return array
     */
    protected function generateHallReport($user): array
    {
        $year = $this->selectedYear;
        $hallId = $this->selectedHall;

        $query = Booking::whereHas('hall', function ($q) use ($user, $hallId): void {
            $q->where('owner_id', $user->id);
            if ($hallId) {
                $q->where('id', $hallId);
            }
        })
            ->whereYear('booking_date', $year)
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid');

        $bookings = $query->with(['hall', 'extraServices'])->get();

        // Get halls
        $halls = Hall::where('owner_id', $user->id)
            ->when($hallId, fn($q) => $q->where('id', $hallId))
            ->get();

        $totalEarnings = (float) $bookings->sum('owner_payout');

        // Hall performance
        $hallData = $halls->map(function ($hall) use ($bookings, $totalEarnings) {
            $hallBookings = $bookings->where('hall_id', $hall->id);
            $hallName = is_array($hall->name)
                ? ($hall->name[app()->getLocale()] ?? $hall->name['en'] ?? '')
                : $hall->name;

            $netEarnings = (float) $hallBookings->sum('owner_payout');

            // Monthly trend
            $monthlyTrend = [];
            for ($month = 1; $month <= 12; $month++) {
                $monthBookings = $hallBookings->filter(function ($b) use ($month) {
                    return $b->booking_date->month === $month;
                });
                $monthlyTrend[$month] = (float) $monthBookings->sum('owner_payout');
            }

            // Slot popularity
            $slotData = $hallBookings->groupBy('time_slot')->map(fn($group) => $group->count())->toArray();

            return [
                'hall_id' => $hall->id,
                'name' => $hallName,
                'hall_name' => $hallName,
                'bookings_count' => $hallBookings->count(),
                'gross_revenue' => (float) $hallBookings->sum('total_amount'),
                'hall_revenue' => (float) $hallBookings->sum('hall_price'),
                'services_revenue' => (float) $hallBookings->sum('services_total'),
                'commission' => (float) $hallBookings->sum('commission_amount'),
                'net_earnings' => $netEarnings,
                'avg_booking' => $hallBookings->count() > 0
                    ? $netEarnings / $hallBookings->count()
                    : 0,
                'contribution_percentage' => $totalEarnings > 0
                    ? ($netEarnings / $totalEarnings) * 100
                    : 0,
                'monthly_trend' => $monthlyTrend,
                'slot_popularity' => $slotData,
            ];
        })->values()->toArray();

        $summary = [
            'total_halls' => $halls->count(),
            'total_bookings' => $bookings->count(),
            'gross_revenue' => (float) $bookings->sum('total_amount'),
            'hall_revenue' => (float) $bookings->sum('hall_price'),
            'services_revenue' => (float) $bookings->sum('services_total'),
            'total_commission' => (float) $bookings->sum('commission_amount'),
            'net_earnings' => $totalEarnings,
        ];

        return [
            'type' => 'hall',
            'year' => $year,
            'period' => (string) $year,
            'summary' => $summary,
            'hall_data' => $hallData,
            'hall_performance' => $hallData, // Alias for compatibility
        ];
    }

    /**
     * Generate comparison report (month-over-month).
     *
     * @param \App\Models\User $user
     * @return array
     */
    protected function generateComparisonReport($user): array
    {
        $currentMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1);
        $previousMonth = $currentMonth->copy()->subMonth();

        // Current month data
        $currentBookings = Booking::whereHas('hall', function ($q) use ($user): void {
            $q->where('owner_id', $user->id);
        })
            ->whereBetween('booking_date', [
                $currentMonth->copy()->startOfMonth(),
                $currentMonth->copy()->endOfMonth(),
            ])
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->get();

        // Previous month data
        $previousBookings = Booking::whereHas('hall', function ($q) use ($user): void {
            $q->where('owner_id', $user->id);
        })
            ->whereBetween('booking_date', [
                $previousMonth->copy()->startOfMonth(),
                $previousMonth->copy()->endOfMonth(),
            ])
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->get();

        // Current month stats - using keys expected by blade template
        $currentStats = [
            'period' => $currentMonth->format('F Y'),
            'bookings' => $currentBookings->count(),
            'gross' => (float) $currentBookings->sum('total_amount'),
            'commission' => (float) $currentBookings->sum('commission_amount'),
            'net' => (float) $currentBookings->sum('owner_payout'),
            // Additional keys for PDF template compatibility
            'total_bookings' => $currentBookings->count(),
            'gross_revenue' => (float) $currentBookings->sum('total_amount'),
            'hall_revenue' => (float) $currentBookings->sum('hall_price'),
            'services_revenue' => (float) $currentBookings->sum('services_total'),
            'total_commission' => (float) $currentBookings->sum('commission_amount'),
            'net_earnings' => (float) $currentBookings->sum('owner_payout'),
            'avg_per_booking' => $currentBookings->count() > 0
                ? (float) $currentBookings->sum('owner_payout') / $currentBookings->count()
                : 0,
        ];

        // Previous month stats - using keys expected by blade template
        $previousStats = [
            'period' => $previousMonth->format('F Y'),
            'bookings' => $previousBookings->count(),
            'gross' => (float) $previousBookings->sum('total_amount'),
            'commission' => (float) $previousBookings->sum('commission_amount'),
            'net' => (float) $previousBookings->sum('owner_payout'),
            // Additional keys for PDF template compatibility
            'total_bookings' => $previousBookings->count(),
            'gross_revenue' => (float) $previousBookings->sum('total_amount'),
            'hall_revenue' => (float) $previousBookings->sum('hall_price'),
            'services_revenue' => (float) $previousBookings->sum('services_total'),
            'total_commission' => (float) $previousBookings->sum('commission_amount'),
            'net_earnings' => (float) $previousBookings->sum('owner_payout'),
            'avg_per_booking' => $previousBookings->count() > 0
                ? (float) $previousBookings->sum('owner_payout') / $previousBookings->count()
                : 0,
        ];

        // Calculate percentage changes
        $changes = [
            'bookings' => $this->calculatePercentageChange(
                (float) $previousStats['bookings'],
                (float) $currentStats['bookings']
            ),
            'gross' => $this->calculatePercentageChange(
                $previousStats['gross'],
                $currentStats['gross']
            ),
            'gross_revenue' => $this->calculatePercentageChange(
                $previousStats['gross'],
                $currentStats['gross']
            ),
            'hall_revenue' => $this->calculatePercentageChange(
                $previousStats['hall_revenue'],
                $currentStats['hall_revenue']
            ),
            'services_revenue' => $this->calculatePercentageChange(
                $previousStats['services_revenue'],
                $currentStats['services_revenue']
            ),
            'commission' => $this->calculatePercentageChange(
                $previousStats['commission'],
                $currentStats['commission']
            ),
            'net' => $this->calculatePercentageChange(
                $previousStats['net'],
                $currentStats['net']
            ),
            'net_earnings' => $this->calculatePercentageChange(
                $previousStats['net'],
                $currentStats['net']
            ),
            'avg_per_booking' => $this->calculatePercentageChange(
                $previousStats['avg_per_booking'],
                $currentStats['avg_per_booking']
            ),
        ];

        return [
            'type' => 'comparison',
            'current_month' => $currentMonth->month,
            'previous_month' => $previousMonth->month,
            'current' => $currentStats,
            'previous' => $previousStats,
            'current_data' => $currentStats,  // Alias for PDF
            'previous_data' => $previousStats, // Alias for PDF
            'changes' => $changes,
        ];
    }

    /**
     * Calculate percentage change between two values.
     *
     * @param float $previous
     * @param float $current
     * @return float
     */
    protected function calculatePercentageChange(float $previous, float $current): float
    {
        if ($previous > 0) {
            return (($current - $previous) / $previous) * 100;
        }

        return $current > 0 ? 100.0 : 0.0;
    }

    /**
     * Get hall breakdown from bookings.
     *
     * @param \Illuminate\Support\Collection $bookings
     * @param \App\Models\User $user
     * @return array
     */
    protected function getHallBreakdown($bookings, $user): array
    {
        $halls = Hall::where('owner_id', $user->id)->get();

        if ($halls->isEmpty()) {
            return [];
        }

        $totalEarnings = (float) $bookings->sum('owner_payout');
        $totalGross = (float) $bookings->sum('total_amount');

        $result = [];

        foreach ($halls as $hall) {
            $hallBookings = $bookings->where('hall_id', $hall->id);

            // Handle translatable name
            $hallName = $hall->name;
            if (is_array($hallName)) {
                $hallName = $hallName[app()->getLocale()] ?? $hallName['en'] ?? reset($hallName) ?? '';
            }
            $hallName = (string) $hallName;

            $netEarnings = (float) $hallBookings->sum('owner_payout');
            $grossRevenue = (float) $hallBookings->sum('total_amount');
            $hallRevenue = (float) $hallBookings->sum('hall_price');
            $servicesRevenue = (float) $hallBookings->sum('services_total');
            $commission = (float) $hallBookings->sum('commission_amount');
            $bookingsCount = (int) $hallBookings->count();

            $result[] = [
                // IDs
                'hall_id' => $hall->id,

                // Names (multiple aliases for compatibility)
                'name' => $hallName,
                'hall_name' => $hallName,

                // Booking counts (multiple aliases)
                'bookings_count' => $bookingsCount,
                'total_bookings' => $bookingsCount,
                'bookings' => $bookingsCount,

                // Revenue fields
                'gross_revenue' => $grossRevenue,
                'gross' => $grossRevenue,
                'hall_revenue' => $hallRevenue,
                'services_revenue' => $servicesRevenue,
                'commission' => $commission,
                'total_commission' => $commission,
                'net_earnings' => $netEarnings,
                'net' => $netEarnings,
                'revenue' => $netEarnings, // Alias used in some templates

                // Calculated fields
                'avg_booking' => $bookingsCount > 0 ? $netEarnings / $bookingsCount : 0.0,
                'avg_per_booking' => $bookingsCount > 0 ? $netEarnings / $bookingsCount : 0.0,
                'contribution_percentage' => $totalEarnings > 0
                    ? ($netEarnings / $totalEarnings) * 100
                    : 0.0,
                'share' => $totalGross > 0
                    ? ($grossRevenue / $totalGross) * 100
                    : 0.0,
            ];
        }

        return $result;
    }

    /**
     * Export report to PDF.
     *
     * FIX: Now properly extracts and passes all required variables to the view.
     *
     * @return void
     */
    public function exportToPdf(): void
    {
        try {
            $user = Auth::user();

            // Base data for all report types
            $pdfData = [
                'owner' => $user,
                'hallOwner' => $user->hallOwner,
                'reportType' => $this->reportType,
                'reportData' => $this->reportData,
                'generatedAt' => now(),
            ];

            // Extract and add type-specific variables
            switch ($this->reportType) {
                case 'monthly':
                    $pdfData = array_merge($pdfData, [
                        'year' => $this->reportData['year'] ?? $this->selectedYear,
                        'month' => $this->reportData['month'] ?? $this->selectedMonth,
                        'period' => $this->reportData['period'] ?? Carbon::create($this->selectedYear, $this->selectedMonth, 1)->format('F Y'),
                        'summary' => $this->reportData['summary'] ?? [],
                        'dailyData' => $this->reportData['daily_data'] ?? [],
                        'hallBreakdown' => $this->reportData['hall_breakdown'] ?? [],
                        'hallData' => $this->reportData['hall_breakdown'] ?? [], // Alias for PDF template
                        'slotBreakdown' => $this->reportData['slot_breakdown'] ?? [],
                    ]);
                    break;

                case 'yearly':
                    // Calculate year totals from summary
                    $yearTotals = [
                        'total_bookings' => $this->reportData['summary']['total_bookings'] ?? 0,
                        'hall_revenue' => $this->reportData['summary']['hall_revenue'] ?? 0,
                        'services_revenue' => $this->reportData['summary']['services_revenue'] ?? 0,
                        'gross_revenue' => $this->reportData['summary']['gross_revenue'] ?? 0,
                        'total_commission' => $this->reportData['summary']['total_commission'] ?? 0,
                        'net_earnings' => $this->reportData['summary']['net_earnings'] ?? 0,
                    ];

                    // Get best month
                    $bestMonth = $this->reportData['summary']['best_month'] ?? [
                        'month' => 1,
                        'net_earnings' => 0,
                    ];

                    // Average monthly earnings
                    $avgMonthly = $this->reportData['summary']['avg_monthly'] ?? 0;

                    $pdfData = array_merge($pdfData, [
                        'year' => $this->reportData['year'] ?? $this->selectedYear,
                        'period' => $this->reportData['period'] ?? (string) $this->selectedYear,
                        'summary' => $this->reportData['summary'] ?? [],
                        'monthlyData' => $this->reportData['monthly_data'] ?? [],
                        'hallBreakdown' => $this->reportData['hall_breakdown'] ?? [],
                        'hallData' => $this->reportData['hall_breakdown'] ?? [],
                        'payoutSummary' => $this->reportData['payout_summary'] ?? [],
                        'yearTotals' => $yearTotals,
                        'bestMonth' => $bestMonth,
                        'avgMonthly' => $avgMonthly,
                    ]);
                    break;

                case 'hall':
                    $pdfData = array_merge($pdfData, [
                        'year' => $this->reportData['year'] ?? $this->selectedYear,
                        'period' => $this->reportData['period'] ?? (string) $this->selectedYear,
                        'summary' => $this->reportData['summary'] ?? [],
                        'hallData' => $this->reportData['hall_data'] ?? [],
                        'hallPerformance' => $this->reportData['hall_performance'] ?? $this->reportData['hall_data'] ?? [],
                    ]);
                    break;

                case 'comparison':
                    $pdfData = array_merge($pdfData, [
                        'year' => $this->selectedYear,
                        'month' => $this->selectedMonth,
                        'period' => Carbon::create($this->selectedYear, $this->selectedMonth, 1)->format('F Y'),
                        'currentMonth' => $this->reportData['current_month'] ?? $this->selectedMonth,
                        'previousMonth' => $this->reportData['previous_month'] ?? ($this->selectedMonth > 1 ? $this->selectedMonth - 1 : 12),
                        'currentData' => $this->reportData['current_data'] ?? $this->reportData['current'] ?? [],
                        'previousData' => $this->reportData['previous_data'] ?? $this->reportData['previous'] ?? [],
                        'changes' => $this->reportData['changes'] ?? [],
                        'summary' => $this->reportData['current_data'] ?? $this->reportData['current'] ?? [], // Use current as summary
                    ]);
                    break;
            }

            // Generate PDF
            $pdf = Pdf::loadView('pdf.financial-report', $pdfData)->setPaper('a4');

            // Ensure directory exists
            if (!Storage::disk('public')->exists('reports')) {
                Storage::disk('public')->makeDirectory('reports');
            }

            // Save file
            $filename = 'financial-report-' . $this->reportType . '-' . now()->format('Ymd-His') . '.pdf';
            $filepath = 'reports/' . $filename;

            Storage::disk('public')->put($filepath, $pdf->output());

            Notification::make()
                ->success()
                ->title(__('owner.reports.export_success'))
                ->actions([
                    \Filament\Notifications\Actions\Action::make('download')
                        ->label(__('owner.actions.download'))
                        ->url(Storage::disk('public')->url($filepath))
                        ->openUrlInNewTab(),
                ])
                ->persistent()
                ->send();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Financial report export failed', [
                'user_id' => Auth::id(),
                'report_type' => $this->reportType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->danger()
                ->title(__('owner.reports.export_failed'))
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Export report to CSV.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|null
     */
    public function exportToCsv()
    {
        try {
            $data = $this->prepareCsvData();

            if (empty($data)) {
                Notification::make()
                    ->warning()
                    ->title(__('owner.reports.no_data'))
                    ->send();
                return null;
            }

            $filename = 'financial-report-' . $this->reportType . '-' . now()->format('Ymd-His') . '.csv';

            return response()->streamDownload(function () use ($data): void {
                $handle = fopen('php://output', 'w');

                // UTF-8 BOM for Excel
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
            ]);
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('owner.reports.export_failed'))
                ->body($e->getMessage())
                ->send();

            return null;
        }
    }

    /**
     * Prepare data for CSV export.
     *
     * @return array
     */
    protected function prepareCsvData(): array
    {
        $data = [];

        switch ($this->reportType) {
            case 'monthly':
                $summary = $this->reportData['summary'] ?? [];
                $data[] = [
                    'Metric' => 'Total Bookings',
                    'Value' => $summary['total_bookings'] ?? 0,
                ];
                $data[] = [
                    'Metric' => 'Gross Revenue (OMR)',
                    'Value' => number_format($summary['gross_revenue'] ?? 0, 3),
                ];
                $data[] = [
                    'Metric' => 'Total Commission (OMR)',
                    'Value' => number_format($summary['total_commission'] ?? 0, 3),
                ];
                $data[] = [
                    'Metric' => 'Net Earnings (OMR)',
                    'Value' => number_format($summary['net_earnings'] ?? 0, 3),
                ];
                break;

            case 'yearly':
                foreach ($this->reportData['monthly_data'] ?? [] as $monthData) {
                    $data[] = [
                        'Month' => $monthData['month_name'] ?? "Month {$monthData['month']}",
                        'Bookings' => $monthData['bookings'] ?? 0,
                        'Gross Revenue (OMR)' => number_format($monthData['gross'] ?? 0, 3),
                        'Commission (OMR)' => number_format($monthData['commission'] ?? 0, 3),
                        'Net Earnings (OMR)' => number_format($monthData['net'] ?? 0, 3),
                    ];
                }
                break;

            case 'hall':
                foreach ($this->reportData['hall_data'] ?? [] as $hall) {
                    $data[] = [
                        'Hall' => $hall['name'] ?? '',
                        'Bookings' => $hall['bookings_count'] ?? 0,
                        'Gross Revenue (OMR)' => number_format($hall['gross_revenue'] ?? 0, 3),
                        'Commission (OMR)' => number_format($hall['commission'] ?? 0, 3),
                        'Net Earnings (OMR)' => number_format($hall['net_earnings'] ?? 0, 3),
                        'Contribution %' => number_format($hall['contribution_percentage'] ?? 0, 1) . '%',
                    ];
                }
                break;

            case 'comparison':
                $current = $this->reportData['current_data'] ?? $this->reportData['current'] ?? [];
                $previous = $this->reportData['previous_data'] ?? $this->reportData['previous'] ?? [];
                $changes = $this->reportData['changes'] ?? [];

                $metrics = ['total_bookings', 'gross_revenue', 'net_earnings'];
                foreach ($metrics as $metric) {
                    $data[] = [
                        'Metric' => ucwords(str_replace('_', ' ', $metric)),
                        'Previous Month' => is_numeric($previous[$metric] ?? 0)
                            ? (strpos($metric, 'revenue') !== false || strpos($metric, 'earnings') !== false
                                ? number_format($previous[$metric] ?? 0, 3)
                                : ($previous[$metric] ?? 0))
                            : ($previous[$metric] ?? 0),
                        'Current Month' => is_numeric($current[$metric] ?? 0)
                            ? (strpos($metric, 'revenue') !== false || strpos($metric, 'earnings') !== false
                                ? number_format($current[$metric] ?? 0, 3)
                                : ($current[$metric] ?? 0))
                            : ($current[$metric] ?? 0),
                        'Change %' => number_format($changes[str_replace('total_', '', $metric)] ?? $changes[$metric] ?? 0, 1) . '%',
                    ];
                }
                break;
        }

        return $data;
    }
}
