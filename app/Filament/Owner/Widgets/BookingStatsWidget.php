<?php

declare(strict_types=1);

namespace App\Filament\Owner\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class BookingStatsWidget extends ChartWidget
{
    /**
     * Widget heading
     */
    protected static ?string $heading = 'Booking Statistics';

    /**
     * Widget column span
     */
    protected int | string | array $columnSpan = [
        'sm' => 'full',
        'md' => 'full',
        'lg' => 2,
        'xl' => 2,
    ];

    /**
     * Widget max height
     */
    protected static ?string $maxHeight = '300px';

    /**
     * Chart type
     */
    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * Get heading
     */
    public function getHeading(): ?string
    {
        return __('owner.widgets.booking_statistics');
    }

    /**
     * Get description
     */
    public function getDescription(): ?string
    {
        $filters = $this->getFilters();
        $view = $filters['view'] ?? 'status';

        return match ($view) {
            'status' => __('owner.widgets.bookings_by_status'),
            'slot' => __('owner.widgets.bookings_by_slot'),
            'hall' => __('owner.widgets.bookings_by_hall'),
            'source' => __('owner.widgets.bookings_by_source'),
            default => __('owner.widgets.booking_distribution')
        };
    }

    /**
     * Get chart data
     */
    protected function getData(): array
    {
        $user = Auth::user();
        $filters = $this->getFilters();
        $view = $filters['view'] ?? 'status';

        return match ($view) {
            'status' => $this->getStatusData($user),
            'slot' => $this->getTimeSlotData($user),
            'hall' => $this->getHallData($user),
            'source' => $this->getSourceData($user),
            default => $this->getStatusData($user)
        };
    }

    /**
     * Get status distribution data
     */
    protected function getStatusData($user): array
    {
        $data = Booking::selectRaw('status, COUNT(*) as count')
            ->whereHas('hall', function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $labels = [];
        $values = [];
        $colors = [];

        $statusConfig = [
            'pending' => ['label' => __('owner.status.pending'), 'color' => 'rgb(251, 191, 36)'],
            'confirmed' => ['label' => __('owner.status.confirmed'), 'color' => 'rgb(34, 197, 94)'],
            'completed' => ['label' => __('owner.status.completed'), 'color' => 'rgb(59, 130, 246)'],
            'cancelled' => ['label' => __('owner.status.cancelled'), 'color' => 'rgb(239, 68, 68)'],
        ];

        foreach ($statusConfig as $status => $config) {
            if (isset($data[$status]) && $data[$status] > 0) {
                $labels[] = $config['label'];
                $values[] = $data[$status];
                $colors[] = $config['color'];
            }
        }

        return [
            'datasets' => [
                [
                    'label' => __('owner.widgets.bookings'),
                    'data' => $values,
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Get time slot distribution data
     */
    protected function getTimeSlotData($user): array
    {
        $data = Booking::selectRaw('time_slot, COUNT(*) as count')
            ->whereHas('hall', function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            })
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereMonth('booking_date', now()->month)
            ->whereYear('booking_date', now()->year)
            ->groupBy('time_slot')
            ->pluck('count', 'time_slot')
            ->toArray();

        $labels = [];
        $values = [];
        $colors = [
            'rgb(251, 191, 36)', // Morning - Yellow
            'rgb(249, 115, 22)', // Afternoon - Orange
            'rgb(99, 102, 241)', // Evening - Indigo
            'rgb(168, 85, 247)', // Full Day - Purple
        ];

        $slots = ['morning', 'afternoon', 'evening', 'full_day'];
        $colorIndex = 0;

        foreach ($slots as $slot) {
            if (isset($data[$slot]) && $data[$slot] > 0) {
                $labels[] = __("owner.slots.{$slot}");
                $values[] = $data[$slot];
            }
        }

        return [
            'datasets' => [
                [
                    'label' => __('owner.widgets.bookings'),
                    'data' => $values,
                    'backgroundColor' => array_slice($colors, 0, count($values)),
                    'borderWidth' => 0,
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Get hall distribution data
     */
    protected function getHallData($user): array
    {
        $data = Booking::selectRaw('hall_id, halls.name, COUNT(*) as count')
            ->join('halls', 'bookings.hall_id', '=', 'halls.id')
            ->where('halls.owner_id', $user->id)
            ->whereIn('bookings.status', ['confirmed', 'completed'])
            ->whereMonth('bookings.booking_date', now()->month)
            ->whereYear('bookings.booking_date', now()->year)
            ->groupBy('hall_id', 'halls.name')
            ->get();

        $labels = [];
        $values = [];
        $colors = [
            'rgb(59, 130, 246)',
            'rgb(34, 197, 94)',
            'rgb(249, 115, 22)',
            'rgb(168, 85, 247)',
            'rgb(236, 72, 153)',
            'rgb(245, 158, 11)',
        ];

        foreach ($data as $index => $item) {
            $hallName = json_decode($item->name, true);
            $labels[] = $hallName[app()->getLocale()] ?? $hallName['en'] ?? 'Hall ' . $item->hall_id;
            $values[] = $item->count;
        }

        return [
            'datasets' => [
                [
                    'label' => __('owner.widgets.bookings'),
                    'data' => $values,
                    'backgroundColor' => array_slice($colors, 0, count($values)),
                    'borderWidth' => 0,
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Get booking source data
     */
    protected function getSourceData($user): array
    {
        $data = Booking::selectRaw('
                CASE
                    WHEN customer_id IS NOT NULL THEN "registered"
                    ELSE "guest"
                END as source,
                COUNT(*) as count
            ')
            ->whereHas('hall', function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->groupBy('source')
            ->pluck('count', 'source')
            ->toArray();

        $labels = [
            __('owner.widgets.registered_customers'),
            __('owner.widgets.guest_bookings'),
        ];

        $values = [
            $data['registered'] ?? 0,
            $data['guest'] ?? 0,
        ];

        $colors = [
            'rgb(34, 197, 94)',
            'rgb(251, 191, 36)',
        ];

        return [
            'datasets' => [
                [
                    'label' => __('owner.widgets.bookings'),
                    'data' => $values,
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Get chart filters
     */
    protected function getFilters(): ?array
    {
        return [
            'status' => __('owner.widgets.by_status'),
            'slot' => __('owner.widgets.by_time_slot'),
            'hall' => __('owner.widgets.by_hall'),
            'source' => __('owner.widgets.by_source'),
        ];
    }

    /**
     * Get chart options
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 15,
                        'usePointStyle' => true,
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) {
                            let label = context.label || '';
                            let value = context.parsed;
                            let sum = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ((value / sum) * 100).toFixed(1);
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }",
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
            'responsive' => true,
        ];
    }
}
