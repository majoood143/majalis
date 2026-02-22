<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\HallResource\Widgets;

use App\Models\Hall;
use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HallBookingTrendWidget extends ChartWidget
{
    public ?Model $record = null;
    protected static ?string $heading = 'Booking Trends';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = '120s';

    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
    ];

    public ?string $filter = '90';

    public function getHeading(): ?string
    {
        return __('widgets.hall-booking-trend.heading');
    }

    public function getDescription(): ?string
    {
        return __('widgets.hall-booking-trend.description');
    }

    protected function getFilters(): ?array
    {
        return [
            '30' => __('widgets.hall-booking-trend.filters.30'),
            '60' => __('widgets.hall-booking-trend.filters.60'),
            '90' => __('widgets.hall-booking-trend.filters.90'),
            '180' => __('widgets.hall-booking-trend.filters.180'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        if (!$this->record instanceof Hall) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $days = (int) ($this->filter ?? 90);
        $startDate = now()->subDays($days)->startOfDay();
        $endDate = now()->endOfDay();

        $groupByFormat = $days <= 30 ? '%Y-%m-%d' : '%Y-%m';
        $labelFormat = $days <= 30 ? 'd M' : 'M Y';

        $labels = [];
        $confirmedData = [];
        $cancelledData = [];
        $pendingData = [];

        if ($days <= 30) {
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
                    'label' => __('widgets.hall-booking-trend.datasets.confirmed_completed'),
                    'data' => $confirmedData,
                    'fill' => false,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'tension' => 0.3,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
                [
                    'label' => __('widgets.hall-booking-trend.datasets.pending'),
                    'data' => $pendingData,
                    'fill' => false,
                    'borderColor' => 'rgb(251, 191, 36)',
                    'backgroundColor' => 'rgba(251, 191, 36, 0.1)',
                    'tension' => 0.3,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
                [
                    'label' => __('widgets.hall-booking-trend.datasets.cancelled'),
                    'data' => $cancelledData,
                    'fill' => false,
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'tension' => 0.3,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
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
                        'stepSize' => 1,
                        'precision' => 0,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => __('widgets.hall-booking-trend.axes.y_title'),
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => __('widgets.hall-booking-trend.axes.x_title'),
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
