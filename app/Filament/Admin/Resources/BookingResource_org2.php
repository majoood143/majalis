<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Support\Facades\Storage;
use App\Models\HallAvailability;
use App\Models\Hall;
use Filament\Forms\Set;;

use Filament\Forms\Get;


class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Booking Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Booking Details')
                    ->schema([
                        Forms\Components\TextInput::make('booking_number')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('hall_id')
                            ->relationship('hall', 'name')
                            ->options(\App\Models\Hall::where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                if ($state && $get('booking_date') && $get('time_slot')) {
                                    static::updateHallPrice($set, $state, $get('booking_date'), $get('time_slot'));
                                }
                            }),
                        Forms\Components\Select::make('time_slot')
                            ->options([
                                'morning' => 'Morning (8 AM - 12 PM)',
                                'afternoon' => 'Afternoon (12 PM - 5 PM)',
                                'evening' => 'Evening (5 PM - 11 PM)',
                                'full_day' => 'Full Day (8 AM - 11 PM)',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                if ($state && $get('hall_id') && $get('booking_date')) {
                                    static::updateHallPrice($set, $get('hall_id'), $get('booking_date'), $state);
                                }
                            }),



                        Forms\Components\DatePicker::make('booking_date')
                            ->required()
                            ->native(false)
                            ->live()
                            ->minDate(now())
                            ->disabled(fn(Forms\Get $get) => !$get('hall_id') || !$get('time_slot'))
                            ->disabledDates(function (Forms\Get $get) {
                                $hallId = $get('hall_id');
                                $timeSlot = $get('time_slot');

                                if (!$hallId || !$timeSlot) {
                                    return [];
                                }

                                return self::getUnavailableDates($hallId, $timeSlot);
                            })
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                if ($state && $get('hall_id') && $get('time_slot')) {
                                    static::updateHallPrice($set, $get('hall_id'), $state, $get('time_slot'));
                                }
                            })
                            ->helperText(
                                fn(Forms\Get $get) =>
                                !$get('hall_id') ? 'Select a hall first' : (!$get('time_slot') ? 'Select a time slot first' : 'Unavailable dates are disabled')
                            ),






                        Forms\Components\TextInput::make('number_of_guests')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                    ])->columns(2),

                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('customer_email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('customer_phone')
                            ->tel()
                            ->required()
                            ->maxLength(20),

                        Forms\Components\Textarea::make('customer_notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('hall_price')
                            ->numeric()
                            ->prefix('OMR')
                            ->required()
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('services_price')
                            ->numeric()
                            ->prefix('OMR')
                            ->default(0),

                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->prefix('OMR')
                            ->required(),

                        Forms\Components\TextInput::make('commission_amount')
                            ->numeric()
                            ->prefix('OMR')
                            ->default(0),

                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->prefix('OMR')
                            ->required(),

                        Forms\Components\TextInput::make('owner_payout')
                            ->numeric()
                            ->prefix('OMR')
                            ->default(0),
                    ])->columns(3),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'cancelled' => 'Cancelled',
                                'completed' => 'Completed',
                                'refunded' => 'Refunded',
                            ])
                            ->required(),

                        Forms\Components\Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                                'partially_refunded' => 'Partially Refunded',
                            ])
                            ->required(),

                        Forms\Components\Textarea::make('admin_notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_number')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('hall.name')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->hall->name),

                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('booking_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('time_slot')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'morning' => 'info',
                        'afternoon' => 'warning',
                        'evening' => 'success',
                        'full_day' => 'primary',
                    }),

                Tables\Columns\TextColumn::make('total_amount')
                    ->money('OMR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hall_id')
                    ->relationship('hall', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                        'refunded' => 'Refunded',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),

                Tables\Filters\Filter::make('booking_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('booking_date', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('booking_date', '<=', $data['until']));
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('download_invoice')
                        ->label('Download Invoice')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function ($record) {
                            if (!$record->invoice_path) {
                                \Filament\Notifications\Notification::make()
                                    ->title('No invoice available')
                                    ->body('Please generate the invoice first.')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            if (!Storage::disk('local')->exists($record->invoice_path)) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Invoice not found')
                                    ->body('The invoice file may have been deleted.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            return response()->download(storage_path(
                                'app/private/' . $record->invoice_path,
                                'invoice-' . $record->booking_number . '.pdf'
                            ));
                        })
                        ->visible(fn($record) => !empty($record->invoice_path)),

                    Tables\Actions\Action::make('confirm')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn(Booking $record) => $record->confirm())
                        ->visible(fn(Booking $record) => $record->status->value === 'pending'),

                    Tables\Actions\Action::make('cancel')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label('Cancellation Reason')
                                ->required(),
                        ])
                        ->action(fn(Booking $record, array $data) => $record->cancel($data['reason']))
                        ->visible(fn(Booking $record) => in_array($record->status->value, ['pending', 'confirmed'])),

                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Booking Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('booking_number')
                            ->label('Booking Number')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('hall.name')
                            ->label('Hall'),

                        Infolists\Components\TextEntry::make('booking_date')
                            ->date(),

                        Infolists\Components\TextEntry::make('time_slot')
                            ->badge(),

                        Infolists\Components\TextEntry::make('number_of_guests')
                            ->suffix(' guests'),

                        Infolists\Components\TextEntry::make('status')
                            ->badge(),

                        Infolists\Components\TextEntry::make('payment_status')
                            ->badge(),
                    ])->columns(3),

                Infolists\Components\Section::make('Customer Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('customer_name'),
                        Infolists\Components\TextEntry::make('customer_email')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('customer_phone')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('customer_notes')
                            ->columnSpanFull(),
                    ])->columns(3),

                Infolists\Components\Section::make('Pricing Breakdown')
                    ->schema([
                        Infolists\Components\TextEntry::make('hall_price')
                            ->money('OMR'),
                        Infolists\Components\TextEntry::make('services_price')
                            ->money('OMR'),
                        Infolists\Components\TextEntry::make('subtotal')
                            ->money('OMR'),
                        Infolists\Components\TextEntry::make('commission_amount')
                            ->money('OMR'),
                        Infolists\Components\TextEntry::make('total_amount')
                            ->money('OMR')
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('owner_payout')
                            ->money('OMR'),
                    ])->columns(3),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    protected static function getUnavailableDates(int $hallId, ?string $timeSlot = null): array
    {
        $startDate = now();
        $endDate = now()->addMonths(6);
        $unavailableDates = [];

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateString = $currentDate->format('Y-m-d');

            // Check if this specific date and slot combination is available
            if ($timeSlot) {
                // Check specific slot availability
                $isAvailable = self::isSlotAvailable($hallId, $dateString, $timeSlot);

                if (!$isAvailable) {
                    $unavailableDates[] = $dateString;
                }
            } else {
                // No slot selected - check if the entire day is blocked
                $hasFullDayBooking = \App\Models\Booking::where('hall_id', $hallId)
                    ->whereDate('booking_date', $dateString)
                    ->where('time_slot', 'full_day')
                    ->whereIn('status', ['confirmed', 'pending'])
                    ->exists();

                if ($hasFullDayBooking) {
                    $unavailableDates[] = $dateString;
                }
            }

            $currentDate->addDay();
        }

        return $unavailableDates;
    }

    protected static function isSlotAvailable(int $hallId, string $date, string $timeSlot): bool
    {
        // Check HallAvailability table
        $availability = \App\Models\HallAvailability::where('hall_id', $hallId)
            ->whereDate('date', $date)
            ->where('time_slot', $timeSlot)
            ->where('is_available', true)
            ->exists();

        if (!$availability) {
            return false;
        }

        // Check if already booked
        $isBooked = \App\Models\Booking::where('hall_id', $hallId)
            ->whereDate('booking_date', $date)
            ->where(function ($q) use ($timeSlot) {
                $q->where('time_slot', $timeSlot)
                    ->orWhere('time_slot', 'full_day'); // Full day blocks all slots
            })
            ->whereIn('status', ['confirmed', 'pending'])
            ->exists();

        return !$isBooked;
    }

    // protected static function updateHallPrice(Forms\Set $set, Forms\Get $get): void
    // {
    //     $hallId = $get('hall_id');
    //     $timeSlot = $get('time_slot');

    //     if (!$hallId || !$timeSlot) {
    //         $set('hall_price', 0);
    //         return;
    //     }

    //     $hall = \App\Models\Hall::find($hallId);

    //     if (!$hall) {
    //         $set('hall_price', 0);
    //         return;
    //     }

    //     // Simple calculation: full_day = 3x the slot price
    //     $multiplier = match ($timeSlot) {
    //         'morning' => 1,
    //         'afternoon' => 1,
    //         'evening' => 1,
    //         'full_day' => 3,
    //         default => 1,
    //     };

    //     $price = $hall->price_per_slot * $multiplier;

    //     $set('hall_price', number_format($price, 3, '.', ''));
    // }

    protected static function updateHallPrice(Set $set, $hallId, $bookingDate, $timeSlot): void
    {
        if (!$hallId || !$bookingDate || !$timeSlot) {
            return;
        }

        // First, check if there's a custom price in HallAvailability
        $availability = HallAvailability::where('hall_id', $hallId)
            ->where('date', $bookingDate)
            ->where('time_slot', $timeSlot)
            ->first();

        if ($availability && $availability->custom_price !== null) {
            // Use custom price from HallAvailability
            $hallPrice = (float) $availability->custom_price;
        } else {
            // Use default Hall price for this time slot
            $hall = Hall::find($hallId);
            if ($hall) {
                $hallPrice = $hall->getPriceForSlot($timeSlot);
            } else {
                $hallPrice = 0;
            }
        }

        $set('hall_price', $hallPrice);

        // Recalculate totals
        static::calculateTotals($set, [
            'hall_price' => $hallPrice,
            'services_price' => 0,
            'commission_amount' => 0,
        ]);
    }

    /**
     * Calculate all totals
     */
    protected static function calculateTotals(Set $set, $get): void
    {
        $hallPrice = (float) ($get['hall_price'] ?? $get('hall_price') ?? 0);
        $servicesPrice = (float) ($get['services_price'] ?? $get('services_price') ?? 0);
        $commissionAmount = (float) ($get['commission_amount'] ?? $get('commission_amount') ?? 0);

        $subtotal = $hallPrice + $servicesPrice;
        $totalAmount = $subtotal + $commissionAmount;
        $ownerPayout = $subtotal - $commissionAmount;

        $set('subtotal', $subtotal);
        $set('total_amount', $totalAmount);
        $set('owner_payout', max(0, $ownerPayout));
    }

    /**
     * Get helper text for price field
     */
    protected static function getPriceHelperText($hallId, $bookingDate, $timeSlot): string
    {
        if (!$hallId || !$bookingDate || !$timeSlot) {
            return 'Select hall, date, and time slot to see pricing';
        }

        $availability = HallAvailability::where('hall_id', $hallId)
            ->where('date', $bookingDate)
            ->where('time_slot', $timeSlot)
            ->first();

        if ($availability && $availability->custom_price !== null) {
            return '✓ Custom price for this date/slot';
        }

        return 'Default hall price for ' . ucfirst(str_replace('_', ' ', $timeSlot));
    }
}
