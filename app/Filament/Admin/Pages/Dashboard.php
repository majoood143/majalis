<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Services\DashboardExportService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;

/**
 * Admin Dashboard Page
 *
 * Main dashboard for platform administrators with comprehensive
 * statistics, charts, and export functionality.
 *
 * @package App\Filament\Admin\Pages
 */
class Dashboard extends BaseDashboard
{
    /**
     * The navigation icon.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-home';

    /**
     * Get the page title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return __('admin.dashboard.title');
    }

    /**
     * Dashboard subheading.
     *
     * @return string|null
     */
    public function getSubheading(): ?string
    {
        $date = now()->format('l, F j, Y');
        return __('admin.dashboard.subheading', ['date' => $date]);
    }

    /**
     * Dashboard widgets configuration.
     *
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            \App\Filament\Admin\Widgets\StatsOverviewWidget::class,
            \App\Filament\Admin\Widgets\RevenueChartWidget::class,
            \App\Filament\Admin\Widgets\BookingStatsWidget::class,
            \App\Filament\Admin\Widgets\RecentBookingsWidget::class,
            \App\Filament\Admin\Widgets\TopHallsWidget::class,
            \App\Filament\Admin\Widgets\PayoutStatsWidget::class,
            \App\Filament\Admin\Widgets\LatestOwnersWidget::class,
        ];
    }

    /**
     * Dashboard layout configuration.
     *
     * @return int|string|array
     */
    public function getColumns(): int|string|array
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
                ->label(__('admin.dashboard.refresh'))
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->redirect(request()->header('Referer')))
                ->color('gray'),

            // Export Action with form
            Action::make('export')
                ->label(__('admin.dashboard.export'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    Forms\Components\DatePicker::make('start_date')
                        ->label(__('admin.dashboard.start_date'))
                        ->default(now()->startOfMonth())
                        ->native(false)
                        ->displayFormat('d M Y')
                        ->required(),

                    Forms\Components\DatePicker::make('end_date')
                        ->label(__('admin.dashboard.end_date'))
                        ->default(now())
                        ->native(false)
                        ->displayFormat('d M Y')
                        ->required(),

                    Forms\Components\Select::make('format')
                        ->label(__('admin.dashboard.export_format'))
                        ->options([
                            'csv' => 'CSV',
                            'pdf' => 'PDF',
                        ])
                        ->default('csv')
                        ->required(),

                    Forms\Components\Select::make('report_type')
                        ->label(__('admin.dashboard.report_type'))
                        ->options([
                            'summary' => __('admin.reports.export.summary'),
                            'bookings' => __('admin.reports.export.bookings'),
                            'revenue' => __('admin.reports.export.revenue'),
                            'halls' => __('admin.reports.export.halls'),
                        ])
                        ->default('summary')
                        ->required(),
                ])
                ->modalHeading(__('admin.dashboard.export_confirm'))
                ->modalDescription(__('admin.dashboard.export_description'))
                ->modalSubmitActionLabel(__('admin.dashboard.export'))
                ->action(fn (array $data) => $this->exportDashboardData($data)),

            // View Reports Action
            Action::make('view_reports')
                ->label(__('admin.dashboard.view_reports'))
                ->icon('heroicon-o-chart-bar-square')
                ->color('primary')
                ->url(fn () => route('filament.admin.pages.reports')),
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
                return $exportService->exportAdminDashboardPDF($startDate, $endDate);
            }

            return $exportService->exportAdminDashboardCSV($startDate, $endDate, $reportType);
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('admin.dashboard.export_error'))
                ->body($e->getMessage())
                ->danger()
                ->send();

            return null;
        }
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
