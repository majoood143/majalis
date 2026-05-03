<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Owner\Resources\AvailabilityResource\Pages\ListAvailabilities;
use App\Filament\Owner\Resources\AvailabilityResource\Pages\CreateAvailability;
use App\Filament\Owner\Resources\AvailabilityResource\Pages\EditAvailability;
use App\Filament\Owner\Resources\AvailabilityResource\Pages\AvailabilityCalendar;
use App\Filament\Owner\Resources\AvailabilityResource\Pages;
use App\Models\Hall;
use App\Models\HallAvailability;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * AvailabilityResource for Owner Panel
 *
 * Allows hall owners to manage availability slots across ALL their halls
 * from a single centralized interface.
 *
 * Features:
 * - View all availability slots across all owned halls
 * - Filter by hall, date range, status, time slot
 * - Bulk block/unblock operations
 * - Custom pricing management
 * - Quick status toggle
 *
 * @package App\Filament\Owner\Resources
 */
class AvailabilityResource extends OwnerResource
{
    /**
     * The model the resource corresponds to.
     */
    protected static ?string $model = HallAvailability::class;

    /**
     * The navigation icon.
     */
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    /**
     * The navigation group.
     */
    //protected static ?string $navigationGroup = 'Hall Management';

    public static function getNavigationGroup(): ?string
    {
        return __('owner.nav_groups.hall_management');
    }

    /**
     * The navigation sort order.
     */
    protected static ?int $navigationSort = 2;


    /**
     * Get the navigation label.
     */
    public static function getNavigationLabel(): string
    {
        return __('owner.availability_resource.navigation');
    }

    /**
     * Get the model label.
     */
    public static function getModelLabel(): string
    {
        return __('owner.availability_resource.singular');
    }

    /**
     * Get the plural model label.
     */
    public static function getPluralModelLabel(): string
    {
        return __('owner.availability_resource.plural');
    }

