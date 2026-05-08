<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\TextSize;
use Filament\Infolists\Components\KeyValueEntry;
use App\Filament\Admin\Resources\PayoutResource\Pages\ListPayouts;
use App\Filament\Admin\Resources\PayoutResource\Pages\CreatePayout;
use App\Filament\Admin\Resources\PayoutResource\Pages\ViewPayout;
use App\Filament\Admin\Resources\PayoutResource\Pages\EditPayout;
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
use Filament\Tables\Actions\ActionGroup;
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
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    /**
     * The navigation group for this resource.
     *
     * @var string|null
     */
    protected static string | \UnitEnum | null $navigationGroup = 'Finance';

    public static function getModelLabel(): string
    {
        return __('admin.payout.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.payout.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.payout.navigation_label');
    }

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
     * @param Schema $schema
     * @return Schema
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Main Information Section
                Section::make(__('admin.payout.sections.main'))
                    ->description(__('admin.payout.sections.main_desc'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Payout Number (auto-generated)
                                TextInput::make('payout_number')
                                    ->label(__('admin.payout.fields.payout_number'))
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->placeholder(__('admin.payout.auto_generated'))
                                    ->helperText(__('admin.payout.auto_generated_help')),

                                // Owner Selection
                                Select::make('owner_id')
                                    ->label(__('admin.payout.fields.owner'))
                                    ->relationship('owner', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->getOptionLabelFromRecordUsing(
                                        fn(User $record): string =>
                                        $record->name . ' (' . ($record->email) . ')'
                                    )
                                    ->options(function () {
                                        // Only show users who are hall owners
                                        return User::whereHas('hallOwner')
                                            ->pluck('name', 'id');
                                    }),
                            ]),

                        // Period Selection
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('period_start')
                                    ->label(__('admin.payout.fields.period_start'))
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->maxDate(now())
                                    ->live()
                                    ->afterStateUpdated(
                                        fn(Set $set, $state) =>
                                        $set('period_end', $state)
                                    ),

                                DatePicker::make('period_end')
                                    ->label(__('admin.payout.fields.period_end'))
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->minDate(fn(Get $get) => $get('period_start'))
                                    ->maxDate(now()),
                            ]),

                        // Status
                        Select::make('status')
                            ->label(__('admin.payout.fields.status'))
                            ->options(PayoutStatus::toSelectArray())
                            ->default(PayoutStatus::PENDING->value)
                            ->required()
                            ->native(false)
                            ->visible(fn(?OwnerPayout $record) => $record !== null),
                    ])
                    ->columns(1),

                // Financial Details Section
                Section::make(__('admin.payout.sections.financial'))
                    ->description(__('admin.payout.sections.financial_desc'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('gross_revenue')
                                    ->label(__('admin.payout.fields.gross_revenue'))
                                    ->numeric()
                                    ->prefix('OMR')
                                    ->default(0)
                                    ->required()
                                    ->step(0.001)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(
                                        fn(Set $set, Get $get) =>
                                        self::calculateNetPayout($set, $get)
                                    ),

                                TextInput::make('commission_amount')
                                    ->label(__('admin.payout.fields.commission'))
                                    ->numeric()
                                    ->prefix('OMR')
                                    ->default(0)
                                    ->required()
                                    ->step(0.001)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(
                                        fn(Set $set, Get $get) =>
                                        self::calculateNetPayout($set, $get)
                                    ),

                                TextInput::make('commission_rate')
                                    ->label(__('admin.payout.fields.commission_rate'))
                                    ->numeric()
                                    ->suffix('%')
                                    ->default(0)
                                    ->step(0.01)
                                    ->maxValue(100),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('adjustments')
                                    ->label(__('admin.payout.fields.adjustments'))
                                    ->numeric()
                                    ->prefix('OMR')
                                    ->default(0)
                                    ->step(0.001)
                                    ->helperText(__('admin.payout.adjustments_help'))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(
                                        fn(Set $set, Get $get) =>
                                        self::calculateNetPayout($set, $get)
                                    ),

                                TextInput::make('bookings_count')
                                    ->label(__('admin.payout.fields.bookings_count'))
                                    ->numeric()
                                    ->default(0)
                                    ->integer(),

                                TextInput::make('net_payout')
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
                Section::make(__('admin.payout.sections.payment'))
                    ->description(__('admin.payout.sections.payment_desc'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('payment_method')
                                    ->label(__('admin.payout.fields.payment_method'))
                                    ->options([
                                        'bank_transfer' => __('admin.payout.methods.bank_transfer'),
                                        'cash' => __('admin.payout.methods.cash'),
                                        'cheque' => __('admin.payout.methods.cheque'),
                                        'other' => __('admin.payout.methods.other'),
                                    ])
                                    ->native(false),

                                TextInput::make('transaction_reference')
                                    ->label(__('admin.payout.fields.transaction_reference'))
                                    ->maxLength(100)
                                    ->placeholder(__('admin.payout.transaction_placeholder')),
                            ]),

                        // Bank Details (JSON)
                        KeyValue::make('bank_details')
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
                Section::make(__('admin.payout.sections.notes'))
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('admin.payout.fields.notes'))
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Textarea::make('failure_reason')
                            ->label(__('admin.payout.fields.failure_reason'))
                            ->rows(2)
                            ->maxLength(500)
                            ->visible(fn(Get $get) => $get('status') === PayoutStatus::FAILED->value)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ])
            ->columns(1);
    }

    /**
     * Calculate net payout from form values.
     *
     * @param Set $set
     * @param Get $get
     * @return void
     */
    protected static function calculateNetPayout(Set $set, Get $get): void
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
                TextColumn::make('payout_number')
                    ->label(__('admin.payout.fields.payout_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Bold)
                    ->color('primary'),

                // Owner
                TextColumn::make('owner.name')
                    ->label(__('admin.payout.fields.owner'))
                    ->searchable()
                    ->sortable()
                    ->description(
                        fn(OwnerPayout $record): string =>
                        $record->owner?->email ?? ''
                    ),

                // Period
                TextColumn::make('period_start')
                    ->label(__('admin.payout.fields.period'))
                    ->formatStateUsing(
                        fn(OwnerPayout $record): string =>
                        $record->period_start->format('d M') . ' - ' . $record->period_end->format('d M Y')
                    )
                    ->sortable(),

                // Bookings Count
                TextColumn::make('bookings_count')
                    ->label(__('admin.payout.fields.bookings'))
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                // Gross Revenue
                TextColumn::make('gross_revenue')
                    ->label(__('admin.payout.fields.gross'))
                    ->money('OMR')
                    ->sortable()
                    ->alignEnd(),

                // Commission
                TextColumn::make('commission_amount')
                    ->label(__('admin.payout.fields.commission'))
                    ->money('OMR')
                    ->sortable()
                    ->alignEnd()
                    ->color('danger'),

                // Net Payout
                TextColumn::make('net_payout')
                    ->label(__('admin.payout.fields.net'))
                    ->money('OMR')
                    ->sortable()
                    ->alignEnd()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                // Status
                TextColumn::make('status')
                    ->label(__('admin.payout.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn(PayoutStatus $state): string => $state->getLabel())
                    ->color(fn(PayoutStatus $state): string => $state->getColor())
                    ->icon(fn(PayoutStatus $state): string => $state->getIcon()),

                // Processed Date
                TextColumn::make('completed_at')
                    ->label(__('admin.payout.fields.completed_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Created Date
                TextColumn::make('created_at')
                    ->label(__('admin.payout.fields.created_at'))
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Status Filter
                SelectFilter::make('status')
                    ->label(__('admin.payout.filters.status'))
                    ->options(PayoutStatus::toSelectArray())
                    ->multiple()
                    ->preload(),

                // Owner Filter
                SelectFilter::make('owner_id')
                    ->label(__('admin.payout.filters.owner'))
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->preload(),

                // Period Filter
                Filter::make('period')
                    ->label(__('admin.payout.filters.period'))
                    ->schema([
                        DatePicker::make('from')
                            ->label(__('admin.payout.filters.from')),
                        DatePicker::make('to')
                            ->label(__('admin.payout.filters.to')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn($q, $date) =>
                                $q->where('period_start', '>=', $date)
                            )
                            ->when(
                                $data['to'],
                                fn($q, $date) =>
                                $q->where('period_end', '<=', $date)
                            );
                    }),

                // Pending Payouts
                TernaryFilter::make('pending_only')
                    ->label(__('admin.payout.filters.pending_only'))
                    ->queries(
                        true: fn(Builder $query) => $query->where('status', PayoutStatus::PENDING),
                        false: fn(Builder $query) => $query->whereNot('status', PayoutStatus::PENDING),
                    ),
            ])
            ->recordActions([

                // View Action
                ViewAction::make()
                    ->iconButton(),

                // Edit Action
                EditAction::make()
                    ->iconButton()
                    ->visible(
                        fn(OwnerPayout $record): bool =>
                        !$record->status->isTerminal()
                    ),

                \Filament\Actions\ActionGroup::make([





                    // Process Action
                    Action::make('process')
                        ->label(__('admin.payout.actions.process'))
                        ->icon('heroicon-o-play')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading(__('admin.payout.modal.process_title'))
                        ->modalDescription(__('admin.payout.modal.process_desc'))
                        ->modalSubmitActionLabel(__('admin.payout.modal.process_confirm'))
                        ->visible(fn(OwnerPayout $record): bool => $record->canProcess())
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
                    Action::make('complete')
                        ->label(__('admin.payout.actions.complete'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->schema([
                            Select::make('payment_method')
                                ->label(__('admin.payout.fields.payment_method'))
                                ->options([
                                    'bank_transfer' => __('admin.payout.methods.bank_transfer'),
                                    'cash' => __('admin.payout.methods.cash'),
                                    'cheque' => __('admin.payout.methods.cheque'),
                                    'other' => __('admin.payout.methods.other'),
                                ])
                                ->required()
                                ->native(false),

                            TextInput::make('transaction_reference')
                                ->label(__('admin.payout.fields.transaction_reference'))
                                ->required()
                                ->maxLength(100),
                        ])
                        ->visible(
                            fn(OwnerPayout $record): bool =>
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
                    Action::make('fail')
                        ->label(__('admin.payout.actions.fail'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->schema([
                            Textarea::make('failure_reason')
                                ->label(__('admin.payout.fields.failure_reason'))
                                ->required()
                                ->rows(3)
                                ->maxLength(500),
                        ])
                        ->visible(
                            fn(OwnerPayout $record): bool =>
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
                    Action::make('hold')
                        ->label(__('admin.payout.actions.hold'))
                        ->icon('heroicon-o-pause-circle')
                        ->color('warning')
                        ->schema([
                            Textarea::make('reason')
                                ->label(__('admin.payout.fields.hold_reason'))
                                ->rows(2)
                                ->maxLength(500),
                        ])
                        ->visible(fn(OwnerPayout $record): bool => $record->canCancel())
                        ->action(function (OwnerPayout $record, array $data): void {
                            if ($record->putOnHold($data['reason'] ?? null)) {
                                Notification::make()
                                    ->title(__('admin.payout.notifications.on_hold'))
                                    ->warning()
                                    ->send();
                            }
                        }),

                    // Cancel Action
                    Action::make('cancel')
                        ->label(__('admin.payout.actions.cancel'))
                        ->icon('heroicon-o-no-symbol')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading(__('admin.payout.modal.cancel_title'))
                        ->schema([
                            Textarea::make('reason')
                                ->label(__('admin.payout.fields.cancel_reason'))
                                ->rows(2)
                                ->maxLength(500),
                        ])
                        ->visible(fn(OwnerPayout $record): bool => $record->canCancel())
                        ->action(function (OwnerPayout $record, array $data): void {
                            if ($record->cancel($data['reason'] ?? null)) {
                                Notification::make()
                                    ->title(__('admin.payout.notifications.cancelled'))
                                    ->send();
                            }
                        }),
                    // TODO: ActivityLogTimelineTableAction removed (rmsramos v3-only) - replace with v4 equivalent,
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Bulk Process
                    BulkAction::make('bulk_process')
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
                    BulkAction::make('bulk_cancel')
                        ->label(__('admin.payout.bulk.cancel'))
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->schema([
                            Textarea::make('reason')
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

                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->striped();
    }

    /**
     * Define the infolist schema for viewing payouts.
     *
     * @param Schema $schema
     * @return Schema
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Header Section
                Section::make()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('payout_number')
                                    ->label(__('admin.payout.fields.payout_number'))
                                    ->weight(FontWeight::Bold)
                                    ->size(TextSize::Large)
                                    ->copyable(),

                                TextEntry::make('status')
                                    ->label(__('admin.payout.fields.status'))
                                    ->badge()
                                    ->formatStateUsing(fn(PayoutStatus $state): string => $state->getLabel())
                                    ->color(fn(PayoutStatus $state): string => $state->getColor()),

                                TextEntry::make('created_at')
                                    ->label(__('admin.payout.fields.created_at'))
                                    ->dateTime('d M Y H:i'),
                            ]),
                    ]),

                // Owner Information
                Section::make(__('admin.payout.sections.owner_info'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('owner.name')
                                    ->label(__('admin.payout.fields.owner_name')),

                                TextEntry::make('owner.email')
                                    ->label(__('admin.payout.fields.owner_email'))
                                    ->copyable(),

                                TextEntry::make('hallOwner.business_name')
                                    ->label(__('admin.payout.fields.business_name'))
                                    ->placeholder('—'),

                                TextEntry::make('hallOwner.bank_name')
                                    ->label(__('admin.payout.fields.bank_name'))
                                    ->placeholder('—'),
                            ]),
                    ]),

                // Period & Financial
                Section::make(__('admin.payout.sections.financial'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('period_string')
                                    ->label(__('admin.payout.fields.period')),

                                TextEntry::make('bookings_count')
                                    ->label(__('admin.payout.fields.bookings_count'))
                                    ->badge()
                                    ->color('info'),
                            ]),

                        Grid::make(4)
                            ->schema([
                                TextEntry::make('gross_revenue')
                                    ->label(__('admin.payout.fields.gross_revenue'))
                                    ->money('OMR'),

                                TextEntry::make('commission_amount')
                                    ->label(__('admin.payout.fields.commission'))
                                    ->money('OMR')
                                    ->color('danger'),

                                TextEntry::make('adjustments')
                                    ->label(__('admin.payout.fields.adjustments'))
                                    ->money('OMR')
                                    ->color(
                                        fn($state): string =>
                                        $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray')
                                    ),

                                TextEntry::make('net_payout')
                                    ->label(__('admin.payout.fields.net_payout'))
                                    ->money('OMR')
                                    ->weight(FontWeight::Bold)
                                    ->color('success')
                                    ->size(TextSize::Large),
                            ]),

                        TextEntry::make('commission_rate')
                            ->label(__('admin.payout.fields.commission_rate'))
                            ->formatStateUsing(
                                fn($state): string =>
                                number_format((float) $state, 2) . '%'
                            ),
                    ]),

                // Payment Details
                Section::make(__('admin.payout.sections.payment'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('payment_method')
                                    ->label(__('admin.payout.fields.payment_method'))
                                    ->badge()
                                    ->placeholder('—'),

                                TextEntry::make('transaction_reference')
                                    ->label(__('admin.payout.fields.transaction_reference'))
                                    ->copyable()
                                    ->placeholder('—'),
                            ]),

                        KeyValueEntry::make('bank_details')
                            ->label(__('admin.payout.fields.bank_details'))
                            ->visible(fn($record) => !empty($record->bank_details)),
                    ])
                    ->collapsible(),

                // Timestamps
                Section::make(__('admin.payout.sections.timestamps'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('processed_at')
                                    ->label(__('admin.payout.fields.processed_at'))
                                    ->dateTime('d M Y H:i')
                                    ->placeholder('—'),

                                TextEntry::make('completed_at')
                                    ->label(__('admin.payout.fields.completed_at'))
                                    ->dateTime('d M Y H:i')
                                    ->placeholder('—'),

                                TextEntry::make('failed_at')
                                    ->label(__('admin.payout.fields.failed_at'))
                                    ->dateTime('d M Y H:i')
                                    ->placeholder('—'),
                            ]),

                        TextEntry::make('processor.name')
                            ->label(__('admin.payout.fields.processed_by'))
                            ->placeholder('—'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                // Notes & Failure Reason
                Section::make(__('admin.payout.sections.notes'))
                    ->schema([
                        TextEntry::make('notes')
                            ->label(__('admin.payout.fields.notes'))
                            ->placeholder(__('admin.payout.no_notes'))
                            ->columnSpanFull(),

                        TextEntry::make('failure_reason')
                            ->label(__('admin.payout.fields.failure_reason'))
                            ->color('danger')
                            ->visible(fn($record) => $record->status === PayoutStatus::FAILED)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ])
            ->columns(1);
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
            'index' => ListPayouts::route('/'),
            'create' => CreatePayout::route('/create'),
            'view' => ViewPayout::route('/{record}'),
            'edit' => EditPayout::route('/{record}/edit'),
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
