<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Booking Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Information')
                    ->schema([
                        Forms\Components\TextInput::make('payment_reference')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Select::make('booking_id')
                            ->relationship('booking', 'booking_number')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('transaction_id')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->prefix('OMR')
                            ->step(0.001),

                        Forms\Components\TextInput::make('currency')
                            ->default('OMR')
                            ->maxLength(3),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                                'partially_refunded' => 'Partially Refunded',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('payment_method')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Refund Information')
                    ->schema([
                        Forms\Components\TextInput::make('refund_amount')
                            ->numeric()
                            ->prefix('OMR')
                            ->step(0.001),

                        Forms\Components\Textarea::make('refund_reason')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Failure Information')
                    ->schema([
                        Forms\Components\Textarea::make('failure_reason')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Gateway Response')
                    ->schema([
                        Forms\Components\KeyValue::make('gateway_response')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_reference')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('booking.booking_number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('transaction_id')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('amount')
                    ->money('OMR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                        'partially_refunded' => 'Partially Refunded',
                    ]),

                Tables\Filters\Filter::make('paid_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('paid_at', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('paid_at', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('refund')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Refund Amount')
                            ->numeric()
                            ->required()
                            ->prefix('OMR'),
                        Forms\Components\Textarea::make('reason')
                            ->label('Refund Reason')
                            ->required(),
                    ])
                    ->action(fn(Payment $record, array $data) => $record->refund($data['amount'], $data['reason']))
                    ->visible(fn(Payment $record) => $record->canBeRefunded()),
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
                Infolists\Components\Section::make('Payment Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_reference')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('booking.booking_number')
                            ->label('Booking'),
                        Infolists\Components\TextEntry::make('transaction_id')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('amount')
                            ->money('OMR'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge(),
                        Infolists\Components\TextEntry::make('payment_method')
                            ->badge(),
                    ])->columns(3),

                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\TextEntry::make('paid_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('failed_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('refunded_at')
                            ->dateTime(),
                    ])->columns(3),

                Infolists\Components\Section::make('Refund Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('refund_amount')
                            ->money('OMR'),
                        Infolists\Components\TextEntry::make('refund_reason'),
                    ])
                    ->visible(fn($record) => $record->isRefunded()),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'failed')->count();
    }
}
