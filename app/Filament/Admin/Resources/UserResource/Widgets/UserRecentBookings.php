<?php

namespace App\Filament\Admin\Resources\UserResource\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use App\Filament\Admin\Resources\BookingResource;
use App\Models\Booking;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class UserRecentBookings extends BaseWidget
{
    public ?User $record = null;

    protected static ?string $heading = 'Recent Bookings';

    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Booking::query()
            ->where('user_id', $this->record?->id)
            ->latest('booking_date');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('booking_number')
                    ->label('Booking #')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->url(fn(Booking $record): string => BookingResource::getUrl('view', ['record' => $record])),

                TextColumn::make('hall.name')
                    ->label('Hall')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('booking_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('time_slot')
                    ->label('Time Slot')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('total_amount')
                    ->label('Amount')
                    ->money('OMR', 3)
                    ->sortable(),
            ])
            ->defaultSort('booking_date', 'desc')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->recordActions([
                Action::make('view')
                    ->url(fn(Booking $record): string => BookingResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-m-eye')
                    ->label('View'),
            ]);
    }
}
