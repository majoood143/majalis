<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\ExportBulkAction;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use App\Filament\Owner\Resources\EarningsResource\Pages\ListEarnings;
use App\Filament\Owner\Resources\EarningsResource\Pages\ViewEarnings;
use Filament\Resources\Pages\PageRegistration;
use App\Filament\Owner\Resources\EarningsResource\Pages;
use App\Models\Booking;
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
use Illuminate\Support\Facades\DB;

/**
 * EarningsResource for Owner Panel
 *
 * This resource allows hall owners to view their earnings from bookings.
 * It provides a read-only view of completed/paid bookings with financial details.
 *
 * Features:
 * - View earnings by booking
 * - Filter by date range, hall, payment status
 * - Export earnings data
 * - Summary statistics (total revenue, commission, net earnings)
 *
 * @package App\Filament\Owner\Resources
 */
class EarningsResource extends Resource
{
    /**
     * The model the resource corresponds to.
     * We use Booking model as earnings are derived from bookings.
     *
     * @var string|null
     */
    protected static ?string $model = Booking::class;

    /**
     * The navigation icon for the resource.
     *
     * @var string|null
     */
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    /**
     * The navigation group for the resource.
     *
     * @var string|null
     */
    //protected static ?string $navigationGroup = 'Financial';

    public static function getNavigationGroup(): ?string
    {
        return __('owner.earnings.navigation_group');
    }

    /**
     * The navigation sort order.
     *
     * @var int|null
     */
    protected static ?int $navigationSort = 1;

    /**
     * The slug for the resource.
     *
     * @var string|null
     */
    protected static ?string $slug = 'earnings';

    /**
     * Get the navigation label.
     *
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('owner.earnings.navigation');
    }

    /**
     * Get the model label.
     *
     * @return string
     */
    public static function getModelLabel(): string
    {
        return __('owner.earnings.singular');
    }

    /**
     * Get the plural model label.
     *
     * @return string
     */
    public static function getPluralModelLabel(): string
    {
        return __('owner.earnings.plural');
    }

    /**
     * Get the navigation badge showing total pending earnings.
     *
     * @return string|null
     */
    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        // Get total pending earnings (confirmed but not yet paid out)
        $pendingEarnings = Booking::whereHas('hall', function ($q) use ($user): void {
            $q->where('owner_id', $user->id);
        })
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->sum('owner_payout');