    /**
     * Get the navigation badge showing unavailable slots count.
     */
    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        // Count blocked slots in the next 30 days
        $count = HallAvailability::whereHas('hall', function (Builder $query) use ($user) {
                $query->where('owner_id', $user->id);
            })
            ->where('is_available', false)
            ->where('date', '>=', now()->toDateString())
            ->where('date', '<=', now()->addDays(30)->toDateString())
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    /**
     * Get the navigation badge color.
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    /**
     * Get the Eloquent query scoped to owner's halls.
     */
    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        return parent::getEloquentQuery()
            ->whereHas('hall', function (Builder $query) use ($user) {
                $query->where('owner_id', $user?->id);
            })
            ->with(['hall']);
    }

    /**
     * Configure the form for creating/editing availability.
     */
    public static function form(Schema $schema): Schema
    {
        $user = Auth::user();

        return $schema
            ->components([
                Section::make(__('owner.availability_resource.sections.slot_info'))
                    ->description(__('owner.availability_resource.sections.slot_info_desc'))
                    ->columns(2)
                    ->schema([
                        // Hall Selection (owner's halls only)
                        Select::make('hall_id')
                            ->label(__('owner.availability_resource.fields.hall'))
                            ->relationship(
                                name: 'hall',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->where('owner_id', $user?->id)
                            )
                            ->getOptionLabelFromRecordUsing(fn (Hall $record) => $record->getTranslation('name', app()->getLocale()))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        // Date
                        DatePicker::make('date')
                            ->label(__('owner.availability_resource.fields.date'))
                            ->required()
                            ->native(false)
                            ->minDate(now())
                            ->displayFormat('d M Y')
                            ->closeOnDateSelection(),

                        // Time Slot
                        Select::make('time_slot')
                            ->label(__('owner.availability_resource.fields.time_slot'))
                            ->required()
                            ->options([
                                'morning' => __('owner.slots.morning'),
                                'afternoon' => __('owner.slots.afternoon'),
                                'evening' => __('owner.slots.evening'),
                                'full_day' => __('owner.slots.full_day'),
                            ])
                            ->native(false),

                        // Available Status
                        Toggle::make('is_available')
                            ->label(__('owner.availability_resource.fields.is_available'))
                            ->default(true)
                            ->live()
                            ->afterStateUpdated(function (Set $set, bool $state) {
                                if ($state) {
                                    $set('reason', null);
                                }
                            }),
                    ]),

                Section::make(__('owner.availability_resource.sections.blocking'))
                    ->description(__('owner.availability_resource.sections.blocking_desc'))
                    ->columns(2)
                    ->visible(fn (Get $get): bool => !$get('is_available'))
                    ->schema([
                        // Block Reason
                        Select::make('reason')
                            ->label(__('owner.availability_resource.fields.reason'))
                            ->options([
                                'blocked' => __('owner.availability.reasons.blocked'),
                                'maintenance' => __('owner.availability.reasons.maintenance'),
                                'holiday' => __('owner.availability.reasons.holiday'),
                                'private_event' => __('owner.availability.reasons.private_event'),
                                'renovation' => __('owner.availability.reasons.renovation'),
                                'other' => __('owner.availability.reasons.other'),
                            ])
                            ->default('blocked')
                            ->native(false),

                        // Notes
                        Textarea::make('notes')
                            ->label(__('owner.availability_resource.fields.notes'))
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('owner.availability_resource.sections.pricing'))
                    ->description(__('owner.availability_resource.sections.pricing_desc'))
                    ->collapsed()
                    ->schema([
                        TextInput::make('custom_price')
                            ->label(__('owner.availability_resource.fields.custom_price'))
                            ->numeric()
                            ->minValue(0)
                            ->step(0.001)
                            ->prefix('OMR')
                            ->placeholder(__('owner.availability_resource.placeholders.use_hall_price'))
                            ->helperText(__('owner.availability_resource.helpers.custom_price')),
                    ]),
            ]);
    }

    /**
     * Configure the table for listing availabilities.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'asc')
            ->defaultGroup('hall.name')
            ->striped()
            ->columns([
                // Hall Name
                TextColumn::make('hall.name')
                    ->label(__('owner.availability_resource.columns.hall'))
                    ->formatStateUsing(fn ($record) => $record->hall->getTranslation('name', app()->getLocale()))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                // Date
                TextColumn::make('date')
                    ->label(__('owner.availability_resource.columns.date'))
                    ->date('D, d M Y')
                    ->sortable()
                    ->description(fn ($record): string => $record->date->diffForHumans())
                    ->color(fn ($record): string => match (true) {
                        $record->date->isPast() => 'gray',
                        $record->date->isToday() => 'primary',
                        $record->date->isWeekend() => 'warning',
                        default => 'success',
                    }),

                // Time Slot
                TextColumn::make('time_slot')
                    ->label(__('owner.availability_resource.columns.time_slot'))
                    ->formatStateUsing(fn (string $state): string => __("owner.slots.{$state}"))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'morning' => 'info',
                        'afternoon' => 'warning',
                        'evening' => 'purple',
                        'full_day' => 'success',
                        default => 'gray',
                    }),

                // Status
                IconColumn::make('is_available')
                    ->label(__('owner.availability_resource.columns.status'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),

                // Reason (if blocked)
                TextColumn::make('reason')
                    ->label(__('owner.availability_resource.columns.reason'))
                    ->formatStateUsing(fn (?string $state): string => $state ? __("owner.availability.reasons.{$state}") : '-')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'booked' => 'info',
                        'maintenance' => 'warning',
                        'holiday' => 'success',
                        'renovation' => 'purple',
                        default => 'danger',
                    })
                    ->toggleable(),

                // Custom Price
                TextColumn::make('custom_price')
                    ->label(__('owner.availability_resource.columns.price'))
                    ->money('OMR')
                    ->placeholder(__('owner.availability_resource.placeholders.default_price'))
                    ->toggleable(isToggledHiddenByDefault: true),

                // Notes
                TextColumn::make('notes')
                    ->label(__('owner.availability_resource.columns.notes'))
                    ->limit(30)
                    ->tooltip(fn ($record): ?string => $record->notes)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Hall Filter
                SelectFilter::make('hall_id')
                    ->label(__('owner.availability_resource.filters.hall'))
                    ->relationship('hall', 'name', fn (Builder $query) => $query->where('owner_id', Auth::id()))
                    ->getOptionLabelFromRecordUsing(fn (Hall $record) => $record->getTranslation('name', app()->getLocale()))
                    ->searchable()
                    ->preload(),

                // Time Slot Filter
                SelectFilter::make('time_slot')
                    ->label(__('owner.availability_resource.filters.time_slot'))
                    ->options([
                        'morning' => __('owner.slots.morning'),
                        'afternoon' => __('owner.slots.afternoon'),
                        'evening' => __('owner.slots.evening'),
                        'full_day' => __('owner.slots.full_day'),
                    ]),

                // Status Filter
                TernaryFilter::make('is_available')
                    ->label(__('owner.availability_resource.filters.status'))
                    ->trueLabel(__('owner.availability_resource.filters.available_only'))
                    ->falseLabel(__('owner.availability_resource.filters.blocked_only')),

                // Reason Filter
                SelectFilter::make('reason')
                    ->label(__('owner.availability_resource.filters.reason'))
                    ->options([
                        'blocked' => __('owner.availability.reasons.blocked'),
                        'maintenance' => __('owner.availability.reasons.maintenance'),
                        'holiday' => __('owner.availability.reasons.holiday'),
                        'private_event' => __('owner.availability.reasons.private_event'),
                        'renovation' => __('owner.availability.reasons.renovation'),
                        'booked' => __('owner.availability.reasons.booked'),
                        'other' => __('owner.availability.reasons.other'),
                    ]),

                // Future Only Filter
                Filter::make('future_only')
                    ->label(__('owner.availability_resource.filters.future_only'))
                    ->query(fn (Builder $query): Builder => $query->where('date', '>=', now()->toDateString()))
                    ->default(),

                // Date Range Filter
                Filter::make('date_range')
                    ->schema([
                        DatePicker::make('from_date')
                            ->label(__('owner.availability_resource.filters.from_date'))
                            ->native(false),
                        DatePicker::make('to_date')
                            ->label(__('owner.availability_resource.filters.to_date'))
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from_date'], fn (Builder $q, $date) => $q->whereDate('date', '>=', $date))
                            ->when($data['to_date'], fn (Builder $q, $date) => $q->whereDate('date', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from_date'] ?? null) {
                            $indicators['from_date'] = __('owner.availability_resource.filters.from') . ': ' . $data['from_date'];
                        }
                        if ($data['to_date'] ?? null) {
                            $indicators['to_date'] = __('owner.availability_resource.filters.to') . ': ' . $data['to_date'];
                        }
                        return $indicators;
                    }),

                // This Week Filter
                Filter::make('this_week')
                    ->label(__('owner.availability_resource.filters.this_week'))
                    ->query(fn (Builder $query): Builder => $query->whereBetween('date', [
                        now()->startOfWeek()->toDateString(),
                        now()->endOfWeek()->toDateString(),
                    ])),

                // This Month Filter
                Filter::make('this_month')
                    ->label(__('owner.availability_resource.filters.this_month'))
                    ->query(fn (Builder $query): Builder => $query->whereBetween('date', [
                        now()->startOfMonth()->toDateString(),
                        now()->endOfMonth()->toDateString(),
                    ])),
            ])
            ->filtersFormColumns(3)
            ->recordActions([
                // Quick Toggle Action
                Action::make('toggle')
                    ->label(fn ($record): string => $record->is_available
                        ? __('owner.availability_resource.actions.block')
                        : __('owner.availability_resource.actions.unblock'))
                    ->icon(fn ($record): string => $record->is_available
                        ? 'heroicon-o-x-circle'
                        : 'heroicon-o-check-circle')
                    ->color(fn ($record): string => $record->is_available ? 'danger' : 'success')
                    ->action(function ($record): void {
                        if ($record->is_available) {
                            $record->block();
                        } else {
                            $record->unblock();
                        }

                        Notification::make()
                            ->success()
                            ->title($record->is_available
                                ? __('owner.availability.notifications.unblocked')
                                : __('owner.availability.notifications.blocked'))
                            ->send();
                    }),

                EditAction::make()
                    ->slideOver(),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Bulk Block
                    BulkAction::make('block_selected')
                        ->label(__('owner.availability_resource.bulk.block'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->schema([
                            Select::make('reason')
                                ->label(__('owner.availability_resource.fields.reason'))
                                ->options([
                                    'blocked' => __('owner.availability.reasons.blocked'),
                                    'maintenance' => __('owner.availability.reasons.maintenance'),
                                    'holiday' => __('owner.availability.reasons.holiday'),
                                    'private_event' => __('owner.availability.reasons.private_event'),
                                    'renovation' => __('owner.availability.reasons.renovation'),
                                    'other' => __('owner.availability.reasons.other'),
                                ])
                                ->default('blocked')
                                ->required(),
                            Textarea::make('notes')
                                ->label(__('owner.availability_resource.fields.notes'))
                                ->rows(2),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->is_available) {
                                    $record->block($data['reason'], $data['notes'] ?? null);
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->success()
                                ->title(__('owner.availability.notifications.slots_blocked'))
                                ->body(__('owner.availability.notifications.slots_blocked_count', ['count' => $count]))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Bulk Unblock
                    BulkAction::make('unblock_selected')
                        ->label(__('owner.availability_resource.bulk.unblock'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $count = 0;
                            foreach ($records as $record) {
                                if (!$record->is_available && $record->reason !== 'booked') {
                                    $record->unblock();
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->success()
                                ->title(__('owner.availability.notifications.slots_unblocked'))
                                ->body(__('owner.availability.notifications.slots_unblocked_count', ['count' => $count]))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Bulk Set Custom Price
                    BulkAction::make('set_price')
                        ->label(__('owner.availability_resource.bulk.set_price'))
                        ->icon('heroicon-o-currency-dollar')
                        ->color('warning')
                        ->schema([
                            TextInput::make('custom_price')
                                ->label(__('owner.availability_resource.fields.custom_price'))
                                ->numeric()
                                ->minValue(0)
                                ->step(0.001)
                                ->prefix('OMR')
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(fn ($record) => $record->setCustomPrice($data['custom_price']));

                            Notification::make()
                                ->success()
                                ->title(__('owner.availability.notifications.price_updated'))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Bulk Clear Custom Price
                    BulkAction::make('clear_price')
                        ->label(__('owner.availability_resource.bulk.clear_price'))
                        ->icon('heroicon-o-x-mark')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each(fn ($record) => $record->clearCustomPrice());

                            Notification::make()
                                ->success()
                                ->title(__('owner.availability.notifications.price_cleared'))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('owner.availability_resource.empty.heading'))
            ->emptyStateDescription(__('owner.availability_resource.empty.description'))
            ->emptyStateIcon('heroicon-o-calendar')
            ->emptyStateActions([
                Action::make('generate')
                    ->label(__('owner.availability_resource.empty.action'))
                    ->icon('heroicon-o-plus')
                    ->url(fn () => static::getUrl('create')),
            ]);
    }

    /**
     * Get the pages for the resource.
     */
    public static function getPages(): array
    {
        return [
            'index' => ListAvailabilities::route('/'),
            'create' => CreateAvailability::route('/create'),
            'edit' => EditAvailability::route('/{record}/edit'),
            'calendar' => AvailabilityCalendar::route('/calendar'),
        ];
    }
}
