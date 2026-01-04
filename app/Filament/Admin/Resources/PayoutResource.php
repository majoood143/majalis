<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\PayoutStatus;
use App\Filament\Admin\Resources\PayoutResource\Pages;
use App\Models\OwnerPayout;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * PayoutResource - Admin Payout Management
 *
 * Provides full CRUD operations for managing owner payouts including:
 * - Create payouts for specific periods
 * - Process, complete, fail, hold, and cancel payouts
 * - Track payout history and status changes
 * - Bulk processing capabilities
 *
 * @package App\Filament\Admin\Resources
 */
class PayoutResource extends Resource
{
    /**
     * The Eloquent model associated with this resource.
     *
     * @var string|null
     */
    protected static ?string $model = OwnerPayout::class;

    /**
     * The navigation icon for this resource.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    /**
     * The navigation group for this resource.
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = 'Finance';

    /**
     * The navigation sort order for this resource.
     *
     * @var int|null
     */
    protected static ?int $navigationSort = 20;

    /**
     * The singular label for this resource.
     *
     * @var string|null
     */
    protected static ?string $modelLabel = 'Payout';

    /**
     * The plural label for this resource.
     *
     * @var string|null
     */
    protected static ?string $pluralModelLabel = 'Payouts';

    /**
     * The record title attribute.
     *
     * @var string|null
     */
    protected static ?string $recordTitleAttribute = 'payout_number';

    /**
     * Get the navigation badge showing pending payouts count.
     *
     * @return string|null
     */
    public static function getNavigationBadge(): ?string
    {
        $pendingCount = static::getModel()::where('status', PayoutStatus::PENDING)->count();

        return $pendingCount > 0 ? (string) $pendingCount : null;
    }

