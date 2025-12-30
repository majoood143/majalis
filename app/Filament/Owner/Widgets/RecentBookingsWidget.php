<?php

declare(strict_types=1);

namespace App\Filament\Owner\Widgets;

use App\Models\Booking;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class RecentBookingsWidget extends BaseWidget
{
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
     * Widget heading
     */
    public function getTableHeading(): ?string
    {
        return __('owner.widgets.recent_bookings');
    }

    /**
     * Configure the table
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()
                    ->whereHas('hall', function ($q) {
                        $q->where('owner_id', Auth::id());
                    })
                    ->with(['hall', 'user', 'payment']) // ✅ Fixed: using 'user' instead of 'customer'
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('booking_number')
                    ->label(__('owner.bookings.number'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage(__('owner.bookings.number_copied'))
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('hall.name')
                    ->label(__('owner.bookings.hall'))
                    //->getStateUsing(fn($record) => $record->hall->name[app()->getLocale()] ?? $record->hall->name['en'])
                    ->limit(20),
                    //->tooltip(fn($record) => $record->hall->name[app()->getLocale()] ?? $record->hall->name['en']),

                Tables\Columns\TextColumn::make('customer_name') // ✅ Fixed: using actual field name
                    ->label(__('owner.bookings.customer'))
                    ->getStateUsing(fn($record) => $record->user?->name ?? $record->customer_name)
                    ->searchable()
                    ->icon('heroicon-m-user')
                    ->iconColor(fn($record) => $record->user_id ? 'success' : 'warning'),

                Tables\Columns\TextColumn::make('booking_date') // ✅ Fixed: correct field name
                    ->label(__('owner.bookings.booking_date'))
                    ->date()
                    ->sortable()
                    ->icon('heroicon-m-calendar')
                    ->color(fn($record) => $record->booking_date->isPast() ? 'gray' : 'success'),

                Tables\Columns\TextColumn::make('time_slot')
                    ->label(__('owner.bookings.slot'))
                    ->badge()
                    ->formatStateUsing(fn($state) => __("owner.slots.{$state}")),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('owner.bookings.status'))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'info' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn($state) => __("owner.status.{$state}")),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('owner.bookings.amount'))
                    ->money('OMR')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                Tables\Columns\BadgeColumn::make('payment_status') // ✅ Fixed: using payment_status from booking
                    ->label(__('owner.bookings.payment'))
                    ->colors([
                        'success' => 'paid',
                        'warning' => ['pending', 'partial'],
                        'danger' => 'failed',
                        'info' => 'refunded',
                    ])
                    ->formatStateUsing(fn($state) => $state ? __("owner.payment.{$state}") : '-'),
            ])
            ->actions([
                Action::make('view')
                    ->label(__('owner.actions.view'))
                    ->icon('heroicon-m-eye')
                    ->url(fn($record) => route('filament.owner.resources.bookings.view', $record))
                    ->color('info'),

                Action::make('confirm')
                    ->label(__('owner.actions.confirm'))
                    ->icon('heroicon-m-check')
                    ->action(fn($record) => $this->confirmBooking($record))
                    ->visible(fn($record) => $record->status === 'pending')
                    ->color('success')
                    ->requiresConfirmation(),
            ])
            ->striped()
            ->paginated([5, 10])
            ->poll('30s')
            ->emptyStateHeading(__('owner.bookings.no_recent'))
            ->emptyStateDescription(__('owner.bookings.no_recent_description'))
            ->emptyStateIcon('heroicon-o-calendar');
    }

    /**
     * Confirm booking action
     */
    protected function confirmBooking($record): void
    {
        $record->confirm(); // Using your model's confirm() method

        // Send notification to customer
        // We'll implement this in Part 6

        Notification::make()
            ->title(__('owner.bookings.confirmed_success'))
            ->success()
            ->send();
    }
}