        return number_format((float) $pendingEarnings, 0) . ' OMR';
    }

    /**
     * Get the navigation badge color.
     *
     * @return string|null
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    /**
     * Define the form schema (read-only for earnings).
     *
     * @param Schema $schema
     * @return Schema
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Earnings are read-only, no form needed
            ]);
    }

    /**
     * Define the table schema for earnings listing.
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                $user = Auth::user();

                // Only show bookings from owner's halls that are paid
                return $query
                    ->whereHas('hall', function ($q) use ($user): void {
                        $q->where('owner_id', $user->id);
                    })
                    ->whereIn('status', ['confirmed', 'completed'])
                    ->where('payment_status', 'paid')
                    ->with(['hall', 'user', 'extraServices'])
                    ->latest('booking_date');
            })
            ->columns([
                // Booking number
                TextColumn::make('booking_number')
                    ->label(__('owner.earnings.booking_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

                // Hall name
                TextColumn::make('hall.name')
                    ->label(__('owner.earnings.hall'))
                    ->formatStateUsing(function ($state): string {
                        if (is_array($state)) {
                            return $state[app()->getLocale()] ?? $state['en'] ?? '-';
                        }
                        return $state ?? '-';
                    })
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                // Booking date
                TextColumn::make('booking_date')
                    ->label(__('owner.earnings.date'))
                    ->date('M d, Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar'),

                // Time slot
                TextColumn::make('time_slot')
                    ->label(__('owner.earnings.slot'))
                    ->badge()
                    ->formatStateUsing(fn ($state): string => __("owner.slots.{$state}"))
                    ->toggleable(),

                // Customer name
                TextColumn::make('customer_name')
                    ->label(__('owner.earnings.customer'))
                    ->searchable()
                    ->toggleable(),

                // Hall price (base)
                TextColumn::make('hall_price')
                    ->label(__('owner.earnings.hall_price'))
                    ->money('OMR')
                    ->sortable()
                    ->summarize(Sum::make()->money('OMR')->label(__('owner.earnings.total')))
                    ->alignEnd()
                    ->toggleable(),

                // Services price
                TextColumn::make('services_price')
                    ->label(__('owner.earnings.services'))
                    ->money('OMR')
                    ->sortable()
                    ->summarize(Sum::make()->money('OMR')->label(__('owner.earnings.total')))
                    ->alignEnd()
                    ->toggleable(),

                // Total amount (gross)
                TextColumn::make('total_amount')
                    ->label(__('owner.earnings.gross'))
                    ->money('OMR')
                    ->sortable()
                    ->summarize(Sum::make()->money('OMR')->label(__('owner.earnings.total')))
                    ->alignEnd()
                    ->weight('semibold'),

                // Commission amount
                TextColumn::make('commission_amount')
                    ->label(__('owner.earnings.commission'))
                    ->money('OMR')
                    ->sortable()
                    ->summarize(Sum::make()->money('OMR')->label(__('owner.earnings.total')))
                    ->alignEnd()
                    ->color('danger')
                    ->toggleable(),

                // Owner payout (net)
                TextColumn::make('owner_payout')
                    ->label(__('owner.earnings.net'))
                    ->money('OMR')
                    ->sortable()
                    ->summarize(Sum::make()->money('OMR')->label(__('owner.earnings.total')))
                    ->alignEnd()
                    ->weight('bold')
                    ->color('success'),

                // Status
                BadgeColumn::make('status')
                    ->label(__('owner.earnings.status'))
                    ->colors([
                        'success' => 'confirmed',
                        'info' => 'completed',
                    ])
                    ->formatStateUsing(fn ($state): string => __("owner.status.{$state}"))
                    ->toggleable(),

                // Completed date
                TextColumn::make('completed_at')
                    ->label(__('owner.earnings.completed_at'))
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Date range filter
                Filter::make('date_range')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('from_date')
                                    ->label(__('owner.earnings.from_date'))
                                    ->default(now()->startOfMonth())
                                    ->native(false),

                                DatePicker::make('to_date')
                                    ->label(__('owner.earnings.to_date'))
                                    ->default(now()->endOfMonth())
                                    ->native(false),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $q): Builder => $q->whereDate('booking_date', '>=', $data['from_date']),
                            )
                            ->when(
                                $data['to_date'],
                                fn (Builder $q): Builder => $q->whereDate('booking_date', '<=', $data['to_date']),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from_date'] ?? null) {
                            $indicators['from_date'] = __('owner.earnings.from') . ': ' . $data['from_date'];
                        }

                        if ($data['to_date'] ?? null) {
                            $indicators['to_date'] = __('owner.earnings.to') . ': ' . $data['to_date'];
                        }

                        return $indicators;
                    }),

                // Hall filter
                SelectFilter::make('hall_id')
                    ->label(__('owner.earnings.hall'))
                    ->relationship(
                        'hall',
                        'name',
                        fn (Builder $query): Builder => $query->where('owner_id', Auth::id())
                    )
                    ->searchable()
                    ->preload(),

                // Status filter
                SelectFilter::make('status')
                    ->label(__('owner.earnings.status'))
                    ->options([
                        'confirmed' => __('owner.status.confirmed'),
                        'completed' => __('owner.status.completed'),
                    ]),

                // Time slot filter
                SelectFilter::make('time_slot')
                    ->label(__('owner.earnings.slot'))
                    ->options([
                        'morning' => __('owner.slots.morning'),
                        'afternoon' => __('owner.slots.afternoon'),
                        'evening' => __('owner.slots.evening'),
                        'full_day' => __('owner.slots.full_day'),
                    ]),

                // This month quick filter
                Filter::make('this_month')
                    ->label(__('owner.earnings.this_month'))
                    ->query(fn (Builder $query): Builder => $query
                        ->whereMonth('booking_date', now()->month)
                        ->whereYear('booking_date', now()->year))
                    ->toggle(),

                // Last month quick filter
                Filter::make('last_month')
                    ->label(__('owner.earnings.last_month'))
                    ->query(fn (Builder $query): Builder => $query
                        ->whereMonth('booking_date', now()->subMonth()->month)
                        ->whereYear('booking_date', now()->subMonth()->year))
                    ->toggle(),
            ])
            ->recordActions([
                // View details
                ViewAction::make()
                    ->label(__('owner.actions.view'))
                    ->icon('heroicon-m-eye'),
            ])
            ->toolbarActions([
                // Export action
                ExportBulkAction::make()
                    ->label(__('owner.earnings.export')),
            ])
            ->defaultSort('booking_date', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('60s')
            ->emptyStateHeading(__('owner.earnings.no_earnings'))
            ->emptyStateDescription(__('owner.earnings.no_earnings_desc'))
            ->emptyStateIcon('heroicon-o-banknotes');
    }

    /**
     * Define the infolist schema for viewing earnings details.
     *
     * @param Schema $schema
     * @return Schema
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Booking Information Section
                Section::make(__('owner.earnings.booking_info'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('booking_number')
                                    ->label(__('owner.earnings.booking_number'))
                                    ->weight('bold')
                                    ->copyable(),

                                TextEntry::make('booking_date')
                                    ->label(__('owner.earnings.date'))
                                    ->date('F j, Y'),

                                TextEntry::make('time_slot')
                                    ->label(__('owner.earnings.slot'))
                                    ->badge()
                                    ->formatStateUsing(fn ($state): string => __("owner.slots.{$state}")),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('hall.name')
                                    ->label(__('owner.earnings.hall'))
                                    ->formatStateUsing(function ($state): string {
                                        if (is_array($state)) {
                                            return $state[app()->getLocale()] ?? $state['en'] ?? '-';
                                        }
                                        return $state ?? '-';
                                    }),

                                TextEntry::make('customer_name')
                                    ->label(__('owner.earnings.customer')),

                                TextEntry::make('status')
                                    ->label(__('owner.earnings.status'))
                                    ->badge()
                                    ->color(fn ($state): string => match ($state) {
                                        'confirmed' => 'success',
                                        'completed' => 'info',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn ($state): string => __("owner.status.{$state}")),
                            ]),
                    ])
                    ->collapsible(),

                // Financial Breakdown Section
                Section::make(__('owner.earnings.financial_breakdown'))
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('hall_price')
                                    ->label(__('owner.earnings.hall_price'))
                                    ->money('OMR')
                                    ->size('lg'),

                                TextEntry::make('services_price')
                                    ->label(__('owner.earnings.services'))
                                    ->money('OMR')
                                    ->size('lg'),

                                TextEntry::make('total_amount')
                                    ->label(__('owner.earnings.gross'))
                                    ->money('OMR')
                                    ->size('lg')
                                    ->weight('bold'),

                                TextEntry::make('commission_amount')
                                    ->label(__('owner.earnings.commission'))
                                    ->money('OMR')
                                    ->size('lg')
                                    ->color('danger'),
                            ]),

                        // Net Earnings Highlight
                        TextEntry::make('owner_payout')
                            ->label(__('owner.earnings.your_earnings'))
                            ->money('OMR')
                            ->size('xl')
                            ->weight('bold')
                            ->color('success')
                            ->extraAttributes(['class' => 'text-2xl']),

                        // Commission details
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('commission_type')
                                    ->label(__('owner.earnings.commission_type'))
                                    ->formatStateUsing(fn ($state): string => $state ? ucfirst($state) : 'Percentage'),

                                TextEntry::make('commission_value')
                                    ->label(__('owner.earnings.commission_rate'))
                                    ->formatStateUsing(function ($state, $record): string {
                                        if ($record->commission_type === 'fixed') {
                                            return number_format((float) $state, 3) . ' OMR';
                                        }
                                        return number_format((float) $state, 2) . '%';
                                    }),
                            ]),
                    ])
                    ->columns(1),

                // Extra Services Section (if any)
                Section::make(__('owner.earnings.extra_services'))
                    ->schema([
                        RepeatableEntry::make('extraServices')
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('owner.earnings.service_name'))
                                    ->formatStateUsing(function ($state): string {
                                        if (is_array($state)) {
                                            return $state[app()->getLocale()] ?? $state['en'] ?? '-';
                                        }
                                        return $state ?? '-';
                                    }),

                                TextEntry::make('pivot.quantity')
                                    ->label(__('owner.earnings.quantity')),

                                TextEntry::make('pivot.unit_price')
                                    ->label(__('owner.earnings.unit_price'))
                                    ->money('OMR'),

                                TextEntry::make('pivot.total_price')
                                    ->label(__('owner.earnings.total'))
                                    ->money('OMR')
                                    ->weight('bold'),
                            ])
                            ->columns(4)
                            ->grid(1),
                    ])
                    ->collapsible()
                    ->collapsed(fn ($record): bool => $record->extraServices->isEmpty())
                    ->hidden(fn ($record): bool => $record->extraServices->isEmpty()),

                // Payment Details
                Section::make(__('owner.earnings.payment_details'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('payment_status')
                                    ->label(__('owner.earnings.payment_status'))
                                    ->badge()
                                    ->color('success')
                                    ->formatStateUsing(fn ($state): string => __("owner.payment.{$state}")),

                                TextEntry::make('confirmed_at')
                                    ->label(__('owner.earnings.confirmed_at'))
                                    ->dateTime('F j, Y H:i'),

                                TextEntry::make('completed_at')
                                    ->label(__('owner.earnings.completed_at'))
                                    ->dateTime('F j, Y H:i')
                                    ->placeholder('-'),
                            ]),
                    ])
                    ->collapsible(),
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
            'index' => ListEarnings::route('/'),
            'view' => ViewEarnings::route('/{record}'),
        ];
    }

    /**
     * Allow any authenticated owner to access this resource.
     * Data is already scoped to the owner's halls via modifyQueryUsing.
     *
     * @return bool
     */
    public static function canAccess(): bool
    {
        return Auth::check();
    }

    /**
     * Allow any authenticated owner to view the list.
     *
     * @return bool
     */
    public static function canViewAny(): bool
    {
        return Auth::check();
    }

    /**
     * Disable creating earnings (read-only resource).
     *
     * @return bool
     */
    public static function canCreate(): bool
    {
        return false;
    }
}
