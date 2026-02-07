<?php

declare(strict_types=1);

/**
 * HallBookingStatusWidget - Booking Status Distribution Doughnut Chart
 *
 * Displays a doughnut chart showing the distribution of bookings by status.
 * Provides quick visual insight into:
 * - Pending bookings requiring attention
 * - Confirmed/active bookings
 * - Completed bookings
 * - Cancelled bookings
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

class HallBookingStatusWidget extends ChartWidget
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
    protected static ?string $heading = 'Booking Distribution';

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
    public ?string $filter = 'all';

    /**
     * Get the chart heading with translation support.
     *
     * @return string|null
     */
    public function getHeading(): ?string
    {
        return __('Booking Distribution');
    }

    /**
     * Get the chart description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return __('Bookings by status');
    }

    /**
     * Define available filter options.
     *
     * @return array<string, string>|null
     */
    protected function getFilters(): ?array
    {
        return [
            'all' => __('All Time'),
            'month' => __('This Month'),
            'quarter' => __('This Quarter'),
            'year' => __('This Year'),
        ];
    }

    /**
     * Get the chart type.
     *
     * @return string
     */
    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * Get the chart data including labels and datasets.
     *
     * @return array{
     *     datasets: array<array{
     *         data: array<int>,
     *         backgroundColor: array<string>,
     *         borderColor: array<string>,
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

        // Build base query with date filter
        $query = $this->record->bookings();
        $query = $this->applyDateFilter($query);

        // Get counts for each status
        $pending = (int) (clone $query)->where('status', 'pending')->count();
        $confirmed = (int) (clone $query)->where('status', 'confirmed')->count();
        $completed = (int) (clone $query)->where('status', 'completed')->count();
        $cancelled = (int) (clone $query)->where('status', 'cancelled')->count();

        // Define colors for each status
        $colors = [
            'pending' => [
                'bg' => 'rgba(251, 191, 36, 0.8)',   // Yellow
                'border' => 'rgb(251, 191, 36)',
            ],
            'confirmed' => [
                'bg' => 'rgba(59, 130, 246, 0.8)',   // Blue
                'border' => 'rgb(59, 130, 246)',
            ],
            'completed' => [
                'bg' => 'rgba(34, 197, 94, 0.8)',    // Green
                'border' => 'rgb(34, 197, 94)',
            ],
            'cancelled' => [
                'bg' => 'rgba(239, 68, 68, 0.8)',    // Red
                'border' => 'rgb(239, 68, 68)',
            ],
        ];

        return [
            'datasets' => [
                [
                    'data' => [$pending, $confirmed, $completed, $cancelled],
                    'backgroundColor' => [
                        $colors['pending']['bg'],
                        $colors['confirmed']['bg'],
                        $colors['completed']['bg'],
                        $colors['cancelled']['bg'],
                    ],
                    'borderColor' => [
                        $colors['pending']['border'],
                        $colors['confirmed']['border'],
                        $colors['completed']['border'],
                        $colors['cancelled']['border'],
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => [
                __('Pending') . " ({$pending})",
                __('Confirmed') . " ({$confirmed})",
                __('Completed') . " ({$completed})",
                __('Cancelled') . " ({$cancelled})",
            ],
        ];
    }

    /**
     * Apply date filter to query based on selected filter.
     *
     * @param \Illuminate\Database\Eloquent\Relations\HasMany $query
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    protected function applyDateFilter($query)
    {
        return match ($this->filter) {
            'month' => $query->whereMonth('booking_date', now()->month)
                             ->whereYear('booking_date', now()->year),
            'quarter' => $query->whereBetween('booking_date', [
                now()->startOfQuarter(),
                now()->endOfQuarter(),
            ]),
            'year' => $query->whereYear('booking_date', now()->year),
            default => $query, // 'all' - no filter
        };
    }

    /**
     * Get chart configuration options.
     *
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 20,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) {
                            var label = context.label || '';
                            var value = context.raw || 0;
                            var total = context.dataset.data.reduce((a, b) => a + b, 0);
                            var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return value + ' bookings (' + percentage + '%)';
                        }",
                    ],
                ],
            ],
            'cutout' => '60%',
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
