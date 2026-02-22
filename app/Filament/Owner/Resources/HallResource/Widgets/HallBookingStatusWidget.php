<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\HallResource\Widgets;

use App\Models\Hall;
use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class HallBookingStatusWidget extends ChartWidget
{
    public ?Model $record = null;
    protected static ?string $heading = 'Booking Distribution';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = '120s';

    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
    ];

    public ?string $filter = 'all';

    public function getHeading(): ?string
    {
        return __('widgets.hall-booking-status.heading');
    }

    public function getDescription(): ?string
    {
        return __('widgets.hall-booking-status.description');
    }

    protected function getFilters(): ?array
    {
        return [
            'all' => __('widgets.hall-booking-status.filters.all'),
            'month' => __('widgets.hall-booking-status.filters.month'),
            'quarter' => __('widgets.hall-booking-status.filters.quarter'),
            'year' => __('widgets.hall-booking-status.filters.year'),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        if (!$this->record instanceof Hall) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $query = $this->record->bookings();
        $query = $this->applyDateFilter($query);

        $pending = (int) (clone $query)->where('status', 'pending')->count();
        $confirmed = (int) (clone $query)->where('status', 'confirmed')->count();
        $completed = (int) (clone $query)->where('status', 'completed')->count();
        $cancelled = (int) (clone $query)->where('status', 'cancelled')->count();

        $colors = [
            'pending' => [
                'bg' => 'rgba(251, 191, 36, 0.8)',
                'border' => 'rgb(251, 191, 36)',
            ],
            'confirmed' => [
                'bg' => 'rgba(59, 130, 246, 0.8)',
                'border' => 'rgb(59, 130, 246)',
            ],
            'completed' => [
                'bg' => 'rgba(34, 197, 94, 0.8)',
                'border' => 'rgb(34, 197, 94)',
            ],
            'cancelled' => [
                'bg' => 'rgba(239, 68, 68, 0.8)',
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
                __('common.booking_status.pending') . " ({$pending})",
                __('common.booking_status.confirmed') . " ({$confirmed})",
                __('common.booking_status.completed') . " ({$completed})",
                __('common.booking_status.cancelled') . " ({$cancelled})",
            ],
        ];
    }

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
            default => $query,
        };
    }

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
                            return value + ' ' + '" . __('common.bookings') . "' + ' (' + percentage + '%)';
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
