<?php

namespace App\Filament\Admin\Resources\HallOwnerResource\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use App\Filament\Admin\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Hall;
use App\Models\HallOwner;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class HallOwnerRecentBookings extends BaseWidget
{
    public ?HallOwner $record = null;

    protected static ?string $heading = null;

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return __('hall-owner.widgets.recent_bookings');
    }

    protected function getTableQuery(): Builder
    {
        $hallIds = Hall::where('owner_id', $this->record?->user_id)->pluck('id');

        return Booking::query()
            ->whereIn('hall_id', $hallIds)
            ->latest('booking_date');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('booking_number')
                    ->label(__('hall-owner.widgets.columns.booking_number'))
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->url(fn(Booking $record): string => BookingResource::getUrl('view', ['record' => $record])),

                TextColumn::make('hall.name')
                    ->label(__('hall-owner.widgets.columns.hall'))
                    ->searchable()
                    ->limit(25),

                TextColumn::make('user.name')
                    ->label(__('hall-owner.widgets.columns.customer'))
                    ->searchable()
                    ->limit(25),

                TextColumn::make('booking_date')
                    ->label(__('hall-owner.widgets.columns.date'))
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('hall-owner.widgets.columns.status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('payment_status')
                    ->label(__('hall-owner.widgets.columns.payment'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('total_amount')
                    ->label(__('hall-owner.widgets.columns.income'))
                    ->money('OMR', 3)
                    ->sortable(),

                TextColumn::make('commission_amount')
                    ->label(__('hall-owner.widgets.columns.commission'))
                    ->money('OMR', 3)
                    ->sortable(),

                TextColumn::make('owner_payout')
                    ->label(__('hall-owner.widgets.columns.payout'))
                    ->money('OMR', 3)
                    ->sortable()
                    ->color('success'),
            ])
            ->defaultSort('booking_date', 'desc')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->recordActions([
                Action::make('view')
                    ->url(fn(Booking $record): string => BookingResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-m-eye')
                    ->label(__('hall-owner.widgets.actions.view')),
            ]);
    }
}
