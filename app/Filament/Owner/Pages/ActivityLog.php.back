<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use Carbon\Carbon;

class ActivityLog extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static string $view = 'filament.owner.pages.activity-log';
    protected static ?string $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'Activity Log';
    protected static ?int $navigationSort = 100;

    public $perPage = 25;
    public $totalActivities = 0;
    public $groupedActivities = [];
    public $stats = [
        'today' => 0,
        'week' => 0,
        'month' => 0,
    ];

    public function mount(): void
    {
        $this->loadActivities();
        $this->loadStats();
    }

    protected function loadActivities(): void
    {
        // Load activities with pagination
        $activities = Activity::query()
            ->with(['causer'])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $this->totalActivities = $activities->total();
        $this->groupedActivities = $this->groupActivitiesByDate($activities->items());
    }

    protected function loadStats(): void
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();

        $this->stats = [
            'today' => Activity::whereDate('created_at', $today)->count(),
            'week' => Activity::where('created_at', '>=', $weekStart)->count(),
            'month' => Activity::where('created_at', '>=', $monthStart)->count(),
        ];
    }

    protected function groupActivitiesByDate(array $activities): array
    {
        $grouped = [];

        foreach ($activities as $activity) {
            $date = $activity->created_at->toDateString();
            $formattedDate = $activity->created_at->format('F j, Y');

            if (!isset($grouped[$formattedDate])) {
                $grouped[$formattedDate] = [];
            }

            $grouped[$formattedDate][] = (object)[
                'description' => $this->getActivityDescription($activity),
                'details' => $this->getActivityDetails($activity),
                'time' => $activity->created_at->format('g:i A'),
                'causer' => $activity->causer ? $activity->causer->name : 'System',
                'icon' => $this->getActivityIcon($activity->log_name),
                'color' => $this->getActivityColor($activity->log_name),
            ];
        }

        return $grouped;
    }

    protected function getActivityDescription($activity): string
    {
        $subjectType = class_basename($activity->subject_type);
        $event = $activity->event;

        $descriptions = [
            'created' => "New {$subjectType} created",
            'updated' => "{$subjectType} updated",
            'deleted' => "{$subjectType} deleted",
            'restored' => "{$subjectType} restored",
            'forceDeleted' => "{$subjectType} permanently deleted",
        ];

        return $descriptions[$event] ?? "{$subjectType} {$event}";
    }

    protected function getActivityDetails($activity): ?string
    {
        if ($activity->properties && isset($activity->properties['attributes'])) {
            $attributes = $activity->properties['attributes'];

            if (isset($attributes['name'])) {
                return $attributes['name'];
            }

            if (isset($attributes['title'])) {
                return $attributes['title'];
            }

            if (isset($attributes['email'])) {
                return $attributes['email'];
            }
        }

        return null;
    }

    protected function getActivityIcon(string $logName): string
    {
        $icons = [
            'default' => 'heroicon-o-document-text',
            'user' => 'heroicon-o-user',
            'product' => 'heroicon-o-shopping-bag',
            'order' => 'heroicon-o-shopping-cart',
            'category' => 'heroicon-o-tag',
            'settings' => 'heroicon-o-cog',
            'authentication' => 'heroicon-o-lock-closed',
        ];

        return $icons[$logName] ?? $icons['default'];
    }

    protected function getActivityColor(string $logName): string
    {
        $colors = [
            'default' => 'gray',
            'user' => 'blue',
            'product' => 'green',
            'order' => 'purple',
            'category' => 'yellow',
            'settings' => 'indigo',
            'authentication' => 'red',
        ];

        return $colors[$logName] ?? $colors['default'];
    }

    public function loadMore(): void
    {
        $this->perPage += 25;
        $this->loadActivities();
    }

    protected function getViewData(): array
    {
        return [
            'groupedActivities' => $this->groupedActivities,
            'stats' => $this->stats,
            'totalActivities' => $this->totalActivities,
            'hasMore' => $this->totalActivities > $this->perPage,
        ];
    }
}
