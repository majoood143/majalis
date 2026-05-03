<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\ServiceFeeSettingResource\Pages\ListServiceFeeSettings;
use App\Filament\Admin\Resources\ServiceFeeSettingResource\Pages\CreateServiceFeeSetting;
use App\Filament\Admin\Resources\ServiceFeeSettingResource\Pages\EditServiceFeeSetting;
use App\Filament\Admin\Resources\ServiceFeeSettingResource\Pages;
use App\Models\ServiceFeeSetting;
use App\Models\Hall;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Tables\Actions\ActionGroup;

/**
 * Filament Resource: Service Fee Settings
 *
 * Manages customer-visible service fees charged on top of booking prices.
 * Mirrors CommissionSettingResource structure for consistency.
 *
 * @see \App\Models\ServiceFeeSetting
 */
class ServiceFeeSettingResource extends Resource
{
    protected static ?string $model = ServiceFeeSetting::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-receipt-percent';

    /**
     * Group under "Financial" navigation (same as Commission Settings).
     */
    public static function getNavigationGroup(): ?string
    {
        return __('service-fee.navigation_group');
    }

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('service-fee.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('service-fee.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('service-fee.navigation_label');
    }

    // ─────────────────────────────────────────────────────────
    // Form Definition
    // ─────────────────────────────────────────────────────────

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Section 1: Fee Scope ──
                Section::make(__('service-fee.fee_scope'))
                    ->description(__('service-fee.scope_description'))
                    ->schema([

                        // Hall selector (optional — leave blank for owner/global)
                        Select::make('hall_id')
                            ->label(__('service-fee.hall'))
                            ->options(function (): array {
                                return Hall::query()
                                    ->where('is_active', true)
                                    ->get()
                                    ->mapWithKeys(function (Hall $hall): array {
                                        $name = is_array($hall->name)
                                            ? ($hall->name[app()->getLocale()] ?? $hall->name['en'] ?? 'Hall #' . $hall->id)
                                            : ($hall->name ?? 'Hall #' . $hall->id);
                                        return [$hall->id => $name];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->helperText(__('service-fee.hall_helper')),

                        // Owner selector (optional — leave blank for global)
                        Select::make('owner_id')
                            ->label(__('service-fee.owner'))
                            ->options(function (): array {
                                return User::role('hall_owner')
                                    ->get()
                                    ->mapWithKeys(fn(User $user): array => [
                                        $user->id => $user->name . ' (' . $user->email . ')',
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->helperText(__('service-fee.owner_helper')),

                        // Priority info placeholder
                        Placeholder::make('scope_note')
                            ->label(__('service-fee.scope_note_title'))
                            ->content(__('service-fee.scope_note'))
                            ->columnSpanFull(),

                    ])->columns(2),

                // ── Section 2: Fee Details ──
                Section::make(__('service-fee.fee_details'))
                    ->schema([

                        // Bilingual name
                        TextInput::make('name.en')
                            ->label(__('service-fee.name_en'))
                            ->maxLength(255),

                        TextInput::make('name.ar')
                            ->label(__('service-fee.name_ar'))
                            ->maxLength(255),

                        // Fee type: percentage or fixed
                        Select::make('fee_type')
                            ->options([
                                'percentage' => __('service-fee.percentage'),
                                'fixed'      => __('service-fee.fixed'),
                            ])
                            ->label(__('service-fee.fee_type'))
                            ->required()
                            ->live()
                            ->default('percentage'),

                        // Fee value with dynamic suffix
                        TextInput::make('fee_value')
                            ->label(__('service-fee.fee_value'))
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(
                                fn(\Filament\Schemas\Components\Utilities\Get $get): float =>
                                $get('fee_type') === 'percentage' ? 100.00 : 99999.99
                            )
                            ->suffix(fn(\Filament\Schemas\Components\Utilities\Get $get): string =>
                                $get('fee_type') === 'percentage' ? '%' : 'OMR'
                            )
                            ->live(debounce: 500),

                        // Bilingual description
                        Textarea::make('description.en')
                            ->label(__('service-fee.description_en'))
                            ->rows(2)
                            ->maxLength(1000),

                        Textarea::make('description.ar')
                            ->label(__('service-fee.description_ar'))
                            ->rows(2)
                            ->maxLength(1000),

                    ])->columns(2),

                // ── Section 3: Validity Period ──
                Section::make(__('service-fee.validity_period'))
                    ->schema([

                        DatePicker::make('effective_from')
                            ->label(__('service-fee.effective_from'))
                            ->helperText(__('service-fee.effective_from_helper'))
                            ->native(false),

                        DatePicker::make('effective_to')
                            ->label(__('service-fee.effective_to'))
                            ->helperText(__('service-fee.effective_to_helper'))
                            ->native(false)
                            ->after('effective_from'),

                        Toggle::make('is_active')
                            ->label(__('service-fee.is_active'))
                            ->default(true)
                            ->columnSpanFull(),

                    ])->columns(2),
            ]);
    }

    // ─────────────────────────────────────────────────────────
    // Table Definition
    // ─────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                // Scope badge (Global / Owner / Hall)
                TextColumn::make('scope')
                    ->label(__('service-fee.scope'))
                    ->state(function (ServiceFeeSetting $record): string {
                        if ($record->hall_id) {
                            $hallName = is_array($record->hall?->name)
                                ? ($record->hall->name[app()->getLocale()] ?? $record->hall->name['en'] ?? '')
                                : ($record->hall->name ?? '');
                            return __('service-fee.hall_specific', ['name' => $hallName]);
                        }
                        if ($record->owner_id) {
                            return __('service-fee.owner_specific', ['name' => $record->owner?->name ?? '']);
                        }
                        return __('service-fee.global');
                    })
                    ->badge()
                    ->color(fn(ServiceFeeSetting $record) => match (true) {
                        $record->hall_id !== null  => 'success',
                        $record->owner_id !== null => 'warning',
                        default                    => 'primary',
                    })
                    ->searchable(false)
                    ->sortable(false),

                // Fee type badge
                TextColumn::make('fee_type')
                    ->label(__('service-fee.fee_type'))
                    ->formatStateUsing(function ($state) {
                        return $state->value === 'percentage'
                            ? __('service-fee.percentage')
                            : __('service-fee.fixed');
                    })
                    ->color(fn($state) => match ($state->value) {
                        'percentage' => 'success',
                        'fixed'      => 'info',
                        default      => 'gray',
                    })
                    ->badge()
                    ->sortable(),

                // Fee value (formatted)
                TextColumn::make('fee_value')
                    ->label(__('service-fee.value'))
                    ->formatStateUsing(function ($record) {
                        return $record->fee_type->value === 'percentage'
                            ? $record->fee_value . '%'
                            : number_format((float) $record->fee_value, 3) . ' OMR';
                    })
                    ->sortable(),

                // Effective date range
                TextColumn::make('effective_from')
                    ->label(__('service-fee.effective_from'))
                    ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('effective_to')
                    ->label(__('service-fee.effective_to'))
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->placeholder(__('Indefinite')),

                // Active toggle
                IconColumn::make('is_active')
                    ->label(__('service-fee.is_active'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('service-fee.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            // ── Filters ──
            ->filters([
                SelectFilter::make('fee_type')
                    ->label(__('service-fee.fee_type'))
                    ->options([
                        'percentage' => __('service-fee.percentage'),
                        'fixed'      => __('service-fee.fixed'),
                    ]),

                TernaryFilter::make('is_active')
                    ->label(__('service-fee.filters.active'))
                    ->boolean()
                    ->native(false),

                Filter::make('scope')
                    ->label(__('service-fee.scope'))
                    ->schema([
                        Select::make('scope_type')
                            ->label(__('service-fee.filters.scope_type'))
                            ->options([
                                'global' => __('service-fee.filters.global'),
                                'owner'  => __('service-fee.filters.owner'),
                                'hall'   => __('service-fee.filters.hall'),
                            ]),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['scope_type'] === 'global', fn($q) => $q->whereNull('hall_id')->whereNull('owner_id'))
                            ->when($data['scope_type'] === 'owner', fn($q) => $q->whereNotNull('owner_id')->whereNull('hall_id'))
                            ->when($data['scope_type'] === 'hall', fn($q) => $q->whereNotNull('hall_id'));
                    }),
            ])

            // ── Row Actions ──
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                EditAction::make()
                    ->label(__('service-fee.edit')),
                DeleteAction::make()
                    ->label(__('service-fee.delete')),
                // TODO: ActivityLogTimelineTableAction removed (rmsramos v3-only) - replace with v4 equivalent,
                ])
            ])

            // ── Bulk Actions ──
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])

            ->defaultSort('created_at', 'desc');
    }

    // ─────────────────────────────────────────────────────────
    // Pages
    // ─────────────────────────────────────────────────────────

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListServiceFeeSettings::route('/'),
            'create' => CreateServiceFeeSetting::route('/create'),
            'edit'   => EditServiceFeeSetting::route('/{record}/edit'),
        ];
    }
}
