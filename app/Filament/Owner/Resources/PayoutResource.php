<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Owner\Resources\PayoutResource\Pages\ListPayouts;
use App\Filament\Owner\Resources\PayoutResource\Pages\CreatePayout;
use App\Filament\Owner\Resources\PayoutResource\Pages\ViewPayout;
use Filament\Resources\Pages\PageRegistration;
use App\Enums\PayoutStatus;
use App\Filament\Owner\Resources\PayoutResource\Pages;
use App\Models\Booking;
use App\Models\OwnerPayout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * PayoutResource for Owner Panel
 *
 * This resource allows hall owners to view their payout history.
 * Payouts are processed by admins, so this is a read-only view.
 *
 * Features:
 * - View payout history
 * - Filter by status, date range
 * - Track pending vs completed payouts
 * - Download payout receipts
 *
 * @package App\Filament\Owner\Resources
 */
class PayoutResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = OwnerPayout::class;

    /**
     * The navigation icon for the resource.
     *
     * @var string|null
     */
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';

    /**
     * The navigation group for the resource.
     *
     * @var string|null
     */
    //protected static ?string $navigationGroup = 'Financial';

    public static function getNavigationGroup(): ?string
    {
        return __('owner.payouts.navigation_group');
    }

    /**
     * The navigation sort order.
     *
     * @var int|null
     */
    protected static ?int $navigationSort = 2;

    /**
     * The slug for the resource.
     *
     * @var string|null
     */
    protected static ?string $slug = 'payouts';

    /**
     * Get the navigation label.
     *
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('owner.payouts.navigation');
    }

    /**
     * Get the model label.
     *
     * @return string
     */
    public static function getModelLabel(): string
    {
        return __('owner.payouts.singular');
    }

    /**
     * Get the plural model label.
     *
     * @return string
     */
    public static function getPluralModelLabel(): string
    {
        return __('owner.payouts.plural');
    }

    /**
     * Get the navigation badge showing pending payouts.
     *
     * @return string|null
     */
    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        $pendingCount = OwnerPayout::where('owner_id', $user->id)
            ->whereIn('status', [PayoutStatus::PENDING, PayoutStatus::PROCESSING])
            ->count();

        return $pendingCount > 0 ? (string) $pendingCount : null;
    }

    /**
     * Get the navigation badge color.
     *
     * @return string|null
     */
    public static function getNavigationBadgeColor(): ?string
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        $hasPending = OwnerPayout::where('owner_id', $user->id)
            ->where('status', PayoutStatus::PENDING)
            ->exists();

        return $hasPending ? 'warning' : 'success';
    }

    /**
     * Define the form schema for creating a payout request.
     *
     * @param Schema $schema
     * @return Schema
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Period Selection
                Section::make(__('owner.payouts.form_period_section'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('period_start')
                                    ->label(__('owner.payouts.period_start'))
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->maxDate(now())
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                                        if ($state && $get('period_end')) {
                                            static::recalculateFinancials($set, $get);
                                        }
                                    }),

                                DatePicker::make('period_end')
                                    ->label(__('owner.payouts.period_end'))
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->minDate(fn(Get $get) => $get('period_start'))
                                    ->maxDate(now())
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                                        if ($get('period_start') && $state) {
                                            static::recalculateFinancials($set, $get);
                                        }
                                    }),
                            ]),
                    ]),

                // Financial Summary (read-only, auto-calculated)
                Section::make(__('owner.payouts.form_financial_section'))
                    ->description(__('owner.payouts.form_financial_desc'))
                    ->schema([
                        Placeholder::make('no_bookings_notice')
                            ->label('')
                            ->content(__('owner.payouts.no_bookings_warning'))
                            ->visible(fn(Get $get): bool =>
                                (int) ($get('bookings_count') ?? 0) === 0
                                && !empty($get('period_start'))
                                && !empty($get('period_end'))
                            ),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('bookings_count')
                                    ->label(__('owner.payouts.bookings_count'))
                                    ->readOnly()
                                    ->default(0),

                                TextInput::make('gross_revenue')
                                    ->label(__('owner.payouts.gross_revenue'))
                                    ->readOnly()
                                    ->prefix('OMR')
                                    ->default('0.000'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('commission_amount')
                                    ->label(__('owner.payouts.commission_amount'))
                                    ->readOnly()
                                    ->prefix('OMR')
                                    ->default('0.000'),

                                TextInput::make('commission_rate')
                                    ->label(__('owner.payouts.commission_rate'))
                                    ->readOnly()
                                    ->suffix('%')
                                    ->default('0.00'),

                                TextInput::make('net_payout')
                                    ->label(__('owner.payouts.net_payout'))
                                    ->readOnly()
                                    ->prefix('OMR')
                                    ->default('0.000')
                                    ->extraAttributes(['class' => 'font-bold text-green-600']),
                            ]),
                    ]),

                // Notes (optional)
                Section::make(__('owner.payouts.notes'))
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('owner.payouts.notes'))
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder(__('owner.payouts.notes_placeholder'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    /**
     * Auto-calculate financial details from bookings in the selected period.
     */
    protected static function recalculateFinancials(Set $set, Get $get): void
    {
        $user = Auth::user();
        $periodStart = $get('period_start');
        $periodEnd   = $get('period_end');

        if (!$user || !$periodStart || !$periodEnd) {
            return;
        }

        $bookings = Booking::whereHas('hall', function ($q) use ($user): void {
            $q->where('owner_id', $user->id);
        })
            ->whereBetween('booking_date', [$periodStart, $periodEnd])
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->get();

        $gross      = (float) $bookings->sum('total_amount');
        $commission = (float) $bookings->sum('commission_amount');
        $net        = (float) $bookings->sum('owner_payout');
        $rate       = $gross > 0 ? ($commission / $gross) * 100 : 0;

        $set('bookings_count',    $bookings->count());
        $set('gross_revenue',     number_format($gross, 3, '.', ''));
        $set('commission_amount', number_format($commission, 3, '.', ''));
        $set('commission_rate',   number_format($rate, 2, '.', ''));
        $set('net_payout',        number_format($net, 3, '.', ''));
    }

    /**
     * Define the table schema for payouts listing.
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                $user = Auth::user();

                // Only show payouts for this owner
                return $query
                    ->where('owner_id', $user->id)
                    ->with(['processor'])
                    ->latest('created_at');
            })
            ->columns([
                // Payout number
                TextColumn::make('payout_number')
                    ->label(__('owner.payouts.payout_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

                // Period
                TextColumn::make('period_start')
                    ->label(__('owner.payouts.period'))
                    ->formatStateUsing(function ($state, $record): string {
                        return $record->period_start->format('M d') . ' - ' .
                            $record->period_end->format('M d, Y');
                    })
                    ->sortable()
                    ->icon('heroicon-m-calendar'),

                // Bookings count
                TextColumn::make('bookings_count')
                    ->label(__('owner.payouts.bookings_count'))
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                // Gross revenue
                TextColumn::make('gross_revenue')
                    ->label(__('owner.payouts.gross_revenue'))
                    ->money('OMR')
                    ->sortable()
                    ->summarize(Sum::make()->money('OMR'))
                    ->alignEnd()
                    ->toggleable(),

                // Commission
                TextColumn::make('commission_amount')
                    ->label(__('owner.payouts.commission_amount'))
                    ->money('OMR')
                    ->sortable()
                    ->summarize(Sum::make()->money('OMR'))
                    ->alignEnd()
                    ->color('danger')
                    ->toggleable(),

                // Commission rate
                TextColumn::make('commission_rate')
                    ->label(__('owner.payouts.rate'))
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 1) . '%')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Net payout
                TextColumn::make('net_payout')
                    ->label(__('owner.payouts.net_payout'))
                    ->money('OMR')
                    ->sortable()
                    ->summarize(Sum::make()->money('OMR'))
                    ->alignEnd()
                    ->weight('bold')
                    ->color('success'),

                // Status
                TextColumn::make('status')
                    ->label(__('owner.payouts.status'))
                    ->badge()
                    ->formatStateUsing(fn (PayoutStatus $state): string => $state->getLabel())
                    ->color(fn (PayoutStatus $state): string => $state->getColor())
                    ->icon(fn (PayoutStatus $state): string => $state->getIcon())
                    ->sortable(),

                // Payment method
                TextColumn::make('payment_method')
                    ->label(__('owner.payouts.payment_method'))
                    ->formatStateUsing(fn ($state): string => $state
                        ? __("owner.payouts.methods.{$state}")
                        : '-')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                // Completed date
                TextColumn::make('completed_at')
                    ->label(__('owner.payouts.completed_at'))
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Transaction reference
                TextColumn::make('transaction_reference')
                    ->label(__('owner.payouts.reference'))
                    ->copyable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Created date
                TextColumn::make('created_at')
                    ->label(__('owner.payouts.created_at'))
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                // Status filter
                SelectFilter::make('status')
                    ->label(__('owner.payouts.status'))
                    ->options(PayoutStatus::toSelectArray()),

                // Date range filter
                Filter::make('period')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('from_date')
                                    ->label(__('owner.payouts.from_date'))
                                    ->native(false),

                                DatePicker::make('to_date')
                                    ->label(__('owner.payouts.to_date'))
                                    ->native(false),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $q): Builder => $q->where('period_start', '>=', $data['from_date']),
                            )
                            ->when(
                                $data['to_date'],
                                fn (Builder $q): Builder => $q->where('period_end', '<=', $data['to_date']),
                            );
                    }),

                // Completed filter
                TernaryFilter::make('completed')
                    ->label(__('owner.payouts.completed'))
                    ->queries(
                        true: fn (Builder $query): Builder => $query->where('status', PayoutStatus::COMPLETED),
                        false: fn (Builder $query): Builder => $query->where('status', '!=', PayoutStatus::COMPLETED),
                    ),

                // This year filter
                Filter::make('this_year')
                    ->label(__('owner.payouts.this_year'))
                    ->query(fn (Builder $query): Builder => $query
                        ->whereYear('period_start', now()->year))
                    ->toggle(),
            ])
            ->recordActions([
                // View details
                ViewAction::make()
                    ->label(__('owner.actions.view'))
                    ->icon('heroicon-m-eye'),

                // Download receipt (if available)
                Action::make('downloadReceipt')
                    ->label(__('owner.payouts.download_receipt'))
                    ->icon('heroicon-m-document-arrow-down')
                    ->color('success')
                    ->visible(fn ($record): bool => $record->status === PayoutStatus::COMPLETED
                        && !empty($record->receipt_path))
                    ->url(fn ($record): string => Storage::disk('public')
                        ->url($record->receipt_path))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                // No bulk actions for read-only resource
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50])
            ->poll('60s')
            ->emptyStateHeading(__('owner.payouts.no_payouts'))
            ->emptyStateDescription(__('owner.payouts.no_payouts_desc'))
            ->emptyStateIcon('heroicon-o-credit-card');
    }

    /**
     * Define the infolist schema for viewing payout details.
     *
     * @param Schema $schema
     * @return Schema
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Payout Summary Section
                Section::make(__('owner.payouts.summary'))
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('payout_number')
                                    ->label(__('owner.payouts.payout_number'))
                                    ->weight('bold')
                                    ->copyable(),

                                TextEntry::make('status')
                                    ->label(__('owner.payouts.status'))
                                    ->badge()
                                    ->formatStateUsing(fn (PayoutStatus $state): string => $state->getLabel())
                                    ->color(fn (PayoutStatus $state): string => $state->getColor()),

                                TextEntry::make('period_start')
                                    ->label(__('owner.payouts.period'))
                                    ->formatStateUsing(fn ($state, $record): string =>
                                        $record->period_start->format('M d, Y') . ' - ' .
                                        $record->period_end->format('M d, Y')),

                                TextEntry::make('bookings_count')
                                    ->label(__('owner.payouts.bookings_count'))
                                    ->badge()
                                    ->color('info'),
                            ]),
                    ]),

                // Financial Breakdown Section
                Section::make(__('owner.payouts.financial_breakdown'))
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('gross_revenue')
                                    ->label(__('owner.payouts.gross_revenue'))
                                    ->money('OMR')
                                    ->size('lg'),

                                TextEntry::make('commission_amount')
                                    ->label(__('owner.payouts.commission_amount'))
                                    ->money('OMR')
                                    ->size('lg')
                                    ->color('danger'),

                                TextEntry::make('commission_rate')
                                    ->label(__('owner.payouts.commission_rate'))
                                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 2) . '%')
                                    ->size('lg'),

                                TextEntry::make('adjustments')
                                    ->label(__('owner.payouts.adjustments'))
                                    ->money('OMR')
                                    ->size('lg')
                                    ->visible(fn ($record): bool => (float) $record->adjustments != 0),
                            ]),

                        // Net Payout Highlight
                        TextEntry::make('net_payout')
                            ->label(__('owner.payouts.net_payout'))
                            ->money('OMR')
                            ->size('xl')
                            ->weight('bold')
                            ->color('success')
                            ->extraAttributes(['class' => 'text-2xl']),
                    ])
                    ->columns(1),

                // Payment Details Section
                Section::make(__('owner.payouts.section_payment'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('payment_method')
                                    ->label(__('owner.payouts.payment_method'))
                                    ->formatStateUsing(fn ($state): string => $state
                                        ? __("owner.payouts.methods.{$state}")
                                        : '-')
                                    ->badge()
                                    ->placeholder('-'),

                                TextEntry::make('transaction_reference')
                                    ->label(__('owner.payouts.transaction_reference'))
                                    ->copyable()
                                    ->placeholder('-'),

                                TextEntry::make('completed_at')
                                    ->label(__('owner.payouts.completed_at'))
                                    ->dateTime('F j, Y H:i')
                                    ->placeholder('-'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('processed_at')
                                    ->label(__('owner.payouts.processed_at'))
                                    ->dateTime('F j, Y H:i')
                                    ->placeholder('-'),

                                TextEntry::make('processor.name')
                                    ->label(__('owner.payouts.processed_by'))
                                    ->placeholder('-'),
                            ]),
                    ])
                    ->collapsible()
                    ->visible(fn ($record): bool => $record->status !== PayoutStatus::PENDING),

                // Failure Details Section (if failed)
                Section::make(__('owner.payouts.failure_details'))
                    ->schema([
                        TextEntry::make('failure_reason')
                            ->label(__('owner.payouts.failure_reason'))
                            ->columnSpanFull(),

                        TextEntry::make('failed_at')
                            ->label(__('owner.payouts.failed_at'))
                            ->dateTime('F j, Y H:i'),
                    ])
                    ->visible(fn ($record): bool => $record->status === PayoutStatus::FAILED)
                    ->collapsed(),

                // Notes Section
                Section::make(__('owner.payouts.notes'))
                    ->schema([
                        TextEntry::make('notes')
                            ->label(__('owner.payouts.notes'))
                            ->columnSpanFull()
                            ->placeholder(__('owner.payouts.no_notes')),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn ($record): bool => !empty($record->notes)),

                // Timestamps
                Section::make(__('owner.payouts.section_timestamps'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('owner.payouts.created_at'))
                                    ->dateTime('F j, Y H:i'),

                                TextEntry::make('updated_at')
                                    ->label(__('owner.payouts.updated_at'))
                                    ->dateTime('F j, Y H:i'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    /**
     * Define the resource pages.
     *
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index'  => ListPayouts::route('/'),
            'create' => CreatePayout::route('/create'),
            'view'   => ViewPayout::route('/{record}'),
        ];
    }

    /**
     * Allow any authenticated owner to view their payouts.
     *
     * @return bool
     */
    public static function canViewAny(): bool
    {
        return Auth::check();
    }

   


    /**
     * Allow owners to create payout requests.
     *
     * @return bool
     */
    public static function canCreate(): bool
    {
        return Auth::check();
    }

    /**
     * Disable editing payouts.
     *
     * @return bool
     */
    public static function canEdit($record): bool
    {
        return false;
    }

    /**
     * Disable deleting payouts.
     *
     * @return bool
     */
    public static function canDelete($record): bool
    {
        return false;
    }
}
