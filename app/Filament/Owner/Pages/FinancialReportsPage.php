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
                            ->afterStateUpdated(fn () => $this->generateReport()),

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
                            ->afterStateUpdated(fn () => $this->generateReport()),

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
                            ->visible(fn ($get): bool => in_array($get('reportType'), ['monthly', 'comparison']))
                            ->live()
                            ->afterStateUpdated(fn () => $this->generateReport()),

                        Forms\Components\Select::make('selectedHall')
                            ->label(__('owner.reports.hall'))
                            ->options(function (): array {
                                return Hall::where('owner_id', Auth::id())
                                    ->get()
                                    ->mapWithKeys(fn ($hall): array => [
                                        $hall->id => $hall->name[app()->getLocale()] ?? $hall->name['en'],
                                    ])
                                    ->toArray();
                            })
                            ->placeholder(__('owner.reports.all_halls'))
                            ->visible(fn ($get): bool => $get('reportType') === 'hall')
                            ->live()
                            ->afterStateUpdated(fn () => $this->generateReport()),
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
            Action::make('exportPdf')
                ->label(__('owner.reports.export_pdf'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(fn () => $this->exportToPdf()),

            Action::make('refresh')
                ->label(__('owner.reports.refresh'))
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(fn () => $this->generateReport()),
        ];
    }

    /**
     * Generate report based on selected options.
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
    }

    /**
     * Generate monthly report.
     *
     * @param \App\Models\User $user
     * @return array
     */
    protected function generateMonthlyReport($user): array
    {
        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
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

        // Daily breakdown
        $dailyData = [];
        for ($day = 1; $day <= $endDate->day; $day++) {
            $date = Carbon::create($this->selectedYear, $this->selectedMonth, $day);
            $dayBookings = $bookings->where('booking_date', $date->format('Y-m-d'));

            $dailyData[$day] = [
                'date' => $date->format('M d'),
                'bookings' => $dayBookings->count(),
                'gross' => (float) $dayBookings->sum('total_amount'),
                'commission' => (float) $dayBookings->sum('commission_amount'),
                'net' => (float) $dayBookings->sum('owner_payout'),
            ];
        }

        // Summary stats
        $summary = [
            'total_bookings' => $bookings->count(),
            'gross_revenue' => (float) $bookings->sum('total_amount'),
            'hall_revenue' => (float) $bookings->sum('hall_price'),
            'services_revenue' => (float) $bookings->sum('services_price'),
            'total_commission' => (float) $bookings->sum('commission_amount'),
            'net_earnings' => (float) $bookings->sum('owner_payout'),
            'avg_per_booking' => $bookings->count() > 0
                ? (float) $bookings->sum('owner_payout') / $bookings->count()
                : 0,
        ];

        // Time slot breakdown
        $slotBreakdown = $bookings->groupBy('time_slot')->map(function ($group) {
            return [
                'count' => $group->count(),
                'revenue' => (float) $group->sum('owner_payout'),
            ];
        })->toArray();

        // Hall breakdown
        $hallBreakdown = $bookings->groupBy('hall_id')->map(function ($group) {
            $hall = $group->first()->hall;
            $hallName = is_array($hall->name)
                ? ($hall->name[app()->getLocale()] ?? $hall->name['en'])
                : $hall->name;

            return [
                'hall_name' => $hallName,
                'bookings' => $group->count(),
                'revenue' => (float) $group->sum('owner_payout'),
            ];
        })->values()->toArray();

        return [
            'type' => 'monthly',
            'period' => $startDate->format('F Y'),
            'summary' => $summary,
            'daily_data' => $dailyData,
            'slot_breakdown' => $slotBreakdown,
            'hall_breakdown' => $hallBreakdown,
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

        // Monthly breakdown
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

            $monthlyData[$month] = [
                'month' => $startDate->format('M'),
                'month_full' => $startDate->format('F'),
                'bookings' => $monthBookings->count(),
                'gross' => (float) $monthBookings->sum('total_amount'),
                'commission' => (float) $monthBookings->sum('commission_amount'),
                'net' => (float) $monthBookings->sum('owner_payout'),
            ];
        }

        // Year totals
        $yearBookings = Booking::whereHas('hall', function ($q) use ($user): void {
            $q->where('owner_id', $user->id);
        })
            ->whereYear('booking_date', $year)
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->get();

        $summary = [
            'total_bookings' => $yearBookings->count(),
            'gross_revenue' => (float) $yearBookings->sum('total_amount'),
            'total_commission' => (float) $yearBookings->sum('commission_amount'),
            'net_earnings' => (float) $yearBookings->sum('owner_payout'),
            'best_month' => collect($monthlyData)->sortByDesc('net')->first(),
            'avg_monthly' => (float) $yearBookings->sum('owner_payout') / 12,
        ];

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
            'period' => (string) $year,
            'summary' => $summary,
            'monthly_data' => $monthlyData,
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
            ->when($hallId, fn ($q) => $q->where('id', $hallId))
            ->get();

        // Hall performance
        $hallPerformance = $halls->map(function ($hall) use ($bookings) {
            $hallBookings = $bookings->where('hall_id', $hall->id);
            $hallName = is_array($hall->name)
                ? ($hall->name[app()->getLocale()] ?? $hall->name['en'])
                : $hall->name;

            // Monthly trend
            $monthlyTrend = [];
            for ($month = 1; $month <= 12; $month++) {
                $monthBookings = $hallBookings->filter(function ($b) use ($month) {
                    return $b->booking_date->month === $month;
                });
                $monthlyTrend[$month] = (float) $monthBookings->sum('owner_payout');
            }

            // Slot popularity
            $slotData = $hallBookings->groupBy('time_slot')->map(fn ($group) => $group->count())->toArray();

            return [
                'hall_id' => $hall->id,
                'hall_name' => $hallName,
                'bookings_count' => $hallBookings->count(),
                'gross_revenue' => (float) $hallBookings->sum('total_amount'),
                'commission' => (float) $hallBookings->sum('commission_amount'),
                'net_earnings' => (float) $hallBookings->sum('owner_payout'),
                'avg_booking' => $hallBookings->count() > 0
                    ? (float) $hallBookings->sum('owner_payout') / $hallBookings->count()
                    : 0,
                'monthly_trend' => $monthlyTrend,
                'slot_popularity' => $slotData,
            ];
        })->values()->toArray();

        $summary = [
            'total_halls' => $halls->count(),
            'total_bookings' => $bookings->count(),
            'gross_revenue' => (float) $bookings->sum('total_amount'),
            'net_earnings' => (float) $bookings->sum('owner_payout'),
        ];

        return [
            'type' => 'hall',
            'period' => (string) $year,
            'summary' => $summary,
            'hall_performance' => $hallPerformance,
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
                $currentMonth->startOfMonth(),
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
                $previousMonth->startOfMonth(),
                $previousMonth->copy()->endOfMonth(),
            ])
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->get();

        $currentStats = [
            'period' => $currentMonth->format('F Y'),
            'bookings' => $currentBookings->count(),
            'gross' => (float) $currentBookings->sum('total_amount'),
            'commission' => (float) $currentBookings->sum('commission_amount'),
            'net' => (float) $currentBookings->sum('owner_payout'),
        ];

        $previousStats = [
            'period' => $previousMonth->format('F Y'),
            'bookings' => $previousBookings->count(),
            'gross' => (float) $previousBookings->sum('total_amount'),
            'commission' => (float) $previousBookings->sum('commission_amount'),
            'net' => (float) $previousBookings->sum('owner_payout'),
        ];

        // Calculate changes
        $changes = [
            'bookings' => $previousStats['bookings'] > 0
                ? (($currentStats['bookings'] - $previousStats['bookings']) / $previousStats['bookings']) * 100
                : ($currentStats['bookings'] > 0 ? 100 : 0),
            'gross' => $previousStats['gross'] > 0
                ? (($currentStats['gross'] - $previousStats['gross']) / $previousStats['gross']) * 100
                : ($currentStats['gross'] > 0 ? 100 : 0),
            'net' => $previousStats['net'] > 0
                ? (($currentStats['net'] - $previousStats['net']) / $previousStats['net']) * 100
                : ($currentStats['net'] > 0 ? 100 : 0),
        ];

        return [
            'type' => 'comparison',
            'current' => $currentStats,
            'previous' => $previousStats,
            'changes' => $changes,
        ];
    }

    /**
     * Export report to PDF.
     *
     * @return void
     */
    public function exportToPdf(): void
    {
        try {
            $user = Auth::user();

            $pdf = Pdf::loadView('pdf.financial-report', [
                'owner' => $user,
                'hallOwner' => $user->hallOwner,
                'reportType' => $this->reportType,
                'reportData' => $this->reportData,
                'generatedAt' => now(),
            ])->setPaper('a4');

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
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->danger()
                ->title(__('owner.reports.export_failed'))
                ->body($e->getMessage())
                ->send();
        }
    }
}
