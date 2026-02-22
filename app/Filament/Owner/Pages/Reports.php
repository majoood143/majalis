<?php

declare(strict_types=1);

namespace App\Filament\Owner\Pages;

use App\Models\Booking;
use App\Models\Hall;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
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
use Illuminate\Support\Facades\Response;
use Livewire\Attributes\Computed;
use App\Models\User;

/**
 * Owner Reports Page
 *
 * Comprehensive reporting dashboard for hall owners.
 * Includes earnings reports, booking analytics, hall performance,
 * and export capabilities.
 *
 * @package App\Filament\Owner\Pages
 *
 * @property-read array $dashboardStats Dashboard statistics computed property
 * @property-read array $revenueTrend Revenue trend data computed property
 * @property-read array $bookingDistribution Booking status distribution computed property
 * @property-read Collection $hallPerformance Hall performance data computed property
 * @property-read array $timeSlotDistribution Time slot distribution computed property
 * @property-read array $monthlyComparison Monthly comparison data computed property
 */
class Reports extends Page implements HasForms
{
    use InteractsWithForms;

    /**
     * The view for this page.
     *
     * @var string
     */
    protected static string $view = 'filament.owner.pages.reports';

    /**
     * The navigation icon.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    /**
     * The navigation group.
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = 'Analytics';

    /**
     * The navigation sort order.
     *
     * @var int|null
     */
    protected static ?int $navigationSort = 10;

    /**
     * The page slug.
     *
     * @var string|null
     */
    protected static ?string $slug = 'reports';

    /**
     * Report date range start.
     *
     * @var string|null
     */
    public ?string $startDate = null;

    /**
     * Report date range end.
     *
     * @var string|null
     */
    public ?string $endDate = null;

    /**
     * Selected hall filter.
     *
     * FIX: This property is now actively used in all computed properties
     * to filter data by specific hall when selected.
     *
     * @var int|null
     */
    public ?int $hallId = null;

    /**
     * Active report tab.
     *
     * @var string
     */
    public string $activeTab = 'overview';

