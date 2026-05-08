<?php

declare(strict_types=1);

namespace App\Filament\Owner\Pages;

use App\Filament\Owner\Widgets\StatsOverviewWidget;
use App\Filament\Owner\Widgets\RevenueChartWidget;
use App\Filament\Owner\Widgets\BookingStatsWidget;
use App\Filament\Owner\Widgets\RecentBookingsWidget;
use App\Filament\Owner\Widgets\UpcomingBookingsWidget;
use App\Filament\Owner\Widgets\HallPerformanceWidget;
use App\Filament\Owner\Widgets\RecentActivitiesWidget;
use App\Filament\Owner\Widgets\PendingActionsWidget;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Exception;
use Filament\Support\Enums\Width;
use App\Services\DashboardExportService;
use App\Services\ReportService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;

/**
 * Owner Dashboard Page
 *
 * Main dashboard for hall owners with statistics, charts,
 * and export functionality for their hall performance data.
 *
 * @package App\Filament\Owner\Pages
 */
class Dashboard extends BaseDashboard
{
    /**
     * The navigation icon.
     *
     * @var string|null
     */
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';

    /**
     * Export start date.
     *
     * @var string|null
     */
    public ?string $exportStartDate = null;

    /**
     * Export end date.
     *
     * @var string|null
     */
    public ?string $exportEndDate = null;

    /**
     * Mount the page.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->exportStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->exportEndDate = now()->format('Y-m-d');
    }

    /**
     * Get the page title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getTimeBasedGreeting() . ', ' . (Auth::user()?->name ?? '') . '! 👋';
    }

    /**
     * Dashboard subheading.
     *
     * @return string|null
     */
    public function getSubheading(): ?string
    {
        $date = now()->format('l, F j, Y');
        return __('owner.dashboard.subheading', ['date' => $date]);
    }

    /**
     * Get time-based greeting.
     *
     * @return string
     */
    protected function getTimeBasedGreeting(): string
    {
        $hour = (int) now()->format('H');

        if ($hour < 12) {
            return __('owner.dashboard.good_morning');
        } elseif ($hour < 17) {
            return __('owner.dashboard.good_afternoon');
        } else {
            return __('owner.dashboard.good_evening');
        }
    }

    /**
     * Dashboard widgets configuration.
     *
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            RevenueChartWidget::class,
            BookingStatsWidget::class,
            RecentBookingsWidget::class,
            UpcomingBookingsWidget::class,
            HallPerformanceWidget::class,
            RecentActivitiesWidget::class,
            PendingActionsWidget::class,
        ];
    }

    /**
     * Dashboard layout configuration.
     *
     * @return int|string|array
     */
    public function getColumns(): int|array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
            'xl' => 4,
            '2xl' => 4,
        ];
    }

    /**
     * Header actions.
     *
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            // Refresh Action
            Action::make('refresh')
                ->label(__('owner.dashboard.refresh'))
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->redirect(request()->header('Referer')))
                ->color('gray'),

            // Export Action with form
            Action::make('export')
                ->label(__('owner.dashboard.export'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->schema([
                    DatePicker::make('start_date')
                        ->label(__('owner.dashboard.start_date'))
                        ->default(now()->startOfMonth())
                        ->native(false)
                        ->displayFormat('d M Y')
                        ->required(),

                    DatePicker::make('end_date')
                        ->label(__('owner.dashboard.end_date'))
                        ->default(now())
                        ->native(false)
                        ->displayFormat('d M Y')
                        ->required(),

                    Select::make('format')
                        ->label(__('owner.dashboard.export_format'))
                        ->options([
                            'csv' => 'CSV',
                            'pdf' => 'PDF',
                        ])
                        ->default('csv')
                        ->required(),

                    Select::make('report_type')
                        ->label(__('owner.dashboard.report_type'))
                        ->options([
                            'summary' => __('owner.reports.export.summary'),
                            'bookings' => __('owner.reports.export.bookings'),
                            'revenue' => __('owner.reports.export.revenue'),
                            'halls' => __('owner.reports.export.halls'),
                        ])
                        ->default('summary')
                        ->required(),
                ])
                ->modalHeading(__('owner.dashboard.export_confirm'))
                ->modalDescription(__('owner.dashboard.export_description'))
                ->modalSubmitActionLabel(__('owner.dashboard.export'))
                ->action(fn (array $data) => $this->exportDashboardData($data)),

            // View Reports Action
            Action::make('view_reports')
                ->label(__('owner.dashboard.view_reports'))
                ->icon('heroicon-o-chart-bar-square')
                ->color('primary')
                ->url(fn () => route('filament.owner.pages.reports')),
        ];
    }

    /**
     * Export dashboard data.
     *
     * @param array $data
     * @return mixed
     */
    public function exportDashboardData(array $data = [])
    {
        $startDate = isset($data['start_date'])
            ? Carbon::parse($data['start_date'])
            : now()->startOfMonth();

        $endDate = isset($data['end_date'])
            ? Carbon::parse($data['end_date'])
            : now();

        $format = $data['format'] ?? 'csv';
        $reportType = $data['report_type'] ?? 'summary';

        $exportService = app(DashboardExportService::class);

        try {
            if ($format === 'pdf') {
                return $exportService->exportOwnerDashboardPDF(
                    Auth::id(),
                    $startDate,
                    $endDate
                );
            }

            return $exportService->exportOwnerDashboardCSV(
                Auth::id(),
                $startDate,
                $endDate,
                $reportType
            );
        } catch (Exception $e) {
            Notification::make()
                ->title(__('owner.dashboard.export_error'))
                ->body($e->getMessage())
                ->danger()
                ->send();

            return null;
        }
    }

    /**
     * Get max content width.
     *
     * @return Width
     */
    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }
}
