<?php

declare(strict_types=1);

namespace App\Filament\Owner\Pages;

use App\Models\Booking;
use App\Models\Hall;
use App\Services\ReportService;
use App\Services\PdfExportService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use App\Models\User;

/**
 * Owner Reports Page
 *
 * Comprehensive reporting dashboard for hall owners.
 * Includes earnings reports, booking analytics, hall performance,
 * and export capabilities (CSV + PDF via mPDF).
 *
 * FIX CHANGELOG:
 * ──────────────
 * 1. REMOVED: `use Barryvdh\DomPDF\Facade\Pdf` — project uses mPDF now
 * 2. REMOVED: `use Mpdf\Mpdf` — handled internally by PdfExportService
 * 3. REMOVED: Duplicate 'exportPdf' header action (was still using DomPDF)
 * 4. FIXED: hallPerformance() query — added 'bookings_count' and
 *    'avg_booking_value' aliases to match what the Blade template expects
 * 5. ADDED: Font cache clearing on first PDF export to ensure config changes
 *    take effect (stale cache was the #1 cause of font rendering issues)
 *
 * @package App\Filament\Owner\Pages
 *
 * @property-read array $dashboardStats
 * @property-read array $revenueTrend
 * @property-read array $bookingDistribution
 * @property-read Collection $hallPerformance
 * @property-read array $timeSlotDistribution
 * @property-read array $monthlyComparison
 */
class Reports extends Page implements HasForms
{
    use InteractsWithForms;

    /** @var string */
    protected static string $view = 'filament.owner.pages.reports';

    /** @var string|null */
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    /** @var string|null */
    protected static ?string $navigationGroup = 'Analytics';

    /** @var int|null */
    protected static ?int $navigationSort = 10;

    /** @var string|null */
    protected static ?string $slug = 'reports';

    /** Report date range start. */
    public ?string $startDate = null;

    /** Report date range end. */
    public ?string $endDate = null;

    /** Selected hall filter — used in all computed properties. */
    public ?int $hallId = null;

    /** Active report tab. */
    public string $activeTab = 'overview';

    /**
     * Mount the page.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function getTitle(): string
    {
        return __('owner_report.reports.title');
    }

    public function getHeading(): string
    {
        return __('owner_report.reports.heading');
    }

    public function getSubheading(): ?string
    {
        return __('owner_report.reports.subheading');
    }

    public static function getNavigationLabel(): string
    {
        return __('owner_report.reports.nav_label');
    }

    /**
     * Define the filter form.
     *
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(5)
                    ->schema([
                        Forms\Components\DatePicker::make('startDate')
                            ->label(__('owner_report.reports.filters.start_date'))
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->maxDate(now())
                            ->live()
                            ->afterStateUpdated(fn() => $this->loadData()),

                        Forms\Components\DatePicker::make('endDate')
                            ->label(__('owner_report.reports.filters.end_date'))
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->maxDate(now())
                            ->live()
                            ->afterStateUpdated(fn() => $this->loadData()),

                        Forms\Components\Select::make('hallId')
                            ->label(__('owner_report.reports.filters.hall'))
                            ->options(fn() => $this->getOwnerHalls())
                            ->placeholder(__('owner_report.reports.filters.all_halls'))
                            ->live()
                            ->afterStateUpdated(fn() => $this->loadData()),

                        Forms\Components\Select::make('preset')
                            ->label(__('owner_report.reports.filters.preset'))
                            ->options([
                                'today'        => __('owner_report.reports.presets.today'),
                                'yesterday'    => __('owner_report.reports.presets.yesterday'),
                                'this_week'    => __('owner_report.reports.presets.this_week'),
                                'last_week'    => __('owner_report.reports.presets.last_week'),
                                'this_month'   => __('owner_report.reports.presets.this_month'),
                                'last_month'   => __('owner_report.reports.presets.last_month'),
                                'this_quarter' => __('owner_report.reports.presets.this_quarter'),
                                'this_year'    => __('owner_report.reports.presets.this_year'),
                            ])
                            ->placeholder(__('owner_report.reports.filters.custom'))
                            ->live()
                            ->afterStateUpdated(fn($state) => $this->applyPreset($state)),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('refresh')
                                ->label(__('owner_report.reports.actions.refresh'))
                                ->icon('heroicon-o-arrow-path')
                                ->action(fn() => $this->loadData()),
                        ])->verticallyAlignEnd(),
                    ]),
            ]);
    }

    /**
     * Get owner's halls for filter dropdown.
     *
     * @return array<int, string>
     */
    protected function getOwnerHalls(): array
    {
        return Hall::where('owner_id', Auth::id())
            ->pluck('name', 'id')
            ->map(function ($name) {
                return is_array($name)
                    ? ($name[app()->getLocale()] ?? $name['en'] ?? '')
                    : $name;
            })
            ->toArray();
    }