    /**
     * Mount the page.
     *
     * Initializes the date range to current month by default.
     *
     * @return void
     */
    public function mount(): void
    {
        // Set default date range to current month
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    /**
     * Get the page title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return __('owner_report.reports.title');
    }

    /**
     * Get the page heading.
     *
     * @return string
     */
    public function getHeading(): string
    {
        return __('owner_report.reports.heading');
    }

    /**
     * Get the page subheading.
     *
     * @return string|null
     */
    public function getSubheading(): ?string
    {
        return __('owner_report.reports.subheading');
    }

    /**
     * Get the navigation label.
     *
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('owner_report.reports.nav_label');
    }

    /**
     * Define the filter form.
     *
     * Creates the form with date pickers, hall selector, and preset options.
     *
     * @param Form $form The form instance
     * @return Form The configured form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(5)
                    ->schema([
                        // Start date filter with live updates
                        Forms\Components\DatePicker::make('startDate')
                            ->label(__('owner_report.reports.filters.start_date'))
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->maxDate(now())
                            ->live()
                            ->afterStateUpdated(fn () => $this->loadData()),

                        // End date filter with live updates
                        Forms\Components\DatePicker::make('endDate')
                            ->label(__('owner_report.reports.filters.end_date'))
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->maxDate(now())
                            ->live()
                            ->afterStateUpdated(fn () => $this->loadData()),

                        // Hall selector for filtering by specific hall
                        Forms\Components\Select::make('hallId')
                            ->label(__('owner_report.reports.filters.hall'))
                            ->options(fn () => $this->getOwnerHalls())
                            ->placeholder(__('owner_report.reports.filters.all_halls'))
                            ->live()
                            ->afterStateUpdated(fn () => $this->loadData()),

                        // Quick preset selector for common date ranges
                        Forms\Components\Select::make('preset')
                            ->label(__('owner_report.reports.filters.preset'))
                            ->options([
                                'today' => __('owner_report.reports.presets.today'),
                                'yesterday' => __('owner_report.reports.presets.yesterday'),
                                'this_week' => __('owner_report.reports.presets.this_week'),
                                'last_week' => __('owner_report.reports.presets.last_week'),
                                'this_month' => __('owner_report.reports.presets.this_month'),
                                'last_month' => __('owner_report.reports.presets.last_month'),
                                'this_quarter' => __('owner_report.reports.presets.this_quarter'),
                                'this_year' => __('owner_report.reports.presets.this_year'),
                            ])
                            ->placeholder(__('owner_report.reports.filters.custom'))
                            ->live()
                            ->afterStateUpdated(fn ($state) => $this->applyPreset($state)),

                        // Refresh action button
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('refresh')
                                ->label(__('owner_report.reports.actions.refresh'))
                                ->icon('heroicon-o-arrow-path')
                                ->action(fn () => $this->loadData()),
                        ])->verticallyAlignEnd(),
                    ]),
            ]);
    }

    /**
     * Get owner's halls for filter dropdown.
     *
     * Retrieves all halls belonging to the current owner with proper
     * locale handling for translatable names.
     *
     * @return array<int, string> Array of hall names keyed by ID
     */
    protected function getOwnerHalls(): array
    {
        return Hall::where('owner_id', Auth::id())
            ->pluck('name', 'id')
            ->map(function ($name) {
                // Handle translatable name field
                return is_array($name) ? ($name[app()->getLocale()] ?? $name['en'] ?? '') : $name;
            })
            ->toArray();
    }

    /**
     * Get the hall IDs to filter by.
     *
     * FIX: Central helper that resolves which hall IDs should be used
     * in all queries. If a specific hall is selected (and belongs to
     * the current owner), returns only that hall's ID. Otherwise,
     * returns all hall IDs for the owner.
     *
     * @return Collection<int, int> Collection of hall IDs
     */
    protected function getFilteredHallIds(): Collection
    {
        // If a specific hall is selected, validate ownership and return it
        if ($this->hallId) {
            $hallBelongsToOwner = Hall::where('id', $this->hallId)
                ->where('owner_id', Auth::id())
                ->exists();

            if ($hallBelongsToOwner) {
                return collect([$this->hallId]);
            }
        }

        // Default: return all halls for the owner
        return Hall::where('owner_id', Auth::id())->pluck('id');
    }

    /**
     * Apply date preset.
     *
     * Sets the start and end dates based on the selected preset option.
     *
     * @param string|null $preset The preset identifier
     * @return void
     */
    public function applyPreset(?string $preset): void
    {
        if (!$preset) {
            return;
        }

        // Determine date range based on preset
        [$start, $end] = match ($preset) {
            'today' => [now()->startOfDay(), now()],
            'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'this_week' => [now()->startOfWeek(), now()],
            'last_week' => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            'this_month' => [now()->startOfMonth(), now()],
            'last_month' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'this_quarter' => [now()->startOfQuarter(), now()],
            'this_year' => [now()->startOfYear(), now()],
            default => [now()->startOfMonth(), now()],
        };

        $this->startDate = $start->format('Y-m-d');
        $this->endDate = $end->format('Y-m-d');
        $this->loadData();
    }

    /**
     * Load/refresh report data.
     *
     * Clears computed property caches to force data refresh
     * and dispatches event to reinitialize charts.
     *
     * IMPORTANT: In Livewire 3, computed properties are cached per-request.
     * We use unset() to clear the cached values which forces recalculation
     * on next access.
     *
     * @return void
     */
    public function loadData(): void
    {
        // Clear computed property caches using Livewire 3 approach
        // This forces the computed properties to recalculate on next access
        unset(
            $this->dashboardStats,
            $this->revenueTrend,
            $this->bookingDistribution,
            $this->hallPerformance,
            $this->timeSlotDistribution,
            $this->monthlyComparison
        );

        // Dispatch event to reinitialize charts in JavaScript
        // This is critical for charts to update with new data
        $this->dispatch('chartsDataUpdated');
    }

    /**
     * Set the active tab.
     *
     * Switches to the specified tab and dispatches event
     * to reinitialize charts on the new tab.
     *
     * FIX: This method dispatches the event that JavaScript
     * listens for to reinitialize Chart.js charts.
     *
     * @param string $tab The tab identifier to switch to
     * @return void
     */
    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;

        // CRITICAL FIX: Dispatch event for chart reinitialization
        // This event is listened to by the JavaScript in the blade template
        // Without this, charts won't render when switching tabs
        $this->dispatch('activeTabUpdated', tab: $tab);
    }

    /**
     * Get all chart data for JavaScript consumption.
     *
     * Called by the @script block via `$wire.getChartData()` to fetch
     * fresh data from the server. This ensures charts always reflect
     * the current filter state (date range, hall selection).
     *
     * Returns the existing #[Computed] properties aggregated into a
     * single array, using the key names the JavaScript expects.
     *
     * @return array{
     *     revenueTrend: array{labels: array, revenue: array, payout: array, bookings: array},
     *     bookingDistribution: array{labels: array, data: array},
     *     timeSlotDistribution: array{labels: array, data: array}
     * }
     */
    public function getChartData(): array
    {
        return [
            // Earnings trend line chart data (Earnings tab)
            'revenueTrend' => $this->revenueTrend,

            // Booking status doughnut chart data (Bookings tab)
            'bookingDistribution' => $this->bookingDistribution,

            // Time slot bar chart data (Earnings tab)
            'timeSlotDistribution' => $this->timeSlotDistribution,
        ];
    }

