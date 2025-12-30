<?php

declare(strict_types=1);

namespace App\Filament\Owner\Widgets;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Ticket;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class PendingActionsWidget extends Widget
{
    /**
     * Widget view
     */
    protected static string $view = 'filament.owner.widgets.pending-actions';

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
    protected static ?string $pollingInterval = '30s';

    /**
     * Get view data
     */
    public function getViewData(): array
    {
        $user = Auth::user();

        // Collect all pending actions
        $pendingActions = $this->collectPendingActions($user);

        // Sort by priority and urgency
        $sortedActions = $this->sortActionsByPriority($pendingActions);

        // Get action counts by type
        $actionCounts = $this->getActionCounts($pendingActions);

        return [
            'actions' => $sortedActions,
            'counts' => $actionCounts,
            'totalActions' => $pendingActions->count(),
            'urgentActions' => $pendingActions->where('priority', 'urgent')->count(),
        ];
    }

    /**
     * Collect all pending actions
     */
    protected function collectPendingActions($user): Collection
    {
        $actions = collect();

        // Pending booking confirmations
        $pendingBookings = Booking::whereHas('hall', function ($q) use ($user) {
            $q->where('owner_id', $user->id);
        })
            ->where('status', 'pending')
            ->where('booking_date', '>=', now())
            ->get()
            ->map(function ($booking) {
                return (object)[
                    'type' => 'booking_confirmation',
                    'title' => __('owner.actions.confirm_booking'),
                    'description' => __('owner.actions.booking_needs_confirmation', [
                        'number' => $booking->booking_number,
                        'date' => $booking->booking_date->format('M j'),
                    ]),
                    'icon' => 'heroicon-o-calendar',
                    'color' => 'warning',
                    'priority' => $booking->booking_date->diffInDays(now()) <= 3 ? 'urgent' : 'normal',
                    'action_url' => route('filament.owner.resources.bookings.view', $booking),
                    'created_at' => $booking->created_at,
                    'data' => $booking,
                ];
            });

        $actions = $actions->merge($pendingBookings);

        // ✅ FIXED: Pending payments with proper type casting
        $pendingPaymentBookings = Booking::whereHas('hall', function ($q) use ($user) {
            $q->where('owner_id', $user->id);
        })
            ->where(function ($query) {
                $query->where('payment_status', 'pending')
                    ->orWhere(function ($q) {
                        $q->where('payment_type', 'advance')
                            ->where('balance_due', '>', 0)
                            ->whereNull('balance_paid_at');
                    });
            })
            ->where('booking_date', '>=', now()->subDays(30))
            ->get()
            ->map(function ($booking) {
                $daysUntilEvent = now()->diffInDays($booking->booking_date, false);
                $isOverdue = $daysUntilEvent < 0;

                // ✅ FIXED: Cast to float to handle string values from database
                $pendingAmount = (float) ($booking->balance_due ?? $booking->total_amount ?? 0);

                return (object)[
                    'type' => 'payment_follow_up',
                    'title' => __('owner.actions.payment_pending'),
                    'description' => __('owner.actions.payment_needs_follow_up', [
                        'amount' => number_format($pendingAmount, 3), // ✅ Now safe with float
                        'days' => abs($daysUntilEvent),
                        'status' => $isOverdue ? __('owner.actions.overdue') : __('owner.actions.due_soon'),
                    ]),
                    'icon' => 'heroicon-o-banknotes',
                    'color' => $isOverdue ? 'danger' : 'warning',
                    'priority' => $isOverdue ? 'urgent' : ($daysUntilEvent <= 3 ? 'high' : 'normal'),
                    'action_url' => route('filament.owner.resources.bookings.view', $booking),
                    'created_at' => $booking->created_at,
                    'data' => $booking,
                ];
            });

        $actions = $actions->merge($pendingPaymentBookings);

        // ✅ FIXED: Reviews section with safe type casting
        if (class_exists(Review::class) && Schema::hasTable('reviews')) {
            $unansweredReviews = Review::whereHas('hall', function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            })
                ->whereNull('owner_response')
                ->where('created_at', '>=', now()->subDays(7))
                ->get()
                ->map(function ($review) {
                    return (object)[
                        'type' => 'review_response',
                        'title' => __('owner.actions.respond_to_review'),
                        'description' => __('owner.actions.review_needs_response', [
                            'rating' => (int) $review->rating, // ✅ Cast to int
                            'customer' => $review->user->name ?? __('owner.actions.guest'),
                        ]),
                        'icon' => 'heroicon-o-star',
                        'color' => $review->rating >= 4 ? 'success' : ($review->rating >= 3 ? 'warning' : 'danger'),
                        'priority' => $review->rating <= 2 ? 'high' : 'normal',
                        'action_url' => '#',
                        'created_at' => $review->created_at,
                        'data' => $review,
                    ];
                });

            $actions = $actions->merge($unansweredReviews);
        }

        return $actions;
    }

    /**
     * Sort actions by priority
     */
    protected function sortActionsByPriority(Collection $actions): Collection
    {
        $priorityOrder = [
            'urgent' => 1,
            'high' => 2,
            'normal' => 3,
            'low' => 4,
        ];

        return $actions->sortBy([
            fn($action) => $priorityOrder[$action->priority] ?? 5,
            fn($action) => $action->created_at,
        ])->values();
    }

    /**
     * Get action counts by type
     */
    protected function getActionCounts(Collection $actions): array
    {
        return [
            'booking_confirmation' => $actions->where('type', 'booking_confirmation')->count(),
            'payment_follow_up' => $actions->where('type', 'payment_follow_up')->count(),
            'review_response' => $actions->where('type', 'review_response')->count(),
            'support_ticket' => $actions->where('type', 'support_ticket')->count(),
            'hall_profile' => $actions->where('type', 'hall_profile')->count(),
            'availability_update' => $actions->where('type', 'availability_update')->count(),
        ];
    }
}
