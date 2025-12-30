<?php

declare(strict_types=1);

namespace App\Filament\Owner\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Actions\Action;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    use HasFiltersAction;

    /**
     * Dashboard title with user's name
     */
    public function getTitle(): string|Htmlable
    {
        return __('owner.dashboard.title', [
            'name' => auth()->user()->name
        ]);
    }

    /**
     * Dynamic greeting based on time
     */
    public function getHeading(): string|Htmlable
    {
        $greeting = $this->getTimeBasedGreeting();
        return $greeting . ', ' . auth()->user()->name . '! 👋';
    }

    /**
     * Dashboard subheading
     */
    public function getSubheading(): ?string
    {
        $date = now()->format('l, F j, Y');
        return __('owner.dashboard.subheading', ['date' => $date]);
    }

    /**
     * Get time-based greeting
     */
    protected function getTimeBasedGreeting(): string
    {
        $hour = now()->format('H');

        if ($hour < 12) {
            return __('owner.dashboard.good_morning');
        } elseif ($hour < 17) {
            return __('owner.dashboard.good_afternoon');
        } else {
            return __('owner.dashboard.good_evening');
        }
    }

    /**
     * Dashboard widgets configuration
     */
    public function getWidgets(): array
    {
        return [
            \App\Filament\Owner\Widgets\StatsOverviewWidget::class,
            \App\Filament\Owner\Widgets\RevenueChartWidget::class,
            \App\Filament\Owner\Widgets\BookingStatsWidget::class,
            \App\Filament\Owner\Widgets\RecentBookingsWidget::class,
            \App\Filament\Owner\Widgets\UpcomingBookingsWidget::class,
            \App\Filament\Owner\Widgets\HallPerformanceWidget::class,
            \App\Filament\Owner\Widgets\RecentActivitiesWidget::class,
            \App\Filament\Owner\Widgets\PendingActionsWidget::class,
        ];
    }

    /**
     * Dashboard layout configuration
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
     * Header actions
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label(__('owner.dashboard.refresh'))
                ->icon('heroicon-o-arrow-path')
                ->action(fn() => $this->redirect(request()->header('Referer')))
                ->color('gray'),

            Action::make('export')
                ->label(__('owner.dashboard.export'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportDashboardData())
                ->requiresConfirmation()
                ->modalHeading(__('owner.dashboard.export_confirm')),

            FilterAction::make()
                ->form([
                    \Filament\Forms\Components\DatePicker::make('start_date')
                        ->label(__('owner.dashboard.start_date'))
                        ->default(now()->startOfMonth()),
                    \Filament\Forms\Components\DatePicker::make('end_date')
                        ->label(__('owner.dashboard.end_date'))
                        ->default(now()->endOfMonth()),
                    \Filament\Forms\Components\Select::make('hall_id')
                        ->label(__('owner.dashboard.select_hall'))
                        ->options(fn() => auth()->user()->halls()->pluck('name->en', 'id'))
                        ->placeholder(__('owner.dashboard.all_halls')),
                ]),
        ];
    }

    /**
     * Export dashboard data
     */
    protected function exportDashboardData(): void
    {
        // Implementation for exporting dashboard data to Excel/PDF
        // We'll implement this in Part 7 (Reports)
    }
}
