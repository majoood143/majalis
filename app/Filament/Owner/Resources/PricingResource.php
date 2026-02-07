<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\PricingResource\Pages;
use App\Models\Hall;
use App\Models\SeasonalPricing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * PricingResource for Owner Panel
 *
 * Centralized pricing management for hall owners.
 * Manages seasonal pricing rules, special rates, and discounts.
 *
 * Features:
 * - Create pricing rules for date ranges
 * - Weekend/Holiday special pricing
 * - Percentage or fixed adjustments
 * - Priority-based rule application
 * - Slot-specific pricing
 *
 * @package App\Filament\Owner\Resources
 */
class PricingResource extends Resource
{
    /**
     * The model the resource corresponds to.
     */
    protected static ?string $model = SeasonalPricing::class;

    /**
     * The navigation icon.
     */
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

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
    protected static ?int $navigationSort = 4;

    /**
     * Get the navigation label.
     */
    public static function getNavigationLabel(): string
    {
        return __('owner.pricing.navigation');
    }

    /**
     * Get the model label.
     */
    public static function getModelLabel(): string
    {
        return __('owner.pricing.singular');
    }

    /**
     * Get the plural model label.
     */
    public static function getPluralModelLabel(): string
    {
        return __('owner.pricing.plural');
    }

    /**
     * Get the navigation badge.
     */
    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        $count = SeasonalPricing::whereHas('hall', function (Builder $query) use ($user) {
                $query->where('owner_id', $user->id);
            })
            ->where('is_active', true)
            ->where('end_date', '>=', now())
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    /**
     * Get the navigation badge color.
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
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
     * Configure the form for creating/editing pricing rules.
     */
    public static function form(Form $form): Form
    {
        $user = Auth::user();

        return $form
            ->schema([
                // Basic Information Section
                Forms\Components\Section::make(__('owner.pricing.sections.basic'))
                    ->description(__('owner.pricing.sections.basic_desc'))
                    ->columns(2)
                    ->schema([
                        // Hall Selection
                        Forms\Components\Select::make('hall_id')
                            ->label(__('owner.pricing.fields.hall'))
                            ->relationship(
                                name: 'hall',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->where('owner_id', $user?->id)
                            )
                            ->getOptionLabelFromRecordUsing(fn (Hall $record) => $record->getTranslation('name', app()->getLocale()))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->columnSpanFull(),

                        // Name (English)
                        Forms\Components\TextInput::make('name.en')
                            ->label(__('owner.pricing.fields.name_en'))
                            ->required()
                            ->maxLength(100)
                            ->placeholder(__('owner.pricing.placeholders.name_en')),

                        // Name (Arabic)
                        Forms\Components\TextInput::make('name.ar')
                            ->label(__('owner.pricing.fields.name_ar'))
                            ->required()
                            ->maxLength(100)
                            ->placeholder(__('owner.pricing.placeholders.name_ar')),

                        // Type
                        Forms\Components\Select::make('type')
                            ->label(__('owner.pricing.fields.type'))
                            ->options([
                                'seasonal' => __('owner.pricing.types.seasonal'),
                                'holiday' => __('owner.pricing.types.holiday'),
                                'weekend' => __('owner.pricing.types.weekend'),
                                'special_event' => __('owner.pricing.types.special_event'),
                                'early_bird' => __('owner.pricing.types.early_bird'),
                                'last_minute' => __('owner.pricing.types.last_minute'),
                            ])
                            ->default('seasonal')
                            ->required()
                            ->native(false)
                            ->live(),

                        // Priority
                        Forms\Components\TextInput::make('priority')
                            ->label(__('owner.pricing.fields.priority'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100)
                            ->helperText(__('owner.pricing.helpers.priority')),
                    ]),

                // Date Range Section
                Forms\Components\Section::make(__('owner.pricing.sections.date_range'))
                    ->description(__('owner.pricing.sections.date_range_desc'))
                    ->columns(2)
                    ->schema([
                        // Start Date
                        Forms\Components\DatePicker::make('start_date')
                            ->label(__('owner.pricing.fields.start_date'))
                            ->required()
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->closeOnDateSelection(),

                        // End Date
                        Forms\Components\DatePicker::make('end_date')
                            ->label(__('owner.pricing.fields.end_date'))
                            ->required()
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->closeOnDateSelection()
                            ->afterOrEqual('start_date'),

                        // Is Recurring
                        Forms\Components\Toggle::make('is_recurring')
                            ->label(__('owner.pricing.fields.is_recurring'))
                            ->helperText(__('owner.pricing.helpers.is_recurring'))
                            ->live(),

                        // Recurrence Type
                        Forms\Components\Select::make('recurrence_type')
                            ->label(__('owner.pricing.fields.recurrence_type'))
                            ->options([
                                'weekly' => __('owner.pricing.recurrence.weekly'),
                                'yearly' => __('owner.pricing.recurrence.yearly'),
                            ])
                            ->native(false)
                            ->visible(fn (Forms\Get $get): bool => $get('is_recurring'))
                            ->required(fn (Forms\Get $get): bool => $get('is_recurring'))
                            ->live(),

                        // Days of Week (for weekly recurrence)
                        Forms\Components\CheckboxList::make('days_of_week')
                            ->label(__('owner.pricing.fields.days_of_week'))
                            ->options([
                                0 => __('owner.pricing.days.sunday'),
                                1 => __('owner.pricing.days.monday'),
                                2 => __('owner.pricing.days.tuesday'),
                                3 => __('owner.pricing.days.wednesday'),
                                4 => __('owner.pricing.days.thursday'),
                                5 => __('owner.pricing.days.friday'),
                                6 => __('owner.pricing.days.saturday'),
                            ])
                            ->columns(4)
                            ->visible(fn (Forms\Get $get): bool =>
                                $get('is_recurring') && $get('recurrence_type') === 'weekly'
                            )
                            ->columnSpanFull(),
                    ]),

                // Pricing Adjustment Section
                Forms\Components\Section::make(__('owner.pricing.sections.adjustment'))
                    ->description(__('owner.pricing.sections.adjustment_desc'))
                    ->columns(2)
                    ->schema([
                        // Adjustment Type
                        Forms\Components\Select::make('adjustment_type')
                            ->label(__('owner.pricing.fields.adjustment_type'))
                            ->options([
                                'percentage' => __('owner.pricing.adjustment_types.percentage'),
                                'fixed_increase' => __('owner.pricing.adjustment_types.fixed_increase'),
                                'fixed_price' => __('owner.pricing.adjustment_types.fixed_price'),
                            ])
                            ->default('percentage')
                            ->required()
                            ->native(false)
                            ->live(),

                        // Adjustment Value
                        Forms\Components\TextInput::make('adjustment_value')
                            ->label(fn (Forms\Get $get): string => match ($get('adjustment_type')) {
                                'percentage' => __('owner.pricing.fields.percentage_value'),
                                'fixed_increase' => __('owner.pricing.fields.increase_amount'),
                                'fixed_price' => __('owner.pricing.fields.fixed_amount'),
                                default => __('owner.pricing.fields.adjustment_value'),
                            })
                            ->numeric()
                            ->required()
                            ->step(0.001)
                            ->suffix(fn (Forms\Get $get): string => match ($get('adjustment_type')) {
                                'percentage' => '%',
                                default => 'OMR',
                            })
                            ->helperText(fn (Forms\Get $get): string => match ($get('adjustment_type')) {
                                'percentage' => __('owner.pricing.helpers.percentage'),
                                'fixed_increase' => __('owner.pricing.helpers.fixed_increase'),
                                'fixed_price' => __('owner.pricing.helpers.fixed_price'),
                                default => '',
                            }),

                        // Min Price
                        Forms\Components\TextInput::make('min_price')
                            ->label(__('owner.pricing.fields.min_price'))
                            ->numeric()
                            ->minValue(0)
                            ->step(0.001)
                            ->prefix('OMR')
                            ->placeholder(__('owner.pricing.placeholders.no_minimum'))
                            ->helperText(__('owner.pricing.helpers.min_price')),

                        // Max Price
                        Forms\Components\TextInput::make('max_price')
                            ->label(__('owner.pricing.fields.max_price'))
                            ->numeric()
                            ->minValue(0)
                            ->step(0.001)
                            ->prefix('OMR')
                            ->placeholder(__('owner.pricing.placeholders.no_maximum'))
                            ->helperText(__('owner.pricing.helpers.max_price')),

                        // Apply to Slots
                        Forms\Components\CheckboxList::make('apply_to_slots')
                            ->label(__('owner.pricing.fields.apply_to_slots'))
                            ->options([
                                'morning' => __('owner.slots.morning'),
                                'afternoon' => __('owner.slots.afternoon'),
                                'evening' => __('owner.slots.evening'),
                                'full_day' => __('owner.slots.full_day'),
                            ])
                            ->columns(4)
                            ->helperText(__('owner.pricing.helpers.apply_to_slots'))
                            ->columnSpanFull(),
                    ]),

                // Status Section
                Forms\Components\Section::make(__('owner.pricing.sections.status'))
                    ->columns(2)
                    ->schema([
                        // Is Active
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('owner.pricing.fields.is_active'))
                            ->default(true)
                            ->helperText(__('owner.pricing.helpers.is_active')),

                        // Notes
                        Forms\Components\Textarea::make('notes')
                            ->label(__('owner.pricing.fields.notes'))
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * Configure the table for listing pricing rules.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('priority', 'desc')
            ->striped()
            ->columns([
                // Hall Name
                Tables\Columns\TextColumn::make('hall.name')
                    ->label(__('owner.pricing.columns.hall'))
                    ->formatStateUsing(fn ($record) => $record->hall->getTranslation('name', app()->getLocale()))
                    ->searchable()
                    ->sortable(),

                // Rule Name
                Tables\Columns\TextColumn::make('name')
                    ->label(__('owner.pricing.columns.name'))
                    ->formatStateUsing(fn ($record) => $record->getTranslation('name', app()->getLocale()))
                    ->searchable()
                    ->sortable(),

                // Type
                Tables\Columns\TextColumn::make('type')
                    ->label(__('owner.pricing.columns.type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("owner.pricing.types.{$state}"))
                    ->color(fn (string $state): string => match ($state) {
                        'seasonal' => 'info',
                        'holiday' => 'danger',
                        'weekend' => 'warning',
                        'special_event' => 'purple',
                        'early_bird' => 'success',
                        'last_minute' => 'pink',
                        default => 'gray',
                    }),

                // Date Range
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('owner.pricing.columns.date_range'))
                    ->formatStateUsing(fn ($record): string =>
                        $record->is_recurring
                            ? ($record->recurrence_type === 'weekly'
                                ? __('owner.pricing.columns.every_week')
                                : __('owner.pricing.columns.every_year'))
                            : $record->start_date->format('d M') . ' - ' . $record->end_date->format('d M Y')
                    )
                    ->sortable(),

                // Adjustment
                Tables\Columns\TextColumn::make('adjustment_value')
                    ->label(__('owner.pricing.columns.adjustment'))
                    ->formatStateUsing(fn ($record): string => $record->adjustment_description)
                    ->badge()
                    ->color(fn ($record): string =>
                        $record->adjustment_type === 'percentage' && $record->adjustment_value < 0
                            ? 'success' // Discount
                            : 'warning'  // Increase
                    ),

                // Priority
                Tables\Columns\TextColumn::make('priority')
                    ->label(__('owner.pricing.columns.priority'))
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                // Status
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('owner.pricing.columns.status'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                // Slots
                Tables\Columns\TextColumn::make('apply_to_slots')
                    ->label(__('owner.pricing.columns.slots'))
                    ->formatStateUsing(fn ($state): string =>
                        empty($state)
                            ? __('owner.pricing.columns.all_slots')
                            : implode(', ', array_map(fn ($s) => __("owner.slots.{$s}"), $state))
                    )
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Hall Filter
                SelectFilter::make('hall_id')
                    ->label(__('owner.pricing.filters.hall'))
                    ->relationship('hall', 'name', fn (Builder $query) => $query->where('owner_id', Auth::id()))
                    ->getOptionLabelFromRecordUsing(fn (Hall $record) => $record->getTranslation('name', app()->getLocale()))
                    ->searchable()
                    ->preload(),

                // Type Filter
                SelectFilter::make('type')
                    ->label(__('owner.pricing.filters.type'))
                    ->options([
                        'seasonal' => __('owner.pricing.types.seasonal'),
                        'holiday' => __('owner.pricing.types.holiday'),
                        'weekend' => __('owner.pricing.types.weekend'),
                        'special_event' => __('owner.pricing.types.special_event'),
                        'early_bird' => __('owner.pricing.types.early_bird'),
                        'last_minute' => __('owner.pricing.types.last_minute'),
                    ]),

                // Active Filter
                TernaryFilter::make('is_active')
                    ->label(__('owner.pricing.filters.status'))
                    ->trueLabel(__('owner.pricing.filters.active_only'))
                    ->falseLabel(__('owner.pricing.filters.inactive_only')),

                // Recurring Filter
                TernaryFilter::make('is_recurring')
                    ->label(__('owner.pricing.filters.recurring'))
                    ->trueLabel(__('owner.pricing.filters.recurring_only'))
                    ->falseLabel(__('owner.pricing.filters.one_time_only')),
            ])
            ->actions([
                // Toggle Active
                Tables\Actions\Action::make('toggle')
                    ->label(fn ($record): string => $record->is_active
                        ? __('owner.pricing.actions.deactivate')
                        : __('owner.pricing.actions.activate'))
                    ->icon(fn ($record): string => $record->is_active
                        ? 'heroicon-o-pause'
                        : 'heroicon-o-play')
                    ->color(fn ($record): string => $record->is_active ? 'warning' : 'success')
                    ->action(function ($record): void {
                        $record->update(['is_active' => !$record->is_active]);

                        Notification::make()
                            ->success()
                            ->title($record->is_active
                                ? __('owner.pricing.notifications.activated')
                                : __('owner.pricing.notifications.deactivated'))
                            ->send();
                    }),

                // Duplicate
                Tables\Actions\Action::make('duplicate')
                    ->label(__('owner.pricing.actions.duplicate'))
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function ($record): void {
                        $newRule = $record->replicate();
                        $newRule->name = [
                            'en' => $record->getTranslation('name', 'en') . ' (Copy)',
                            'ar' => $record->getTranslation('name', 'ar') . ' (نسخة)',
                        ];
                        $newRule->is_active = false;
                        $newRule->save();

                        Notification::make()
                            ->success()
                            ->title(__('owner.pricing.notifications.duplicated'))
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Bulk Activate
                    Tables\Actions\BulkAction::make('activate')
                        ->label(__('owner.pricing.bulk.activate'))
                        ->icon('heroicon-o-play')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each(fn ($record) => $record->update(['is_active' => true]));

                            Notification::make()
                                ->success()
                                ->title(__('owner.pricing.notifications.bulk_activated', ['count' => $records->count()]))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Bulk Deactivate
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label(__('owner.pricing.bulk.deactivate'))
                        ->icon('heroicon-o-pause')
                        ->color('warning')
                        ->action(function (Collection $records): void {
                            $records->each(fn ($record) => $record->update(['is_active' => false]));

                            Notification::make()
                                ->success()
                                ->title(__('owner.pricing.notifications.bulk_deactivated', ['count' => $records->count()]))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('owner.pricing.empty.heading'))
            ->emptyStateDescription(__('owner.pricing.empty.description'))
            ->emptyStateIcon('heroicon-o-currency-dollar')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label(__('owner.pricing.empty.action'))
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
            'index' => Pages\ListPricing::route('/'),
            'create' => Pages\CreatePricing::route('/create'),
            'edit' => Pages\EditPricing::route('/{record}/edit'),
            'calculator' => Pages\PriceCalculator::route('/calculator'),
        ];
    }
}
