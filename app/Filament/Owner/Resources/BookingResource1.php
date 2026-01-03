<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\BookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BookingResource1 extends OwnerResource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Bookings';

    protected static ?int $navigationSort = 1;

    /**
     * Apply owner scope to bookings
     */
    protected static function applyOwnerScope(Builder $query, $user): Builder
    {
        return $query->whereHas('hall', function ($q) use ($user) {
            $q->where('owner_id', $user->id);
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Booking Information')
                    ->schema([
                        Forms\Components\TextInput::make('booking_number')
                            ->label('Booking Number')
                            ->disabled(),

                        Forms\Components\Select::make('hall_id')
                            ->label('Hall')
                            ->relationship('hall', 'name')
                            ->disabled(),

                        Forms\Components\DatePicker::make('booking_date')
                            ->label('Event Date')
                            ->disabled(),

                        Forms\Components\Select::make('time_slot')
                            ->label('Time Slot')
                            ->options([
                                'morning' => 'Morning',
                                'afternoon' => 'Afternoon',
                                'evening' => 'Evening',
                                'full_day' => 'Full Day',
                            ])
                            ->disabled(),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Customer Name')
                            ->disabled(),

                        Forms\Components\TextInput::make('customer_email')
                            ->label('Customer Email')
                            ->disabled(),

                        Forms\Components\TextInput::make('customer_phone')
                            ->label('Customer Phone')
                            ->disabled(),

                        Forms\Components\TextInput::make('number_of_guests')
                            ->label('Number of Guests')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Payment Information')
                    ->schema([
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->prefix('OMR')
                            ->disabled(),

                        Forms\Components\Select::make('payment_status')
                            ->label('Payment Status')
                            ->options([
                                'pending' => 'Pending',
                                'partial' => 'Partial',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->disabled(),

                        Forms\Components\TextInput::make('advance_amount')
                            ->label('Advance Amount')
                            ->prefix('OMR')
                            ->visible(fn($record) => $record?->payment_type === 'advance')
                            ->disabled(),

                        Forms\Components\TextInput::make('balance_due')
                            ->label('Balance Due')
                            ->prefix('OMR')
                            ->visible(fn($record) => $record?->payment_type === 'advance')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_number')
                    ->label('Booking #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('hall.name')
                    ->label('Hall')
                    ->getStateUsing(
                        fn($record) =>
                        $record->hall->name[app()->getLocale()] ??
                            $record->hall->name['en'] ??
                            'N/A'
                    )
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->getStateUsing(
                        fn($record) =>
                        $record->user?->name ??
                            $record->customer_name ??
                            'Guest'
                    )
                    ->searchable(),

                Tables\Columns\TextColumn::make('booking_date')
                    ->label('Event Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('time_slot')
                    ->label('Time Slot')
                    ->badge()
                    ->formatStateUsing(fn($state) => ucfirst(str_replace('_', ' ', $state))),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'info' => 'completed',
                        'danger' => 'cancelled',
                    ]),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Amount')
                    ->money('OMR')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Payment')
                    ->colors([
                        'success' => 'paid',
                        'warning' => ['pending', 'partial'],
                        'danger' => 'failed',
                        'info' => 'refunded',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Booked On')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'partial' => 'Partial',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),

                Tables\Filters\Filter::make('upcoming')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('booking_date', '>=', now())
                    )
                    ->label('Upcoming Only'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => in_array($record->status, ['pending', 'confirmed'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // We'll add bulk actions later
                ]),
            ])
            ->defaultSort('booking_date', 'desc')
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [
            // We'll add relation managers in Part 4
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'view' => Pages\ViewBooking::route('/{record}'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }

    /**
     * Get navigation badge showing pending bookings count
     */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()
            ->whereHas('hall', function ($q) {
                $q->where('owner_id', Auth::id());
            })
            ->where('status', 'pending')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    /**
     * Get navigation badge color
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
