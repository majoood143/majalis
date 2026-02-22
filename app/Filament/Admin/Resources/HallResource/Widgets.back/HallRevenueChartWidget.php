<?php

declare(strict_types=1);

/**
 * HallRevenueChartWidget - Revenue Analysis Bar Chart
 *
 * Displays a bar chart showing revenue trends over configurable time periods.
 * Breaks down revenue by:
 * - Gross Revenue (total_amount)
 * - Platform Commission (commission_amount)
 * - Owner Payout (owner_payout)
 *
 * This provides administrators with clear financial insights for each hall.
 *
 * @package App\Filament\Admin\Resources\HallResource\Widgets
 * @version 1.0.0
 * @author Majalis Development Team
 */

namespace App\Filament\Admin\Resources\HallResource\Widgets;

use App\Models\Hall;
use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HallRevenueChartWidget extends ChartWidget
{
    /**
     * The hall record being viewed.
     *
     * @var Hall|Model|null
     */
    public ?Model $record = null;

    /**
     * Chart heading.
     *
     * @var string|null
     */
    protected static ?string $heading = 'Revenue Analysis';

    /**
     * Maximum height of the chart.
     *
     * @var string|null
     */
    protected static ?string $maxHeight = '300px';

    /**
     * Polling interval for auto-refresh.
     *
     * @var string|null
     */
    protected static ?string $pollingInterval = '120s';

    /**
     * Widget column span - responsive configuration.
     *
     * @var int|string|array
     */
    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
    ];

    /**
     * Current filter selection.
     *
     * @var string|null
     */
    public ?string $filter = '6';

    /**
     * Get the chart heading with translation support.
     *
     * @return string|null
     */
    public function getHeading(): ?string
    {
        return __('Revenue Analysis');
    }

    /**
     * Get the chart description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return __('Revenue breakdown by month');
    }

    /**
     * Define available filter options.
     *
     * @return array<string, string>|null
     */
    protected function getFilters(): ?array
    {
        return [
            '3' => __('Last 3 Months'),
            '6' => __('Last 6 Months'),
            '12' => __('Last 12 Months'),
        ];
    }

    /**
     * Get the chart type.
     *
     * @return string
     */
    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * Get the chart data including labels and datasets.
     *
     * @return array{
     *     datasets: array<array{
     *         label: string,
     *         data: array<float>,
     *         backgroundColor?: string,
     *         borderColor?: string,
     *         borderWidth?: int
     *     }>,
     *     labels: array<string>
     * }
     */
    protected function getData(): array
    {
        // Early return if no record
        if (!$this->record instanceof Hall) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        // Determine number of months based on filter
        $months = (int) ($this->filter ?? 6);

        // Arrays to hold data
        $labels = [];
        $grossRevenueData = [];
        $commissionData = [];
        $ownerPayoutData = [];

        // Generate data for each month
        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();

            // Add month label
            $labels[] = $monthStart->format('M Y');

            // Query for paid bookings in this month
            $monthlyBookings = $this->record->bookings()
                ->whereIn('status', ['confirmed', 'completed'])
                ->whereIn('payment_status', ['paid', 'partial'])
                ->whereBetween('booking_date', [$monthStart, $monthEnd]);

            // Calculate totals with proper type casting
            $grossRevenue = (float) (clone $monthlyBookings)->sum('total_amount');
            $commission = (float) (clone $monthlyBookings)->sum('commission_amount');
            $ownerPayout = (float) (clone $monthlyBookings)->sum('owner_payout');

            // Round to 3 decimal places for OMR
            $grossRevenueData[] = round($grossRevenue, 3);
            $commissionData[] = round($commission, 3);
            $ownerPayoutData[] = round($ownerPayout, 3);
        }

        return [
            'datasets' => [
                [
                    'label' => __('Gross Revenue (OMR)'),
                    'data' => $grossRevenueData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)', // Blue
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => __('Platform Commission (OMR)'),
                    'data' => $commissionData,
                    'backgroundColor' => 'rgba(251, 191, 36, 0.8)', // Yellow
                    'borderColor' => 'rgb(251, 191, 36)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => __('Owner Payout (OMR)'),
                    'data' => $ownerPayoutData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)', // Green
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Get chart configuration options.
     *
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) { return value.toFixed(3) + ' OMR'; }",
                    ],
                    'title' => [
                        'display' => true,
                        'text' => __('Amount (OMR)'),
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => __('Month'),
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
                        'label' => "function(context) { return context.dataset.label + ': ' + context.raw.toFixed(3) + ' OMR'; }",
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
