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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Booking Management';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('payment.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('payment.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('payment.navigation_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('payment.sections.payment_information'))
                    ->schema([
                        Forms\Components\TextInput::make('payment_reference')
                            ->label(__('payment.fields.payment_reference'))
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Select::make('booking_id')
                            ->relationship('booking', 'booking_number')
                            ->label(__('payment.fields.booking'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('transaction_id')
                            ->label(__('payment.fields.transaction_id'))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('amount')
                            ->label(__('payment.fields.amount'))
                            ->numeric()
                            ->required()
                            ->prefix('OMR')
                            ->step(0.001),

                        Forms\Components\TextInput::make('currency')
                            ->label(__('payment.fields.currency'))
                            ->default('OMR')
                            ->maxLength(3),

                        Forms\Components\Select::make('status')
                            ->label(__('payment.fields.status'))
                            ->options([
                                'pending' => __('payment.status.pending'),
                                'paid' => __('payment.status.paid'),
                                'failed' => __('payment.status.failed'),
                                'refunded' => __('payment.status.refunded'),
                                'partially_refunded' => __('payment.status.partially_refunded'),
                                'refund_in_progress' => __('payment.status.refund_in_progress'),
                                'retrying' => __('payment.status.retrying'),
                                'reconciliation_pending' => __('payment.status.reconciliation_pending'),
                                'processing' => __('payment.status.processing'),
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('payment_method')
                            ->label(__('payment.fields.payment_method'))
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make(__('payment.sections.refund_information'))
                    ->schema([
                        Forms\Components\TextInput::make('refund_amount')
                            ->label(__('payment.fields.refund_amount'))
                            ->numeric()
                            ->prefix('OMR')
                            ->step(0.001),

                        Forms\Components\Textarea::make('refund_reason')
                            ->label(__('payment.fields.refund_reason'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make(__('payment.sections.failure_information'))
                    ->schema([
                        Forms\Components\Textarea::make('failure_reason')
                            ->label(__('payment.fields.failure_reason'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make(__('payment.sections.gateway_response'))
                    ->schema([
                        Forms\Components\KeyValue::make('gateway_response')
                            ->label(__('payment.fields.gateway_response'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with('booking'))
            ->columns([
                Tables\Columns\TextColumn::make('payment_reference')
                    ->label(__('payment.columns.payment_reference'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('booking.booking_number')
                    ->label(__('payment.columns.booking_number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('transaction_id')
                    ->label(__('payment.columns.transaction_id'))
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('payment.columns.amount'))
                    ->money('OMR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('payment.columns.status'))
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn($state): string => match ($state) {
                        'paid' => __('payment.status.paid'),
                        'pending' => __('payment.status.pending'),
                        'failed' => __('payment.status.failed'),
                        'refunded' => __('payment.status.refunded'),
                        'partially_refunded' => __('payment.status.partially_refunded'),
                        'refund_in_progress' => __('payment.status.refund_in_progress'),
                        'retrying' => __('payment.status.retrying'),
                        'reconciliation_pending' => __('payment.status.reconciliation_pending'),
                        'processing' => __('payment.status.processing'),
                        'canceled' => __('payment.status.canceled'),
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'info',
                        'partially_refunded' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label(__('payment.columns.payment_method'))
                    ->badge()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label(__('payment.columns.paid_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('payment.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('payment.filters.status'))
                    ->options([
                        'pending' => __('payment.status.pending'),
                        'paid' => __('payment.status.paid'),
                        'failed' => __('payment.status.failed'),
                        'refunded' => __('payment.status.refunded'),
                        'partially_refunded' => __('payment.status.partially_refunded'),
                    ]),

                Tables\Filters\Filter::make('paid_at')
                    ->label(__('payment.filters.paid_at'))
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(__('payment.fields.from_date')),
                        Forms\Components\DatePicker::make('until')
                            ->label(__('payment.fields.to_date')),
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
                    ->label(__('payment.actions.refund'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn(Payment $record): bool => $record->canBeRefunded())
                    ->form(function (Payment $record): array {
                        $remainingAmount = $record->getRemainingRefundableAmount();

                        return [
                            Forms\Components\Section::make(__('payment.sections.refund_details'))
                                ->description(__('payment.descriptions.refund_process'))
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Placeholder::make('payment_reference')
                                                ->label(__('payment.fields.payment_reference'))
                                                ->content($record->payment_reference),

                                            Forms\Components\Placeholder::make('booking_number')
                                                ->label(__('payment.fields.booking'))
                                                ->content($record->booking?->booking_number ?? __('payment.n_a')),

                                            Forms\Components\Placeholder::make('original_amount')
                                                ->label(__('payment.placeholders.original_amount'))
                                                ->content(number_format($record->amount, 3) . ' OMR'),

                                            Forms\Components\Placeholder::make('already_refunded')
                                                ->label(__('payment.placeholders.already_refunded'))
                                                ->content(number_format($record->refund_amount ?? 0, 3) . ' OMR'),

                                            Forms\Components\Placeholder::make('refundable_amount')
                                                ->label(__('payment.placeholders.refundable_amount'))
                                                ->content(fn() => number_format($remainingAmount, 3) . ' OMR')
                                                ->columnSpanFull()
                                                ->extraAttributes(['class' => 'text-lg font-bold text-green-600']),
                                        ]),

                                    Forms\Components\Radio::make('refund_type')
                                        ->label(__('payment.fields.refund_type'))
                                        ->options([
                                            'full' => __('payment.options.full_refund', ['amount' => number_format($remainingAmount, 3)]),
                                            'partial' => __('payment.options.partial_refund'),
                                        ])
                                        ->default('full')
                                        ->required()
                                        ->live()
                                        ->columnSpanFull(),

                                    Forms\Components\TextInput::make('amount')
                                        ->label(__('payment.fields.refund_amount_input'))
                                        ->numeric()
                                        ->required()
                                        ->prefix('OMR')
                                        ->step(0.001)
                                        ->minValue(0.001)
                                        ->maxValue($remainingAmount)
                                        ->default($remainingAmount)
                                        ->helperText(__('payment.helpers.max_refund', ['amount' => number_format($remainingAmount, 3)]))
                                        ->visible(fn($get) => $get('refund_type') === 'partial'),

                                    Forms\Components\Select::make('reason')
                                        ->label(__('payment.fields.refund_reason_select'))
                                        ->options([
                                            'Customer Request' => __('payment.refund_reasons.customer_request'),
                                            'Event Cancelled' => __('payment.refund_reasons.event_cancelled'),
                                            'Hall Unavailable' => __('payment.refund_reasons.hall_unavailable'),
                                            'Duplicate Payment' => __('payment.refund_reasons.duplicate_payment'),
                                            'Service Not Provided' => __('payment.refund_reasons.service_not_provided'),
                                            'Quality Issues' => __('payment.refund_reasons.quality_issues'),
                                            'Other' => __('payment.refund_reasons.other'),
                                        ])
                                        ->required()
                                        ->searchable()
                                        ->columnSpanFull(),

                                    Forms\Components\Textarea::make('notes')
                                        ->label(__('payment.fields.additional_notes'))
                                        ->rows(3)
                                        ->placeholder(__('payment.placeholders.additional_notes'))
                                        ->columnSpanFull(),

                                    Forms\Components\Checkbox::make('notify_customer')
                                        ->label(__('payment.fields.notify_customer'))
                                        ->default(true)
                                        ->helperText(__('payment.helpers.notify_customer')),
                                ])
                        ];
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('payment.modals.refund.heading'))
                    ->modalDescription(__('payment.modals.refund.description'))
                    ->modalSubmitActionLabel(__('payment.actions.process_refund'))
                    ->modalWidth('2xl')
                    ->action(function (Payment $record, array $data): void {
                        try {
                            // Determine refund amount
                            $amount = $data['refund_type'] === 'full'
                                ? $record->getRemainingRefundableAmount()
                                : (float) $data['amount'];

                            // Build refund reason
                            $reason = $data['reason'];
                            if (!empty($data['notes'])) {
                                $reason .= ' - ' . $data['notes'];
                            }
                            $reason .= ' | ' . __('payment.processed_by') . ': ' . (Auth::user()?->name ?? 'System');

                            // Process refund
                            $success = $record->refund($amount, $reason);

                            if ($success) {
                                // Send notification if requested
                                if ($data['notify_customer'] ?? false) {
                                    try {
                                        Log::info('Refund notification requested', [
                                            'payment_id' => $record->id,
                                            'amount' => $amount,
                                            'customer_email' => $record->booking?->customer_email,
                                        ]);
                                    } catch (\Exception $e) {
                                        Log::warning('Failed to send refund notification', [
                                            'payment_id' => $record->id,
                                            'error' => $e->getMessage(),
                                        ]);
                                    }
                                }

                                \Filament\Notifications\Notification::make()
                                    ->title(__('payment.notifications.refund_success'))
                                    ->success()
                                    ->body(__('payment.notifications.refund_success_body', ['amount' => number_format($amount, 3)]))
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title(__('payment.notifications.refund_failed'))
                                ->danger()
                                ->body(__('payment.notifications.refund_failed_body', ['error' => $e->getMessage()]))
                                ->persistent()
                                ->send();

                            throw $e;
                        }
                    }),
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
                Infolists\Components\Section::make(__('payment.sections.payment_information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_reference')
                            ->label(__('payment.fields.payment_reference'))
                            ->copyable(),
                        Infolists\Components\TextEntry::make('booking.booking_number')
                            ->label(__('payment.fields.booking')),
                        Infolists\Components\TextEntry::make('transaction_id')
                            ->label(__('payment.fields.transaction_id'))
                            ->copyable(),
                        Infolists\Components\TextEntry::make('amount')
                            ->label(__('payment.fields.amount'))
                            ->money('OMR'),
                        Infolists\Components\TextEntry::make('status')
                            ->label(__('payment.fields.status'))
                            ->badge()
                            ->formatStateUsing(fn($state): string => match ($state) {
                                'paid' => __('payment.status.paid'),
                                'pending' => __('payment.status.pending'),
                                'failed' => __('payment.status.failed'),
                                'refunded' => __('payment.status.refunded'),
                                'partially_refunded' => __('payment.status.partially_refunded'),
                                default => $state,
                            })
                            ->color(fn(string $state): string => match ($state) {
                                'paid' => 'success',
                                'pending' => 'warning',
                                'failed' => 'danger',
                                'refunded' => 'info',
                                'partially_refunded' => 'info',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('payment_method')
                            ->label(__('payment.fields.payment_method'))
                            ->badge(),
                    ])->columns(3),

                Infolists\Components\Section::make(__('payment.sections.timestamps'))
                    ->schema([
                        Infolists\Components\TextEntry::make('paid_at')
                            ->label(__('payment.fields.paid_at'))
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('failed_at')
                            ->label(__('payment.fields.failed_at'))
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('refunded_at')
                            ->label(__('payment.fields.refunded_at'))
                            ->dateTime(),
                    ])->columns(3),

                Infolists\Components\Section::make(__('payment.sections.refund_details'))
                    ->schema([
                        Infolists\Components\TextEntry::make('refund_amount')
                            ->label(__('payment.fields.refund_amount'))
                            ->money('OMR'),
                        Infolists\Components\TextEntry::make('refund_reason')
                            ->label(__('payment.fields.refund_reason')),
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
