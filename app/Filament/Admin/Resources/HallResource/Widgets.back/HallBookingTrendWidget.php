<?php

declare(strict_types=1);

/**
 * HallBookingTrendWidget - Booking Trend Line Chart
 *
 * Displays a line chart showing booking trends over the past 6 months.
 * Includes comparison between confirmed/completed vs cancelled bookings
 * to provide insights into booking patterns and cancellation rates.
 *
 * Features:
 * - Interactive line chart with multiple datasets
 * - Filterable by time period (30, 60, 90, 180 days)
 * - Responsive design for mobile and desktop
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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HallBookingTrendWidget extends ChartWidget
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
    protected static ?string $heading = 'Booking Trends';

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
    public ?string $filter = '90';

    /**
     * Get the chart heading with translation support.
     *
     * @return string|null
     */
    public function getHeading(): ?string
    {
        return __('Booking Trends');
    }

    /**
     * Get the chart description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return __('Booking activity over time');
    }

    /**
     * Define available filter options.
     *
     * @return array<string, string>|null
     */
    protected function getFilters(): ?array
    {
        return [
            '30' => __('Last 30 Days'),
            '60' => __('Last 60 Days'),
            '90' => __('Last 90 Days'),
            '180' => __('Last 6 Months'),
        ];
    }

    /**
     * Get the chart type.
     *
     * @return string
     */
    protected function getType(): string
    {
        return 'line';
    }

    /**
     * Get the chart data including labels and datasets.
     *
     * @return array{
     *     datasets: array<array{
     *         label: string,
     *         data: array<int|float>,
     *         fill?: bool|string,
     *         borderColor?: string,
     *         backgroundColor?: string,
     *         tension?: float,
     *         pointRadius?: int,
     *         pointHoverRadius?: int
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

        // Determine date range based on filter
        $days = (int) ($this->filter ?? 90);
        $startDate = now()->subDays($days)->startOfDay();
        $endDate = now()->endOfDay();

        // Determine grouping based on date range
        $groupByFormat = $days <= 30 ? '%Y-%m-%d' : '%Y-%m';
        $labelFormat = $days <= 30 ? 'd M' : 'M Y';

        // Generate date labels
        $labels = [];
        $confirmedData = [];
        $cancelledData = [];
        $pendingData = [];

        if ($days <= 30) {
            // Daily grouping
            for ($i = $days; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $labels[] = $date->format($labelFormat);

                $confirmedData[] = (int) $this->record->bookings()
                    ->whereIn('status', ['confirmed', 'completed'])
                    ->whereDate('booking_date', $date)
                    ->count();

                $cancelledData[] = (int) $this->record->bookings()
                    ->where('status', 'cancelled')
                    ->whereDate('booking_date', $date)
                    ->count();

                $pendingData[] = (int) $this->record->bookings()
                    ->where('status', 'pending')
                    ->whereDate('booking_date', $date)
                    ->count();
            }
        } else {
            // Monthly grouping
            $months = (int) ceil($days / 30);
            for ($i = $months - 1; $i >= 0; $i--) {
                $monthStart = now()->subMonths($i)->startOfMonth();
                $monthEnd = now()->subMonths($i)->endOfMonth();
                $labels[] = $monthStart->format($labelFormat);

                $confirmedData[] = (int) $this->record->bookings()
                    ->whereIn('status', ['confirmed', 'completed'])
                    ->whereBetween('booking_date', [$monthStart, $monthEnd])
                    ->count();

                $cancelledData[] = (int) $this->record->bookings()
                    ->where('status', 'cancelled')
                    ->whereBetween('booking_date', [$monthStart, $monthEnd])
                    ->count();

                $pendingData[] = (int) $this->record->bookings()
                    ->where('status', 'pending')
                    ->whereBetween('booking_date', [$monthStart, $monthEnd])
                    ->count();
            }
        }

        return [
            'datasets' => [
                [
                    'label' => __('Confirmed/Completed'),
                    'data' => $confirmedData,
                    'fill' => false,
                    'borderColor' => 'rgb(34, 197, 94)', // Green
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'tension' => 0.3,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
                [
                    'label' => __('Pending'),
                    'data' => $pendingData,
                    'fill' => false,
                    'borderColor' => 'rgb(251, 191, 36)', // Yellow
                    'backgroundColor' => 'rgba(251, 191, 36, 0.1)',
                    'tension' => 0.3,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
                [
                    'label' => __('Cancelled'),
                    'data' => $cancelledData,
                    'fill' => false,
                    'borderColor' => 'rgb(239, 68, 68)', // Red
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'tension' => 0.3,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
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
                        'stepSize' => 1,
                        'precision' => 0,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => __('Number of Bookings'),
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => __('Date'),
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
