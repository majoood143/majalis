<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\HallResource\Widgets;

use App\Models\Hall;
use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HallRevenueChartWidget extends ChartWidget
{
    public ?Model $record = null;
    protected static ?string $heading = 'Revenue Analysis';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = '120s';

    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
    ];

    public ?string $filter = '6';

    public function getHeading(): ?string
    {
        return __('widgets.hall-revenue-chart.heading');
    }

    public function getDescription(): ?string
    {
        return __('widgets.hall-revenue-chart.description');
    }

    protected function getFilters(): ?array
    {
        return [
            '3' => __('widgets.hall-revenue-chart.filters.3'),
            '6' => __('widgets.hall-revenue-chart.filters.6'),
            '12' => __('widgets.hall-revenue-chart.filters.12'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        if (!$this->record instanceof Hall) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $months = (int) ($this->filter ?? 6);
        $labels = [];
        $grossRevenueData = [];
        $commissionData = [];
        $ownerPayoutData = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();

            $labels[] = $monthStart->format('M Y');

            $monthlyBookings = $this->record->bookings()
                ->whereIn('status', ['confirmed', 'completed'])
                ->whereIn('payment_status', ['paid', 'partial'])
                ->whereBetween('booking_date', [$monthStart, $monthEnd]);

            $grossRevenue = (float) (clone $monthlyBookings)->sum('total_amount');
            $commission = (float) (clone $monthlyBookings)->sum('commission_amount');
            $ownerPayout = (float) (clone $monthlyBookings)->sum('owner_payout');

            $grossRevenueData[] = round($grossRevenue, 3);
            $commissionData[] = round($commission, 3);
            $ownerPayoutData[] = round($ownerPayout, 3);
        }

        return [
            'datasets' => [
                [
                    'label' => __('widgets.hall-revenue-chart.datasets.gross_revenue'),
                    'data' => $grossRevenueData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => __('widgets.hall-revenue-chart.datasets.platform_commission'),
                    'data' => $commissionData,
                    'backgroundColor' => 'rgba(251, 191, 36, 0.8)',
                    'borderColor' => 'rgb(251, 191, 36)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => __('widgets.hall-revenue-chart.datasets.owner_payout'),
                    'data' => $ownerPayoutData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) { return value.toFixed(3) + ' ' + '" . __('common.currency.omr') . "'; }",
                    ],
                    'title' => [
                        'display' => true,
                        'text' => __('widgets.hall-revenue-chart.axes.y_title'),
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => __('widgets.hall-revenue-chart.axes.x_title'),
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) { return context.dataset.label + ': ' + context.raw.toFixed(3) + ' ' + '" . __('common.currency.omr') . "'; }",
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