    /**
     * Get the navigation badge color.
     *
     * @return string|null
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    /**
     * Define the form schema for creating/editing payouts.
     *
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Main Information Section
                Forms\Components\Section::make(__('admin.payout.sections.main'))
                    ->description(__('admin.payout.sections.main_desc'))
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                // Payout Number (auto-generated)
                                Forms\Components\TextInput::make('payout_number')
                                    ->label(__('admin.payout.fields.payout_number'))
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->placeholder(__('admin.payout.auto_generated'))
                                    ->helperText(__('admin.payout.auto_generated_help')),

                                // Owner Selection
                                Forms\Components\Select::make('owner_id')
                                    ->label(__('admin.payout.fields.owner'))
                                    ->relationship('owner', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->getOptionLabelFromRecordUsing(fn (User $record): string => 
                                        $record->name . ' (' . ($record->email) . ')'
                                    )
                                    ->options(function () {
                                        // Only show users who are hall owners
                                        return User::whereHas('hallOwner')
                                            ->pluck('name', 'id');
                                    }),
                            ]),

                        // Period Selection
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('period_start')
                                    ->label(__('admin.payout.fields.period_start'))
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->maxDate(now())
                                    ->live()
                                    ->afterStateUpdated(fn (Forms\Set $set, $state) => 
                                        $set('period_end', $state)
                                    ),

                                Forms\Components\DatePicker::make('period_end')
                                    ->label(__('admin.payout.fields.period_end'))
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->minDate(fn (Forms\Get $get) => $get('period_start'))
                                    ->maxDate(now()),
                            ]),

                        // Status
                        Forms\Components\Select::make('status')
                            ->label(__('admin.payout.fields.status'))
                            ->options(PayoutStatus::toSelectArray())
                            ->default(PayoutStatus::PENDING->value)
                            ->required()
                            ->native(false)
                            ->visible(fn (?OwnerPayout $record) => $record !== null),
                    ])
                    ->columns(1),

                // Financial Details Section
                Forms\Components\Section::make(__('admin.payout.sections.financial'))
                    ->description(__('admin.payout.sections.financial_desc'))
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('gross_revenue')
                                    ->label(__('admin.payout.fields.gross_revenue'))
                                    ->numeric()
                                    ->prefix('OMR')
                                    ->default(0)
                                    ->required()
                                    ->step(0.001)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => 
                                        self::calculateNetPayout($set, $get)
                                    ),

                                Forms\Components\TextInput::make('commission_amount')
                                    ->label(__('admin.payout.fields.commission'))
                                    ->numeric()
                                    ->prefix('OMR')
                                    ->default(0)
                                    ->required()
                                    ->step(0.001)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => 
                                        self::calculateNetPayout($set, $get)
                                    ),

                                Forms\Components\TextInput::make('commission_rate')
                                    ->label(__('admin.payout.fields.commission_rate'))
                                    ->numeric()
                                    ->suffix('%')
                                    ->default(0)
                                    ->step(0.01)
                                    ->maxValue(100),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('adjustments')
                                    ->label(__('admin.payout.fields.adjustments'))
                                    ->numeric()
                                    ->prefix('OMR')
                                    ->default(0)
                                    ->step(0.001)
                                    ->helperText(__('admin.payout.adjustments_help'))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => 
                                        self::calculateNetPayout($set, $get)
                                    ),

                                Forms\Components\TextInput::make('bookings_count')
                                    ->label(__('admin.payout.fields.bookings_count'))
                                    ->numeric()
                                    ->default(0)
                                    ->integer(),

                                Forms\Components\TextInput::make('net_payout')
                                    ->label(__('admin.payout.fields.net_payout'))
                                    ->numeric()
                                    ->prefix('OMR')
                                    ->default(0)
                                    ->required()
                                    ->step(0.001)
                                    ->readOnly()
                                    ->extraAttributes([
                                        'class' => 'font-bold text-green-600',
                                    ]),
                            ]),
                    ])
                    ->collapsible(),

                // Payment Details Section
                Forms\Components\Section::make(__('admin.payout.sections.payment'))
                    ->description(__('admin.payout.sections.payment_desc'))
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('payment_method')
                                    ->label(__('admin.payout.fields.payment_method'))
                                    ->options([
                                        'bank_transfer' => __('admin.payout.methods.bank_transfer'),
                                        'cash' => __('admin.payout.methods.cash'),
                                        'cheque' => __('admin.payout.methods.cheque'),
                                        'other' => __('admin.payout.methods.other'),
                                    ])
                                    ->native(false),

                                Forms\Components\TextInput::make('transaction_reference')
                                    ->label(__('admin.payout.fields.transaction_reference'))
                                    ->maxLength(100)
                                    ->placeholder(__('admin.payout.transaction_placeholder')),
                            ]),

                        // Bank Details (JSON)
                        Forms\Components\KeyValue::make('bank_details')
                            ->label(__('admin.payout.fields.bank_details'))
                            ->keyLabel(__('admin.payout.bank.field'))
                            ->valueLabel(__('admin.payout.bank.value'))
                            ->addActionLabel(__('admin.payout.bank.add'))
                            ->reorderable()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                // Notes Section
                Forms\Components\Section::make(__('admin.payout.sections.notes'))
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label(__('admin.payout.fields.notes'))
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('failure_reason')
                            ->label(__('admin.payout.fields.failure_reason'))
                            ->rows(2)
                            ->maxLength(500)
                            ->visible(fn (Forms\Get $get) => $get('status') === PayoutStatus::FAILED->value)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    /**
     * Calculate net payout from form values.
     *
     * @param Forms\Set $set
     * @param Forms\Get $get
     * @return void
     */
    protected static function calculateNetPayout(Forms\Set $set, Forms\Get $get): void
    {
        $gross = (float) ($get('gross_revenue') ?? 0);
        $commission = (float) ($get('commission_amount') ?? 0);
        $adjustments = (float) ($get('adjustments') ?? 0);

        $net = $gross - $commission + $adjustments;
        $set('net_payout', number_format(max(0, $net), 3, '.', ''));

        // Calculate commission rate if gross > 0
        if ($gross > 0) {
            $rate = ($commission / $gross) * 100;
            $set('commission_rate', number_format($rate, 2, '.', ''));
        }
    }

