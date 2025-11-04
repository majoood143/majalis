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

                        Forms\Components\Select::make('hall_id')
                            ->relationship('hall', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('booking_date')
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('time_slot')
                            ->options([
                                'morning' => 'Morning (8 AM - 12 PM)',
                                'afternoon' => 'Afternoon (12 PM - 5 PM)',
                                'evening' => 'Evening (5 PM - 11 PM)',
                                'full_day' => 'Full Day (8 AM - 11 PM)',
                            ])
                            ->required(),

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
                            ->required(),

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

                    return response()->download(storage_path('app/private/' . $record->invoice_path,
                        'invoice-' . $record->booking_number . '.pdf'));
                        // return Storage::disk('local')->download(
                        //     $record->invoice_path,
                        //     'invoice-' . $record->booking_number . '.pdf'
                        // );
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
}