    /**
     * Get the hall IDs to filter by.
     *
     * @return Collection<int, int>
     */
    protected function getFilteredHallIds(): Collection
    {
        if ($this->hallId) {
            $hallBelongsToOwner = Hall::where('id', $this->hallId)
                ->where('owner_id', Auth::id())
                ->exists();

            if ($hallBelongsToOwner) {
                return collect([$this->hallId]);
            }
        }

        return Hall::where('owner_id', Auth::id())->pluck('id');
    }

    /**
     * Apply date preset.
     *
     * @param string|null $preset
     * @return void
     */
    public function applyPreset(?string $preset): void
    {
        if (!$preset) {
            return;
        }

        [$start, $end] = match ($preset) {
            'today'        => [now()->startOfDay(), now()],
            'yesterday'    => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'this_week'    => [now()->startOfWeek(), now()],
            'last_week'    => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            'this_month'   => [now()->startOfMonth(), now()],
            'last_month'   => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'this_quarter' => [now()->startOfQuarter(), now()],
            'this_year'    => [now()->startOfYear(), now()],
            default        => [now()->startOfMonth(), now()],
        };

        $this->startDate = $start->format('Y-m-d');
        $this->endDate = $end->format('Y-m-d');
        $this->loadData();
    }

    /**
     * Load/refresh report data.
     *
     * @return void
     */
    public function loadData(): void
    {
        unset(
            $this->dashboardStats,
            $this->revenueTrend,
            $this->bookingDistribution,
            $this->hallPerformance,
            $this->timeSlotDistribution,
            $this->monthlyComparison
        );

        $this->dispatch('chartsDataUpdated');
    }

