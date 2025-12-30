<?php

declare(strict_types=1);

namespace App\Filament\Owner\Widgets;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class UpcomingBookingsWidget extends Widget
{
    protected static string $view = 'filament.owner.widgets.upcoming-bookings';

    protected int | string | array $columnSpan = [
        'sm' => 'full',
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
    ];

    protected static ?string $pollingInterval = '60s';

    protected function getViewData(): array
    {
        $user = Auth::user();

        // For the count badge - get ALL bookings
        $allBookings = Booking::whereHas('hall', function ($q) use ($user) {
            $q->where('owner_id', $user->id);
        })
            ->where('status', 'confirmed')
            ->whereBetween('booking_date', [now(), now()->addDays(7)])
            ->get();

        // For display - get limited bookings
        $displayBookings = Booking::whereHas('hall', function ($q) use ($user) {
            $q->where('owner_id', $user->id);
        })
            ->where('status', 'confirmed')
            ->whereBetween('booking_date', [now(), now()->addDays(7)])
            ->with(['hall', 'user'])
            ->orderBy('booking_date')
            ->orderBy('time_slot')
            ->limit(5)
            ->get();

        return [
            'bookings' => $allBookings, // For count badge and isEmpty check
            'displayBookings' => $displayBookings, // If you want to show limited list
            'todayBookings' => $this->getTodayBookings($user),
            'tomorrowBookings' => $this->getTomorrowBookings($user),
        ];
    }

    /**
     * Get today's bookings
     */
    protected function getTodayBookings($user)
    {
        return Booking::whereHas('hall', function ($q) use ($user) {
            $q->where('owner_id', $user->id);
        })
            ->where('status', 'confirmed')
            ->whereDate('booking_date', today())
            ->with(['hall', 'user'])
            ->orderBy('time_slot')
            ->get();
    }

    /**
     * Get tomorrow's bookings
     */
    protected function getTomorrowBookings($user)
    {
        return Booking::whereHas('hall', function ($q) use ($user) {
            $q->where('owner_id', $user->id);
        })
            ->where('status', 'confirmed')
            ->whereDate('booking_date', Carbon::tomorrow())
            ->with(['hall', 'user'])
            ->orderBy('time_slot')
            ->get();
    }
}