    /**
     * Get dashboard statistics.
     *
     * Computed property that retrieves comprehensive statistics
     * for the owner's dashboard within the selected date range.
     *
     * FIX: Now respects the hallId filter. When a specific hall
     * is selected, statistics are scoped to that hall only.
     *
     * @return array<string, mixed> Array of dashboard statistics
     */
    #[Computed]
    public function dashboardStats(): array
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);
        $hallIds = $this->getFilteredHallIds();

        // Base query scoped to filtered halls and date range
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

            // Hall Stats — adjusted for hall filter context
            'total_halls' => $this->hallId
                ? 1
                : Hall::where('owner_id', Auth::id())->count(),
            'active_halls' => $this->hallId
                ? (int) Hall::where('id', $this->hallId)->where('is_active', true)->exists()
                : Hall::where('owner_id', Auth::id())->where('is_active', true)->count(),

            // Payout Stats — always show owner-level payouts
            'pending_payouts' => (float) \App\Models\OwnerPayout::where('owner_id', Auth::id())
                ->where('status', \App\Enums\PayoutStatus::PENDING)
                ->sum('net_payout'),
            'completed_payouts' => (float) \App\Models\OwnerPayout::where('owner_id', Auth::id())
                ->where('status', \App\Enums\PayoutStatus::COMPLETED)
                ->sum('net_payout'),

            // Performance
            'average_booking_value' => (float) ($bookingsQuery->clone()
                ->where('payment_status', 'paid')
                ->avg('total_amount') ?? 0),
            'total_guests' => (int) $bookingsQuery->clone()
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('number_of_guests'),

            // Period Info
            'period_start' => $startDate->format('Y-m-d'),
            'period_end' => $endDate->format('Y-m-d'),
        ];
    }

    /**
     * Get revenue trend data.
     *
     * Computed property that retrieves revenue and payout trends
     * over the selected date range. Automatically determines the
     * best grouping (day/week/month) based on range length.
     *
     * FIX: Now respects the hallId filter by scoping to filtered hall IDs.
     *
     * @return array{labels: array, revenue: array, payout: array, bookings: array}
     */
    #[Computed]
    public function revenueTrend(): array
    {
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $hallIds = $this->getFilteredHallIds();

        // Determine optimal grouping based on date range length
        $days = $start->diffInDays($end);
        $dateFormat = match (true) {
            $days <= 31 => '%Y-%m-%d',
            $days <= 90 => '%Y-%u',
            default => '%Y-%m',
        };

        // FIX: Query directly with filtered hall IDs instead of relying
        // on the service which only accepts ownerId (no hallId support)
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

        // Return formatted data for Chart.js
        return [
            'labels' => $data->pluck('period')->toArray(),
            'revenue' => $data->pluck('revenue')->map(fn ($v) => (float) $v)->toArray(),
            'payout' => $data->pluck('payout')->map(fn ($v) => (float) $v)->toArray(),
            'bookings' => $data->pluck('bookings')->toArray(),
        ];
    }

    /**
     * Get booking status distribution.
     *
     * Computed property that retrieves the count of bookings
     * grouped by their status within the selected date range.
     *
     * FIX: Now respects the hallId filter.
     *
     * @return array{labels: array, data: array}
     */
    #[Computed]
    public function bookingDistribution(): array
    {
        $hallIds = $this->getFilteredHallIds();

        // FIX: Query directly with filtered hall IDs
        $data = Booking::select('status', DB::raw('COUNT(*) as count'))
            ->whereIn('hall_id', $hallIds)
            ->whereBetween('booking_date', [
                Carbon::parse($this->startDate),
                Carbon::parse($this->endDate),
            ])
            ->groupBy('status')
            ->get();

        // Return formatted data with translated labels
        return [
            'labels' => $data->pluck('status')->map(fn ($s) => __('owner_report.reports.status.' . $s))->toArray(),
            'data' => $data->pluck('count')->toArray(),
        ];
    }

    /**
     * Get hall performance data.
     *
     * Computed property that retrieves performance metrics
     * for halls owned by the current user.
     *
     * FIX: Now respects the hallId filter — shows only the
     * selected hall when one is chosen.
     *
     * @return Collection
     */
    #[Computed]
    public function hallPerformance(): Collection
    {
        $hallIds = $this->getFilteredHallIds();

        // FIX: Filter by specific hall IDs (respects hallId selection)
        return Hall::select(
            'halls.id',
            'halls.name',
            'halls.owner_id',
            DB::raw('COUNT(bookings.id) as total_bookings'),
            DB::raw('SUM(bookings.total_amount) as total_revenue'),
            DB::raw('SUM(bookings.owner_payout) as total_payout')
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
     * Export report as PDF.
     *
     * Generates a PDF file download with owner dashboard summary.
     *
     * FIX: Previously used Auth::user() directly which can return null
     * in Livewire streamDownload contexts. Now uses Auth::id() captured
     * early + User::findOrFail() for reliable user resolution.
     * Also added null-safe operator for hallOwner relationship.
     *
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportPDF()
    {
        /**
         * FIX: Capture Auth::id() first (reliable in Livewire action context),
         * then resolve the full User model from the database.
         * This avoids the null Auth::user() issue in streamDownload contexts.
         */
        $ownerId = Auth::id();
        $user = User::findOrFail($ownerId);
        $hallOwner = $user->hallOwner ?? null;

        $pdf = Pdf::setOptions([
            'fontDir' => storage_path('fonts'),
            'fontCache' => storage_path('fonts'),
            'defaultFont' => 'Tajawal'
        ]);

        // Generate PDF from view template
        $pdf = Pdf::loadView('pdf.reports.owner-dashboard', [
            'stats' => $this->dashboardStats,
            'hallPerformance' => $this->hallPerformance,
            'comparison' => $this->monthlyComparison,
            'owner' => $user,
            'hallOwner' => $hallOwner,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'generatedAt' => now()->format('d M Y H:i'),
            //'locale' => app()->getLocale(),
            //'direction' => app()->getLocale() === 'ar' ? 'rtl' : 'ltr',
            'fontFamily' => 'Tajawal, DejaVu Sans, sans-serif',
        ]);

        $filename = "owner_report_{$this->startDate}_{$this->endDate}.pdf";

        return response()->streamDownload(
            fn() => print($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Export report as PDF.
     *
     * Generates a PDF file download with owner dashboard summary.
     *
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\StreamedResponse
     */
//     public function exportPDF()
//     {
//         $ownerId = Auth::id();
//         $user = User::findOrFail($ownerId);
//         $hallOwner = $user->hallOwner ?? null;

//         // FIX: Register the font properly before generating PDF
//         $fontPath = storage_path('fonts/Tajawal-Regular.ttf');

//         // Verify font exists
//         if (!file_exists($fontPath)) {
//             Notification::make()
//                 ->danger()
//                 ->title('Font Error')
//                 ->body('Tajawal font file not found. Please ensure it exists in storage/fonts/')
//                 ->send();
//             return null;
//         }

//         // FIX: Create HTML content with proper font-face declaration
//         $html = view('pdf.reports.owner-dashboard', [
//             'stats' => $this->dashboardStats,
//             'hallPerformance' => $this->hallPerformance,
//             'comparison' => $this->monthlyComparison,
//             'owner' => $user,
//             'hallOwner' => $hallOwner,
//             'startDate' => $this->startDate,
//             'endDate' => $this->endDate,
//             'generatedAt' => now()->format('d M Y H:i'),
//             'fontFamily' => 'Tajawal, DejaVu Sans, sans-serif',
//         ])->render();

//         // FIX: Add font-face declaration to the HTML
//         $html = '<!DOCTYPE html>
// <html>
// <head>
//     <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
//     <style>
//         @font-face {
//             font-family: \'Tajawal\';
//             src: url("' . $fontPath . '") format("truetype");
//             font-weight: normal;
//             font-style: normal;
//             font-display: swap;
//         }
//         @font-face {
//             font-family: \'Tajawal\';
//             src: url("' . storage_path('fonts/Tajawal-Bold.ttf') . '") format("truetype");
//             font-weight: bold;
//             font-style: normal;
//             font-display: swap;
//         }
//         body {
//             font-family: \'Tajawal\', DejaVu Sans, sans-serif;
//             direction: ' . (app()->getLocale() === 'ar' ? 'rtl' : 'ltr') . ';
//             text-align: ' . (app()->getLocale() === 'ar' ? 'right' : 'left') . ';
//         }
//     </style>
// </head>
// <body>
//     ' . $html . '
// </body>
// </html>';

//         // FIX: Use loadHTML instead of loadView to include our font-face declaration
//         $pdf = Pdf::setOptions([
//             'fontDir' => storage_path('fonts'),
//             'fontCache' => storage_path('fonts'),
//             'defaultFont' => 'Tajawal',
//             'isHtml5ParserEnabled' => true,
//             'isRemoteEnabled' => true,
//             'isFontSubsettingEnabled' => true,
//             'chroot' => storage_path('fonts'),
//         ]);

//         $pdf = Pdf::loadHTML($html);

//         $filename = "owner_report_{$this->startDate}_{$this->endDate}.pdf";

//         return response()->streamDownload(
//             fn() => print($pdf->output()),
//             $filename,
//             ['Content-Type' => 'application/pdf']
//         );
//     }

    /**
     * Get time slot distribution.
     *
     * Computed property that retrieves the count of bookings
     * grouped by time slot (morning, afternoon, evening, full_day).
     *
     * FIX: Now respects the hallId filter.
     *
     * @return array{labels: array, data: array}
     */
    #[Computed]
    public function timeSlotDistribution(): array
    {
        $hallIds = $this->getFilteredHallIds();

        // FIX: Query directly with filtered hall IDs
        $data = Booking::select('time_slot', DB::raw('COUNT(*) as count'))
            ->whereIn('hall_id', $hallIds)
            ->whereBetween('booking_date', [
                Carbon::parse($this->startDate),
                Carbon::parse($this->endDate),
            ])
            ->whereIn('status', ['confirmed', 'completed'])
            ->groupBy('time_slot')
            ->get();

        // Return formatted data with translated slot names
        return [
            'labels' => $data->pluck('time_slot')->map(fn ($s) => __('slots.' . $s))->toArray(),
            'data' => $data->pluck('count')->toArray(),
        ];
    }

    /**
     * Get monthly comparison data.
     *
     * Computed property that retrieves comparison metrics
     * between current and previous month.
     *
     * NOTE: Monthly comparison always uses the full owner scope
     * (not filtered by hallId) as it's a high-level trend indicator.
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

    /**
     * Get header actions.
     *
     * Defines the export and print actions available in the page header.
     *
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [

            // Export CSV Action
            Action::make('export_csv')
                ->label(__('owner_report.reports.actions.export_csv'))
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->form([
                    Forms\Components\Select::make('report_type')
                        ->label(__('owner_report.reports.export.type'))
                        ->options([
                            'summary' => __('owner_report.reports.export.summary'),
                            'bookings' => __('owner_report.reports.export.bookings'),
                            'revenue' => __('owner_report.reports.export.revenue'),
                            'halls' => __('owner_report.reports.export.halls'),
                        ])
                        ->required()
                        ->default('summary'),
                ])
                ->action(fn(array $data) => $this->exportCSV($data['report_type'])),

            // Export to PDF action
            Action::make('exportPdf')
                ->label(__('owner_report.reports.actions.export_pdf'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    try {
                        $pdf = Pdf::loadView('pdf.reports.owner-dashboard', [
                            'stats' => $this->dashboardStats,
                            'comparison' => $this->monthlyComparison,
                            'hallPerformance' => $this->hallPerformance,
                            'startDate' => $this->startDate,
                            'endDate' => $this->endDate,
                        ]);

                        $filename = 'owner-report-' . now()->format('Y-m-d') . '.pdf';

                        return Response::streamDownload(
                            fn () => print($pdf->output()),
                            $filename,
                            ['Content-Type' => 'application/pdf']
                        );
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('owner_report.reports.errors.export_failed'))
                            ->body($e->getMessage())
                            ->send();
                    }
                }),
            // Export PDF Action
            Action::make('export_pdf')
                ->label(__('owner_report.reports.actions.export_pdf'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(fn() => $this->exportPDF()),

            // Print Action
            Action::make('print')
                ->label(__('owner_report.reports.actions.print'))
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->extraAttributes([
                    'onclick' => 'window.print()',
                ]),
        ];
    }

    /**
     * Export report as CSV.
     *
     * Generates a CSV file download with the selected report type data.
     *
     * @param string $reportType The type of report to export
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

        // Check for empty data
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

            // Add UTF-8 BOM for proper Excel encoding
            fwrite($handle, "\xEF\xBB\xBF");

            // Write header row
            if (!empty($data)) {
                fputcsv($handle, array_keys($data[0]));
            }

            // Write data rows
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }

    /**
     * Get max content width.
     *
     * @return MaxWidth
     */
    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }
}
