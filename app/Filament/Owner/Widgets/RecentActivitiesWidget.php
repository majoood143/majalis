<?php

declare(strict_types=1);

namespace App\Filament\Owner\Widgets;

use Filament\Widgets\Widget;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class RecentActivitiesWidget extends Widget
{
    /**
     * Widget view
     */
    protected static string $view = 'filament.owner.widgets.recent-activities';

    /**
     * Widget column span
     */
    protected int | string | array $columnSpan = [
        'sm' => 'full',
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
    ];

    /**
     * Widget polling interval
     */
    protected static ?string $pollingInterval = '60s';

    /**
     * Maximum activities to show
     */
    protected int $maxActivities = 10;

    /**
     * Get view data
     */
    public function getViewData(): array
    {
        $user = Auth::user();

        // Get activities related to owner's resources
        $activities = $this->getOwnerActivities($user);

        // Group activities by date
        $groupedActivities = $this->groupActivitiesByDate($activities);

        // Get activity statistics
        $stats = $this->getActivityStats($user);

        return [
            'activities' => $activities,
            'groupedActivities' => $groupedActivities,
            'stats' => $stats,
            'hasMore' => $activities->count() >= $this->maxActivities,
        ];
    }

    /**
     * Get owner's activities
     */
    protected function getOwnerActivities($user): Collection
    {
        return Activity::where(function ($query) use ($user) {
            // Activities caused by the owner
            $query->where('causer_id', $user->id)
                ->where('causer_type', get_class($user));
        })
            ->orWhere(function ($query) use ($user) {
                // Activities on owner's halls
                $query->where('subject_type', 'App\Models\Hall')
                    ->whereIn('subject_id', $user->halls()->pluck('id'));
            })
            ->orWhere(function ($query) use ($user) {
                // Activities on owner's bookings
                $query->where('subject_type', 'App\Models\Booking')
                    ->whereIn(
                        'subject_id',
                        \App\Models\Booking::whereHas('hall', function ($q) use ($user) {
                            $q->where('owner_id', $user->id);
                        })->pluck('id')
                    );
            })
            ->with(['causer', 'subject'])
            ->latest()
            ->limit($this->maxActivities)
            ->get()
            ->map(function ($activity) {
                // Format the activity for display
                return $this->formatActivity($activity);
            });
    }

    /**
     * Format activity for display
     */
    protected function formatActivity(Activity $activity): object
    {
        $formatted = (object)[
            'id' => $activity->id,
            'description' => $this->getActivityDescription($activity),
            'icon' => $this->getActivityIcon($activity),
            'color' => $this->getActivityColor($activity),
            'time' => $activity->created_at->diffForHumans(),
            'timestamp' => $activity->created_at,
            'details' => $this->getActivityDetails($activity),
            'causer' => $activity->causer?->name ?? __('owner.activities.system'),
            'subject_type' => class_basename($activity->subject_type),
            'event' => $activity->event ?? $activity->description,
        ];

        return $formatted;
    }

    /**
     * Get activity description
     */
    protected function getActivityDescription(Activity $activity): string
    {
        $event = $activity->event ?? $activity->description;
        $subjectType = class_basename($activity->subject_type);

        // Build description based on activity type
        $descriptions = [
            'Hall' => [
                'created' => __('owner.activities.hall_created', ['name' => $activity->subject?->name['en'] ?? '']),
                'updated' => __('owner.activities.hall_updated', ['name' => $activity->subject?->name['en'] ?? '']),
                'deleted' => __('owner.activities.hall_deleted'),
                'activated' => __('owner.activities.hall_activated', ['name' => $activity->subject?->name['en'] ?? '']),
                'deactivated' => __('owner.activities.hall_deactivated', ['name' => $activity->subject?->name['en'] ?? '']),
            ],
            'Booking' => [
                'created' => __('owner.activities.booking_created', [
                    'number' => $activity->subject?->booking_number ?? ''
                ]),
                'confirmed' => __('owner.activities.booking_confirmed', [
                    'number' => $activity->subject?->booking_number ?? ''
                ]),
                'cancelled' => __('owner.activities.booking_cancelled', [
                    'number' => $activity->subject?->booking_number ?? ''
                ]),
                'completed' => __('owner.activities.booking_completed', [
                    'number' => $activity->subject?->booking_number ?? ''
                ]),
                'payment_received' => __('owner.activities.payment_received', [
                    'amount' => number_format($activity->properties['amount'] ?? 0, 3)
                ]),
            ],
            'Payment' => [
                'paid' => __('owner.activities.payment_completed', ['amount' => number_format($activity->subject?->amount ?? 0, 3)]),
                'refunded' => __('owner.activities.payment_refunded', ['amount' => number_format($activity->subject?->amount ?? 0, 3)]),
                'failed' => __('owner.activities.payment_failed'),
            ],
            'Review' => [
                'created' => __('owner.activities.review_received', ['rating' => $activity->subject?->rating ?? 0]),
                'replied' => __('owner.activities.review_replied'),
            ],
        ];

        // Custom description handling
        if ($activity->description === 'login') {
            return __('owner.activities.user_logged_in');
        }

        if ($activity->description === 'logout') {
            return __('owner.activities.user_logged_out');
        }

        return $descriptions[$subjectType][$event]
            ?? $descriptions[$subjectType][$activity->description]
            ?? $activity->description
            ?? __('owner.activities.unknown_activity');
    }

    /**
     * Get activity icon
     */
    protected function getActivityIcon(Activity $activity): string
    {
        $subjectType = class_basename($activity->subject_type);

        $icons = [
            'Hall' => 'heroicon-o-building-office',
            'Booking' => 'heroicon-o-calendar-days',
            'Payment' => 'heroicon-o-credit-card',
            'Review' => 'heroicon-o-star',
            'User' => 'heroicon-o-user',
            'HallAvailability' => 'heroicon-o-clock',
        ];

        return $icons[$subjectType] ?? 'heroicon-o-information-circle';
    }

    /**
     * Get activity color
     */
    protected function getActivityColor(Activity $activity): string
    {
        $event = $activity->event ?? $activity->description;

        $colors = [
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'danger',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'completed' => 'success',
            'paid' => 'success',
            'refunded' => 'warning',
            'failed' => 'danger',
            'login' => 'primary',
            'logout' => 'gray',
        ];

        return $colors[$event] ?? 'gray';
    }

    /**
     * Get activity details
     */
    protected function getActivityDetails(Activity $activity): ?string
    {
        $properties = $activity->properties ?? collect();

        // Extract relevant details from properties
        if ($properties->has('old') && $properties->has('attributes')) {
            $changes = [];
            $attributes = $properties->get('attributes');
            $old = $properties->get('old');

            // Track specific field changes
            $trackedFields = ['status', 'total_amount', 'booking_date', 'time_slot'];

            foreach ($trackedFields as $field) {
                if (isset($attributes[$field]) && isset($old[$field]) && $attributes[$field] !== $old[$field]) {
                    $changes[] = __("owner.activities.field_changed.{$field}", [
                        'from' => $old[$field],
                        'to' => $attributes[$field],
                    ]);
                }
            }

            return implode(', ', $changes) ?: null;
        }

        return null;
    }

    /**
     * Group activities by date
     */
    protected function groupActivitiesByDate(Collection $activities): Collection
    {
        return $activities->groupBy(function ($activity) {
            $date = $activity->timestamp;

            if ($date->isToday()) {
                return __('owner.activities.today');
            } elseif ($date->isYesterday()) {
                return __('owner.activities.yesterday');
            } else {
                return $date->format('F j, Y');
            }
        });
    }

    /**
     * Get activity statistics
     */
    protected function getActivityStats($user): array
    {
        $today = Activity::where('causer_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        $thisWeek = Activity::where('causer_id', $user->id)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $thisMonth = Activity::where('causer_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            'today' => $today,
            'this_week' => $thisWeek,
            'this_month' => $thisMonth,
        ];
    }
}
