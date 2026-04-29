<?php

namespace App\Filament\Admin\Resources\UserResource\Widgets;

use App\Models\Booking;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsOverview extends BaseWidget
{
    public ?User $record = null;

    protected function getStats(): array
    {
        $user = $this->record;

        if (!$user) {
            return [];
        }

        $totalBookings = $user->bookings()->count();

        $confirmedBookings = $user->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->count();

        $cancelledBookings = $user->bookings()
            ->where('status', 'cancelled')
            ->count();

        $pendingBookings = $user->bookings()
            ->where('status', 'pending')
            ->count();

        $totalSpent = $user->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $openTickets = Ticket::where('user_id', $user->id)
            ->whereNotIn('status', [
                TicketStatus::RESOLVED->value,
                TicketStatus::CLOSED->value,
                TicketStatus::CANCELLED->value,
            ])
            ->count();

        $totalTickets = Ticket::where('user_id', $user->id)->count();

        $lastBooking = $user->bookings()->latest('booking_date')->first();
        $lastBookingLabel = $lastBooking
            ? 'Last: ' . $lastBooking->booking_date->format('d M Y')
            : 'No bookings yet';

        return [
            Stat::make('Total Bookings', $totalBookings)
                ->description($lastBookingLabel)
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make('Confirmed / Completed', $confirmedBookings)
                ->description($pendingBookings . ' pending, ' . $cancelledBookings . ' cancelled')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total Spent', number_format((float) $totalSpent, 3) . ' OMR')
                ->description('From paid confirmed/completed bookings')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),

            Stat::make('Support Tickets', $totalTickets)
                ->description($openTickets . ' open ticket' . ($openTickets !== 1 ? 's' : ''))
                ->descriptionIcon('heroicon-m-ticket')
                ->color($openTickets > 0 ? 'danger' : 'gray'),
        ];
    }
}