    /**
     * Define the table schema for listing payouts.
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Payout Number
                Tables\Columns\TextColumn::make('payout_number')
                    ->label(__('admin.payout.fields.payout_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Bold)
                    ->color('primary'),

                // Owner
                Tables\Columns\TextColumn::make('owner.name')
                    ->label(__('admin.payout.fields.owner'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (OwnerPayout $record): string => 
                        $record->owner?->email ?? ''
                    ),

                // Period
                Tables\Columns\TextColumn::make('period_start')
                    ->label(__('admin.payout.fields.period'))
                    ->formatStateUsing(fn (OwnerPayout $record): string => 
                        $record->period_start->format('d M') . ' - ' . $record->period_end->format('d M Y')
                    )
                    ->sortable(),

                // Bookings Count
                Tables\Columns\TextColumn::make('bookings_count')
                    ->label(__('admin.payout.fields.bookings'))
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                // Gross Revenue
                Tables\Columns\TextColumn::make('gross_revenue')
                    ->label(__('admin.payout.fields.gross'))
                    ->money('OMR')
                    ->sortable()
                    ->alignEnd(),

                // Commission
                Tables\Columns\TextColumn::make('commission_amount')
                    ->label(__('admin.payout.fields.commission'))
                    ->money('OMR')
                    ->sortable()
                    ->alignEnd()
                    ->color('danger'),

                // Net Payout
                Tables\Columns\TextColumn::make('net_payout')
                    ->label(__('admin.payout.fields.net'))
                    ->money('OMR')
                    ->sortable()
                    ->alignEnd()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                // Status
                Tables\Columns\TextColumn::make('status')
                    ->label(__('admin.payout.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (PayoutStatus $state): string => $state->getLabel())
                    ->color(fn (PayoutStatus $state): string => $state->getColor())
                    ->icon(fn (PayoutStatus $state): string => $state->getIcon()),

                // Processed Date
                Tables\Columns\TextColumn::make('completed_at')
                    ->label(__('admin.payout.fields.completed_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Created Date
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.payout.fields.created_at'))
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Status Filter
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('admin.payout.filters.status'))
                    ->options(PayoutStatus::toSelectArray())
                    ->multiple()
                    ->preload(),

                // Owner Filter
                Tables\Filters\SelectFilter::make('owner_id')
                    ->label(__('admin.payout.filters.owner'))
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->preload(),

                // Period Filter
                Tables\Filters\Filter::make('period')
                    ->label(__('admin.payout.filters.period'))
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(__('admin.payout.filters.from')),
                        Forms\Components\DatePicker::make('to')
                            ->label(__('admin.payout.filters.to')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q, $date) => 
                                $q->where('period_start', '>=', $date)
                            )
                            ->when($data['to'], fn ($q, $date) => 
                                $q->where('period_end', '<=', $date)
                            );
                    }),

                // Pending Payouts
                Tables\Filters\TernaryFilter::make('pending_only')
                    ->label(__('admin.payout.filters.pending_only'))
                    ->queries(
                        true: fn (Builder $query) => $query->where('status', PayoutStatus::PENDING),
                        false: fn (Builder $query) => $query->whereNot('status', PayoutStatus::PENDING),
                    ),
            ])
            ->actions([
                // View Action
                Tables\Actions\ViewAction::make()
                    ->iconButton(),

                // Edit Action
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->visible(fn (OwnerPayout $record): bool => 
                        !$record->status->isTerminal()
                    ),

                // Process Action
                Tables\Actions\Action::make('process')
                    ->label(__('admin.payout.actions.process'))
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading(__('admin.payout.modal.process_title'))
                    ->modalDescription(__('admin.payout.modal.process_desc'))
                    ->modalSubmitActionLabel(__('admin.payout.modal.process_confirm'))
                    ->visible(fn (OwnerPayout $record): bool => $record->canProcess())
                    ->action(function (OwnerPayout $record): void {
                        if ($record->markAsProcessing(Auth::id())) {
                            Notification::make()
                                ->title(__('admin.payout.notifications.processing'))
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title(__('admin.payout.notifications.process_failed'))
                                ->danger()
                                ->send();
                        }
                    }),

                // Complete Action
                Tables\Actions\Action::make('complete')
                    ->label(__('admin.payout.actions.complete'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('payment_method')
                            ->label(__('admin.payout.fields.payment_method'))
                            ->options([
                                'bank_transfer' => __('admin.payout.methods.bank_transfer'),
                                'cash' => __('admin.payout.methods.cash'),
                                'cheque' => __('admin.payout.methods.cheque'),
                                'other' => __('admin.payout.methods.other'),
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('transaction_reference')
                            ->label(__('admin.payout.fields.transaction_reference'))
                            ->required()
                            ->maxLength(100),
                    ])
                    ->visible(fn (OwnerPayout $record): bool => 
                        $record->status === PayoutStatus::PROCESSING
                    )
                    ->action(function (OwnerPayout $record, array $data): void {
                        if ($record->markAsCompleted(
                            $data['transaction_reference'],
                            $data['payment_method']
                        )) {
                            Notification::make()
                                ->title(__('admin.payout.notifications.completed'))
                                ->body(__('admin.payout.notifications.completed_body', [
                                    'amount' => number_format((float) $record->net_payout, 3),
                                    'owner' => $record->owner->name,
                                ]))
                                ->success()
                                ->send();
                        }
                    }),

                // Mark Failed Action
                Tables\Actions\Action::make('fail')
                    ->label(__('admin.payout.actions.fail'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('failure_reason')
                            ->label(__('admin.payout.fields.failure_reason'))
                            ->required()
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->visible(fn (OwnerPayout $record): bool => 
                        $record->status === PayoutStatus::PROCESSING
                    )
                    ->action(function (OwnerPayout $record, array $data): void {
                        if ($record->markAsFailed($data['failure_reason'])) {
                            Notification::make()
                                ->title(__('admin.payout.notifications.failed'))
                                ->warning()
                                ->send();
                        }
                    }),

                // Hold Action
                Tables\Actions\Action::make('hold')
                    ->label(__('admin.payout.actions.hold'))
                    ->icon('heroicon-o-pause-circle')
                    ->color('warning')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label(__('admin.payout.fields.hold_reason'))
                            ->rows(2)
                            ->maxLength(500),
                    ])
                    ->visible(fn (OwnerPayout $record): bool => $record->canCancel())
                    ->action(function (OwnerPayout $record, array $data): void {
                        if ($record->putOnHold($data['reason'] ?? null)) {
                            Notification::make()
                                ->title(__('admin.payout.notifications.on_hold'))
                                ->warning()
                                ->send();
                        }
                    }),

                // Cancel Action
                Tables\Actions\Action::make('cancel')
                    ->label(__('admin.payout.actions.cancel'))
                    ->icon('heroicon-o-no-symbol')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading(__('admin.payout.modal.cancel_title'))
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label(__('admin.payout.fields.cancel_reason'))
                            ->rows(2)
                            ->maxLength(500),
                    ])
                    ->visible(fn (OwnerPayout $record): bool => $record->canCancel())
                    ->action(function (OwnerPayout $record, array $data): void {
                        if ($record->cancel($data['reason'] ?? null)) {
                            Notification::make()
                                ->title(__('admin.payout.notifications.cancelled'))
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Bulk Process
                    Tables\Actions\BulkAction::make('bulk_process')
                        ->label(__('admin.payout.bulk.process'))
                        ->icon('heroicon-o-play')
                        ->color('info')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records): void {
                            $processed = 0;
                            foreach ($records as $record) {
                                if ($record->canProcess() && $record->markAsProcessing(Auth::id())) {
                                    $processed++;
                                }
                            }

                            Notification::make()
                                ->title(__('admin.payout.notifications.bulk_processed', [
                                    'count' => $processed,
                                ]))
                                ->success()
                                ->send();
                        }),

                    // Bulk Cancel
                    Tables\Actions\BulkAction::make('bulk_cancel')
                        ->label(__('admin.payout.bulk.cancel'))
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label(__('admin.payout.fields.cancel_reason'))
                                ->rows(2),
                        ])
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records, array $data): void {
                            $cancelled = 0;
                            foreach ($records as $record) {
                                if ($record->canCancel() && $record->cancel($data['reason'] ?? null)) {
                                    $cancelled++;
                                }
                            }

                            Notification::make()
                                ->title(__('admin.payout.notifications.bulk_cancelled', [
                                    'count' => $cancelled,
                                ]))
                                ->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->striped();
    }

    /**
     * Define the infolist schema for viewing payouts.
     *
     * @param Infolist $infolist
     * @return Infolist
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Header Section
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('payout_number')
                                    ->label(__('admin.payout.fields.payout_number'))
                                    ->weight(FontWeight::Bold)
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('status')
                                    ->label(__('admin.payout.fields.status'))
                                    ->badge()
                                    ->formatStateUsing(fn (PayoutStatus $state): string => $state->getLabel())
                                    ->color(fn (PayoutStatus $state): string => $state->getColor()),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('admin.payout.fields.created_at'))
                                    ->dateTime('d M Y H:i'),
                            ]),
                    ]),

                // Owner Information
                Infolists\Components\Section::make(__('admin.payout.sections.owner_info'))
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('owner.name')
                                    ->label(__('admin.payout.fields.owner_name')),

                                Infolists\Components\TextEntry::make('owner.email')
                                    ->label(__('admin.payout.fields.owner_email'))
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('hallOwner.business_name')
                                    ->label(__('admin.payout.fields.business_name'))
                                    ->placeholder('—'),

                                Infolists\Components\TextEntry::make('hallOwner.bank_name')
                                    ->label(__('admin.payout.fields.bank_name'))
                                    ->placeholder('—'),
                            ]),
                    ]),

                // Period & Financial
                Infolists\Components\Section::make(__('admin.payout.sections.financial'))
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('period_string')
                                    ->label(__('admin.payout.fields.period')),

                                Infolists\Components\TextEntry::make('bookings_count')
                                    ->label(__('admin.payout.fields.bookings_count'))
                                    ->badge()
                                    ->color('info'),
                            ]),

                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('gross_revenue')
                                    ->label(__('admin.payout.fields.gross_revenue'))
                                    ->money('OMR'),

                                Infolists\Components\TextEntry::make('commission_amount')
                                    ->label(__('admin.payout.fields.commission'))
                                    ->money('OMR')
                                    ->color('danger'),

                                Infolists\Components\TextEntry::make('adjustments')
                                    ->label(__('admin.payout.fields.adjustments'))
                                    ->money('OMR')
                                    ->color(fn ($state): string => 
                                        $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray')
                                    ),

                                Infolists\Components\TextEntry::make('net_payout')
                                    ->label(__('admin.payout.fields.net_payout'))
                                    ->money('OMR')
                                    ->weight(FontWeight::Bold)
                                    ->color('success')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                            ]),

                        Infolists\Components\TextEntry::make('commission_rate')
                            ->label(__('admin.payout.fields.commission_rate'))
                            ->formatStateUsing(fn ($state): string => 
                                number_format((float) $state, 2) . '%'
                            ),
                    ]),

                // Payment Details
                Infolists\Components\Section::make(__('admin.payout.sections.payment'))
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('payment_method')
                                    ->label(__('admin.payout.fields.payment_method'))
                                    ->badge()
                                    ->placeholder('—'),

                                Infolists\Components\TextEntry::make('transaction_reference')
                                    ->label(__('admin.payout.fields.transaction_reference'))
                                    ->copyable()
                                    ->placeholder('—'),
                            ]),

                        Infolists\Components\KeyValueEntry::make('bank_details')
                            ->label(__('admin.payout.fields.bank_details'))
                            ->visible(fn ($record) => !empty($record->bank_details)),
                    ])
                    ->collapsible(),

                // Timestamps
                Infolists\Components\Section::make(__('admin.payout.sections.timestamps'))
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('processed_at')
                                    ->label(__('admin.payout.fields.processed_at'))
                                    ->dateTime('d M Y H:i')
                                    ->placeholder('—'),

                                Infolists\Components\TextEntry::make('completed_at')
                                    ->label(__('admin.payout.fields.completed_at'))
                                    ->dateTime('d M Y H:i')
                                    ->placeholder('—'),

                                Infolists\Components\TextEntry::make('failed_at')
                                    ->label(__('admin.payout.fields.failed_at'))
                                    ->dateTime('d M Y H:i')
                                    ->placeholder('—'),
                            ]),

                        Infolists\Components\TextEntry::make('processor.name')
                            ->label(__('admin.payout.fields.processed_by'))
                            ->placeholder('—'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                // Notes & Failure Reason
                Infolists\Components\Section::make(__('admin.payout.sections.notes'))
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->label(__('admin.payout.fields.notes'))
                            ->placeholder(__('admin.payout.no_notes'))
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('failure_reason')
                            ->label(__('admin.payout.fields.failure_reason'))
                            ->color('danger')
                            ->visible(fn ($record) => $record->status === PayoutStatus::FAILED)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    /**
     * Get the relationships available for this resource.
     *
     * @return array
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Get the pages available for this resource.
     *
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayouts::route('/'),
            'create' => Pages\CreatePayout::route('/create'),
            'view' => Pages\ViewPayout::route('/{record}'),
            'edit' => Pages\EditPayout::route('/{record}/edit'),
        ];
    }

    /**
     * Get the global search results for this resource.
     *
     * @return array<string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['payout_number', 'owner.name', 'owner.email'];
    }

    /**
     * Get the global search result details.
     *
     * @param Model $record
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var OwnerPayout $record */
        return [
            'Owner' => $record->owner?->name ?? 'Unknown',
            'Amount' => number_format((float) $record->net_payout, 3) . ' OMR',
            'Status' => $record->status->getLabel(),
        ];
    }
}
