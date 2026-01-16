<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Livewire\Attributes\Computed;

/**
 * Admin Reports Page
 *
 * Comprehensive reporting dashboard for platform administrators.
 * Includes revenue reports, booking analytics, owner performance,
 * and commission reports with export capabilities.
 *
 * @package App\Filament\Admin\Pages
 */
class Reports extends Page implements HasForms
{
    use InteractsWithForms;

    /**
     * The view for this page.
     *
     * @var string
     */
    protected static string $view = 'filament.admin.pages.reports';

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

    public static function getModelLabel(): string
    {
        return __('admin.reports.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.reports.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.reports.navigation_label');
    }

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
     * Active report tab.
     *
     * @var string
     */
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

    /**
     * Get the page title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return __('admin.reports.title');
    }

    /**
     * Get the page heading.
     *
     * @return string
     */
    public function getHeading(): string
    {
        return __('admin.reports.heading');
    }

    /**
     * Get the page subheading.
     *
     * @return string|null
     */
    public function getSubheading(): ?string
    {
        return __('admin.reports.subheading');
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
                Forms\Components\Grid::make(4)
                    ->schema([
                        Forms\Components\DatePicker::make('startDate')
                            ->label(__('admin.reports.filters.start_date'))
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->maxDate(now())
                            ->live()
                            ->afterStateUpdated(fn () => $this->loadData()),

                        Forms\Components\DatePicker::make('endDate')
                            ->label(__('admin.reports.filters.end_date'))
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->maxDate(now())
                            ->live()
                            ->afterStateUpdated(fn () => $this->loadData()),

                        Forms\Components\Select::make('preset')
                            ->label(__('admin.reports.filters.preset'))
                            ->options([
                                'today' => __('admin.reports.presets.today'),
                                'yesterday' => __('admin.reports.presets.yesterday'),
                                'this_week' => __('admin.reports.presets.this_week'),
                                'last_week' => __('admin.reports.presets.last_week'),
                                'this_month' => __('admin.reports.presets.this_month'),
                                'last_month' => __('admin.reports.presets.last_month'),
                                'this_quarter' => __('admin.reports.presets.this_quarter'),
                                'this_year' => __('admin.reports.presets.this_year'),
                            ])
                            ->placeholder(__('admin.reports.filters.custom'))
                            ->live()
                            ->afterStateUpdated(fn ($state) => $this->applyPreset($state)),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('refresh')
                                ->label(__('admin.reports.actions.refresh'))
                                ->icon('heroicon-o-arrow-path')
                                ->action(fn () => $this->loadData()),
                        ])->verticallyAlignEnd(),
                    ]),
            ]);
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
     * Load report data.
     *
     * @return void
     */
    public function loadData(): void
    {
        // Trigger computed properties refresh
        unset($this->dashboardStats);
        unset($this->revenueTrend);
        unset($this->bookingDistribution);
        unset($this->topHalls);
        unset($this->topOwners);
        unset($this->commissionReport);
    }

    /**
     * Get dashboard statistics.
     *
     * @return array
     */
    #[Computed]
    public function dashboardStats(): array
    {
        $service = app(ReportService::class);

        return $service->getAdminDashboardStats(
            Carbon::parse($this->startDate),
            Carbon::parse($this->endDate)
        );
    }

    /**
     * Get revenue trend data.
     *
     * @return array
     */
    #[Computed]
    public function revenueTrend(): array
    {
        $service = app(ReportService::class);
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);

        // Determine grouping based on date range
        $days = $start->diffInDays($end);
        $groupBy = match (true) {
            $days <= 31 => 'day',
            $days <= 90 => 'week',
            default => 'month',
        };

        $data = $service->getRevenueTrend($start, $end, null, $groupBy);

        return [
            'labels' => $data->pluck('period')->toArray(),
            'revenue' => $data->pluck('revenue')->map(fn ($v) => (float) $v)->toArray(),
            'commission' => $data->pluck('commission')->map(fn ($v) => (float) $v)->toArray(),
            'bookings' => $data->pluck('bookings')->toArray(),
        ];
    }

    /**
     * Get booking status distribution.
     *
     * @return array
     */
    #[Computed]
    public function bookingDistribution(): array
    {
        $service = app(ReportService::class);

        $data = $service->getBookingStatusDistribution(
            Carbon::parse($this->startDate),
            Carbon::parse($this->endDate)
        );

        return [
            'labels' => $data->pluck('status')->map(fn ($s) => ucfirst($s))->toArray(),
            'data' => $data->pluck('count')->toArray(),
        ];
    }

    /**
     * Get top performing halls.
     *
     * @return \Illuminate\Support\Collection
     */
    #[Computed]
    public function topHalls(): \Illuminate\Support\Collection
    {
        $service = app(ReportService::class);

        return $service->getTopHalls(
            Carbon::parse($this->startDate),
            Carbon::parse($this->endDate),
            10
        );
    }

    /**
     * Get top performing owners.
     *
     * @return \Illuminate\Support\Collection
     */
    #[Computed]
    public function topOwners(): \Illuminate\Support\Collection
    {
        $service = app(ReportService::class);

        return $service->getTopOwners(
            Carbon::parse($this->startDate),
            Carbon::parse($this->endDate),
            10
        );
    }

    /**
     * Get commission report.
     *
     * @return array
     */
    #[Computed]
    public function commissionReport(): array
    {
        $service = app(ReportService::class);

        return $service->getCommissionReport(
            Carbon::parse($this->startDate),
            Carbon::parse($this->endDate)
        );
    }

    /**
     * Get time slot distribution.
     *
     * @return array
     */
    #[Computed]
    public function timeSlotDistribution(): array
    {
        $service = app(ReportService::class);

        $data = $service->getTimeSlotDistribution(
            Carbon::parse($this->startDate),
            Carbon::parse($this->endDate)
        );

        return [
            'labels' => $data->pluck('time_slot')->map(fn ($s) => __('slots.' . $s))->toArray(),
            'data' => $data->pluck('count')->toArray(),
        ];
    }

    /**
     * Get header actions.
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            // Export CSV
            Action::make('export_csv')
                ->label(__('admin.reports.actions.export_csv'))
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->form([
                    Forms\Components\Select::make('report_type')
                        ->label(__('admin.reports.export.type'))
                        ->options([
                            'summary' => __('admin.reports.export.summary'),
                            'bookings' => __('admin.reports.export.bookings'),
                            'revenue' => __('admin.reports.export.revenue'),
                            'halls' => __('admin.reports.export.halls'),
                        ])
                        ->required()
                        ->default('summary'),
                ])
                ->action(fn (array $data) => $this->exportCSV($data['report_type'])),

            // Export PDF
            Action::make('export_pdf')
                ->label(__('admin.reports.actions.export_pdf'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(fn () => $this->exportPDF()),

            // Print
            Action::make('print')
                ->label(__('admin.reports.actions.print'))
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
     * @param string $reportType
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCSV(string $reportType)
    {
        $service = app(ReportService::class);

        $data = $service->generateExportData(
            $reportType,
            Carbon::parse($this->startDate),
            Carbon::parse($this->endDate)
        );

        if (empty($data)) {
            Notification::make()
                ->title(__('admin.reports.notifications.no_data'))
                ->warning()
                ->send();
            return null;
        }

        $filename = "report_{$reportType}_{$this->startDate}_{$this->endDate}.csv";

        return Response::streamDownload(function () use ($data): void {
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
    }

    /**
     * Export report as PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPDF()
    {
        $pdf = Pdf::loadView('pdf.reports.admin-dashboard', [
            'stats' => $this->dashboardStats,
            'topHalls' => $this->topHalls,
            'topOwners' => $this->topOwners,
            'commissionReport' => $this->commissionReport,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'generatedAt' => now()->format('d M Y H:i'),
            'generatedBy' => Auth::user()->name,
        ]);

        $filename = "admin_report_{$this->startDate}_{$this->endDate}.pdf";

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Set active tab.
     *
     * @param string $tab
     * @return void
     */
    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
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