    /**
     * Set the active tab.
     *
     * @param string $tab
     * @return void
     */
    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->dispatch('activeTabUpdated', tab: $tab);
    }

    /**
     * Get all chart data for JavaScript consumption.
     *
     * @return array
     */
    public function getChartData(): array
    {
        return [
            'revenueTrend'         => $this->revenueTrend,
            'bookingDistribution'  => $this->bookingDistribution,
            'timeSlotDistribution' => $this->timeSlotDistribution,
        ];
    }

    // ======================================================================
    // COMPUTED PROPERTIES
    // ======================================================================

    /**
     * Dashboard statistics — scoped to selected hall + date range.
     *
     * @return array<string, mixed>
     */
    #[Computed]
    public function dashboardStats(): array
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);
        $hallIds = $this->getFilteredHallIds();

        $bookingsQuery = Booking::whereIn('hall_id', $hallIds)
            ->whereBetween('booking_date', [$startDate, $endDate]);

        return [
            'total_revenue'       => (float) $bookingsQuery->clone()->where('payment_status', 'paid')->sum('total_amount'),
            'total_earnings'      => (float) $bookingsQuery->clone()->where('payment_status', 'paid')->sum('owner_payout'),
            'platform_commission' => (float) $bookingsQuery->clone()->where('payment_status', 'paid')->sum('commission_amount'),

            'total_bookings'     => $bookingsQuery->clone()->count(),
            'confirmed_bookings' => $bookingsQuery->clone()->where('status', 'confirmed')->count(),
            'completed_bookings' => $bookingsQuery->clone()->where('status', 'completed')->count(),
            'pending_bookings'   => $bookingsQuery->clone()->where('status', 'pending')->count(),
            'cancelled_bookings' => $bookingsQuery->clone()->where('status', 'cancelled')->count(),

            'total_halls'  => $this->hallId ? 1 : Hall::where('owner_id', Auth::id())->count(),
            'active_halls' => $this->hallId
                ? (int) Hall::where('id', $this->hallId)->where('is_active', true)->exists()
                : Hall::where('owner_id', Auth::id())->where('is_active', true)->count(),

            'pending_payouts'   => (float) \App\Models\OwnerPayout::where('owner_id', Auth::id())
                ->where('status', \App\Enums\PayoutStatus::PENDING)->sum('net_payout'),
            'completed_payouts' => (float) \App\Models\OwnerPayout::where('owner_id', Auth::id())
                ->where('status', \App\Enums\PayoutStatus::COMPLETED)->sum('net_payout'),

            'average_booking_value' => (float) ($bookingsQuery->clone()->where('payment_status', 'paid')->avg('total_amount') ?? 0),
            'total_guests'          => (int) $bookingsQuery->clone()->whereIn('status', ['confirmed', 'completed'])->sum('number_of_guests'),

            'period_start' => $startDate->format('Y-m-d'),
            'period_end'   => $endDate->format('Y-m-d'),
        ];
    }

    /**
     * Revenue trend data for Chart.js line chart.
     *
     * @return array{labels: array, revenue: array, payout: array, bookings: array}
     */
    #[Computed]
    public function revenueTrend(): array
    {
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $hallIds = $this->getFilteredHallIds();

        $days = $start->diffInDays($end);
        $dateFormat = match (true) {
            $days <= 31  => '%Y-%m-%d',
            $days <= 90  => '%Y-%u',
            default      => '%Y-%m',
        };

        $data = Booking::select(
            DB::raw("DATE_FORMAT(booking_date, '{$dateFormat}') as period"),
            DB::raw('SUM(total_amount) as revenue'),
            DB::raw('SUM(commission_amount) as commission'),
            DB::raw('SUM(owner_payout) as payout'),
            DB::raw('COUNT(*) as bookings')
        )
            ->whereIn('hall_id', $hallIds)
            ->whereBetween('booking_date', [$start, $end])
            ->where('payment_status', 'paid')
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return [
            'labels'   => $data->pluck('period')->toArray(),
            'revenue'  => $data->pluck('revenue')->map(fn($v) => (float) $v)->toArray(),
            'payout'   => $data->pluck('payout')->map(fn($v) => (float) $v)->toArray(),
            'bookings' => $data->pluck('bookings')->toArray(),
        ];
    }

    /**
     * Booking status distribution for doughnut chart.
     *
     * @return array{labels: array, data: array}
     */
    #[Computed]
    public function bookingDistribution(): array
    {
        $hallIds = $this->getFilteredHallIds();

        $data = Booking::select('status', DB::raw('COUNT(*) as count'))
            ->whereIn('hall_id', $hallIds)
            ->whereBetween('booking_date', [Carbon::parse($this->startDate), Carbon::parse($this->endDate)])
            ->groupBy('status')
            ->get();

        return [
            'labels' => $data->pluck('status')->map(fn($s) => __('owner_report.reports.status.' . $s))->toArray(),
            'data'   => $data->pluck('count')->toArray(),
        ];
    }

    /**
     * Hall performance data for the table.
     *
     * FIX: Added 'bookings_count' alias (was 'total_bookings')
     *      Added 'avg_booking_value' alias (was missing entirely)
     *      The Blade template references these exact column names.
     *
     * @return Collection
     */
    #[Computed]
    public function hallPerformance(): Collection
    {
        $hallIds = $this->getFilteredHallIds();

        return Hall::select(
            'halls.id',
            'halls.name',
            'halls.owner_id',
            // FIX: Alias as 'bookings_count' to match Blade template
            DB::raw('COUNT(bookings.id) as bookings_count'),
            DB::raw('SUM(bookings.total_amount) as total_revenue'),
            DB::raw('SUM(bookings.owner_payout) as total_payout'),
            // FIX: Compute average booking value — was missing, caused null in PDF
            DB::raw('AVG(bookings.total_amount) as avg_booking_value')
        )
            ->leftJoin('bookings', function ($join) {
                $join->on('halls.id', '=', 'bookings.hall_id')
                    ->whereBetween('bookings.booking_date', [
                        Carbon::parse($this->startDate),
                        Carbon::parse($this->endDate),
                    ])
                    ->where('bookings.payment_status', 'paid');
            })
            ->whereIn('halls.id', $hallIds)
            ->groupBy('halls.id', 'halls.name', 'halls.owner_id')
            ->orderByDesc('total_revenue')
            ->get();
    }

    /**
     * Time slot distribution for bar chart.
     *
     * @return array{labels: array, data: array}
     */
    public function timeSlotDistribution(): array
    {
        $hallIds = $this->getFilteredHallIds();

        $data = Booking::select('time_slot', DB::raw('COUNT(*) as count'))
            ->whereIn('hall_id', $hallIds)
            ->whereBetween('booking_date', [Carbon::parse($this->startDate), Carbon::parse($this->endDate)])
            ->whereIn('status', ['confirmed', 'completed'])
            ->groupBy('time_slot')
            ->get();

        return [
            'labels' => $data->pluck('time_slot')->map(fn($s) => __('slots.' . $s))->toArray(),
            'data'   => $data->pluck('count')->toArray(),
        ];
    }

    /**
     * Monthly comparison (current vs previous month).
     *
     * @return array<string, mixed>
     */
    #[Computed]
    public function monthlyComparison(): array
    {
        /** @var ReportService $service */
        $service = app(ReportService::class);
        return $service->getMonthlyComparison(Auth::id());
    }

    // ======================================================================
    // EXPORT ACTIONS
    // ======================================================================

    /**
     * Export report as PDF using mPDF.
     *
     * FIX: Clears font cache on first run to ensure fresh font metrics.
     * The stale cache from previous DomPDF/mPDF configurations was the
     * primary cause of Arabic rendering issues (missing letters, corrupted text).
     *
     * @param bool $debug Return raw HTML instead of PDF for browser inspection
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response|null
     */
    public function exportPDF(bool $debug = false)
    {
        try {
            // Resolve authenticated owner
            $ownerId = Auth::id();
            $user = User::findOrFail($ownerId);
            $hallOwner = $user->hallOwner ?? null;

            // Render Blade → HTML (HTML-first approach)
            $html = view('pdf.reports.owner-dashboard', [
                'stats'           => $this->dashboardStats,
                'hallPerformance' => $this->hallPerformance,
                'comparison'      => $this->monthlyComparison,
                'owner'           => $user,
                'hallOwner'       => $hallOwner,
                'startDate'       => $this->startDate,
                'endDate'         => $this->endDate,
                'generatedAt'     => now()->format('d M Y H:i'),
            ])->render();

            // Debug mode: return HTML for browser inspection
            if ($debug) {
                return response($html)->header('Content-Type', 'text/html; charset=utf-8');
            }

            // Save debug HTML (non-blocking)
            try {
                Storage::disk('local')->put(
                    'pdf-debug/report-' . now()->timestamp . '.html',
                    $html
                );
            } catch (\Throwable $e) {
                Log::warning('PDF debug save failed: ' . $e->getMessage());
            }

            // Generate PDF via mPDF
            // clearFontCache: true on first call ensures stale cache is purged
            $pdfService = new PdfExportService(clearFontCache: true);
            $filename = "owner_report_{$this->startDate}_{$this->endDate}.pdf";

            return $pdfService
                ->generateFromHtml($html)
                ->download($filename);
        } catch (\Throwable $e) {
            Log::error('PDF Export failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            Notification::make()
                ->danger()
                ->title(__('owner_report.reports.errors.export_failed'))
                ->body($e->getMessage())
                ->send();

            return null;
        }
    }

    /**
     * Header actions — Export CSV, Export PDF, Print.
     *
     * FIX: Only ONE PDF action now (was two: one DomPDF, one mPDF).
     *
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            // Export CSV
            Action::make('export_csv')
                ->label(__('owner_report.reports.actions.export_csv'))
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->form([
                    Forms\Components\Select::make('report_type')
                        ->label(__('owner_report.reports.export.type'))
                        ->options([
                            'summary'  => __('owner_report.reports.export.summary'),
                            'bookings' => __('owner_report.reports.export.bookings'),
                            'revenue'  => __('owner_report.reports.export.revenue'),
                            'halls'    => __('owner_report.reports.export.halls'),
                        ])
                        ->required()
                        ->default('summary'),
                ])
                ->action(fn(array $data) => $this->exportCSV($data['report_type'])),

            // Export PDF (mPDF with Arabic support)
            Action::make('export_pdf')
                ->label(__('owner_report.reports.actions.export_pdf'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(fn() => $this->exportPDF()),

            // Print (browser dialog)
            Action::make('print')
                ->label(__('owner_report.reports.actions.print'))
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->extraAttributes(['onclick' => 'window.print()']),
        ];
    }

    /**
     * Export report as CSV.
     *
     * @param string $reportType
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|null
     */
    public function exportCSV(string $reportType)
    {
        /** @var ReportService $service */
        $service = app(ReportService::class);

        $data = $service->generateExportData(
            $reportType,
            Carbon::parse($this->startDate),
            Carbon::parse($this->endDate),
            Auth::id()
        );

        if (empty($data)) {
            Notification::make()
                ->title(__('owner_report.reports.notifications.no_data'))
                ->warning()
                ->send();
            return null;
        }

        $filename = "report_{$reportType}_{$this->startDate}_{$this->endDate}.csv";

        return Response::streamDownload(function () use ($data): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

            if (!empty($data)) {
                fputcsv($handle, array_keys($data[0]));
            }

            foreach ($data as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=utf-8']);
    }

    /**
     * @return MaxWidth
     */
    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }
}
