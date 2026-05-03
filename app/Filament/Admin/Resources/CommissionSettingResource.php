<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\CommissionSettingResource\Pages\ListCommissionSettings;
use App\Filament\Admin\Resources\CommissionSettingResource\Pages\CreateCommissionSetting;
use App\Filament\Admin\Resources\CommissionSettingResource\Pages\EditCommissionSetting;
use App\Filament\Admin\Resources\CommissionSettingResource\Pages;
use App\Models\CommissionSetting;
use App\Models\Hall;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;


class CommissionSettingResource extends Resource
{
    protected static ?string $model = CommissionSetting::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calculator';

    public static function getNavigationGroup(): ?string
    {
        return __('commission-setting.navigation_group');
    }

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('commission-setting.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('commission-setting.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('commission-setting.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('commission-setting.commission_scope'))
                    ->description(__('commission-setting.scope_description'))
                    ->schema([
                        // Forms\Components\Select::make('hall_id')
                        //     ->label(__('commission-setting.hall'))
                        //     ->options(Hall::all()->pluck('name', 'id'))
                        //     ->searchable()
                        //     ->preload()
                        //     ->helperText(__('commission-setting.hall_helper')),

                        Select::make('hall_id')
                            ->label(__('commission-setting.hall'))
                            ->options(function (): array {
                                return Hall::query()
                                    ->where('is_active', true)
                                    ->get()
                                    ->mapWithKeys(function (Hall $hall): array {
                                        // Handle translatable name field
                                        $name = is_array($hall->name)
                                            ? ($hall->name[app()->getLocale()] ?? $hall->name['en'] ?? 'Hall #' . $hall->id)
                                            : $hall->name;
                                        return [$hall->id => $name];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->helperText(__('commission-setting.hall_helper')),

                        Select::make('owner_id')
                            ->label(__('commission-setting.owner'))
                            ->options(User::where('role', 'hall_owner')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->helperText(__('commission-setting.owner_helper')),

                        // Forms\Components\Placeholder::make('scope_note')
                        //     ->label(__('commission-setting.scope_note_title'))
                        //     ->content(__('commission-setting.scope_note'))
                        //     ->columnSpanFull(),

                        Placeholder::make('scope_note')
                            ->label('')
                            ->content(fn(): string => '💡 ' . __('commission-setting.scope_note'))
                            ->columnSpanFull(),

                    ])->columns(2),

                Section::make(__('commission-setting.commission_details'))
                    ->schema([
                        TextInput::make('name.en')
                            ->label(__('commission-setting.name_en'))
                            ->maxLength(255),

                        TextInput::make('name.ar')
                            ->label(__('commission-setting.name_ar'))
                            ->maxLength(255),

                        // Forms\Components\Select::make('commission_type')
                        //     ->options([
                        //         'percentage' => __('commission-setting.percentage'),
                        //         'fixed' => __('commission-setting.fixed'),
                        //     ])
                        //     ->label(__('commission-setting.commission_type'))
                        //     ->required()
                        //     ->reactive(),

                        Select::make('commission_type')
                            ->options([
                                'percentage' => __('commission-setting.percentage'),
                                'fixed' => __('commission-setting.fixed'),
                            ])
                            ->label(__('commission-setting.commission_type'))
                            ->required()
                            ->live()  // Use 'live()' instead of deprecated 'reactive()' in Filament 3.3
                            ->default('percentage'),

                        // Forms\Components\TextInput::make('commission_value')
                        //     ->label(__('commission-setting.commission_value'))
                        //     ->numeric()
                        //     ->required()
                        //     ->step(0.01)
                        //     ->suffix(fn($get) => $get('commission_type') === 'percentage' ? '%' : 'OMR')
                        //     ->live() // Add this to make it reactive
                        //     ->afterStateUpdated(
                        //         fn($set, $get, $state) =>
                        //         $set('commission_value', $state)
                        //     ),
                        TextInput::make('commission_value')
                            ->label(__('commission-setting.commission_value'))
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(
                                fn(Get $get): float =>
                                $get('commission_type') === 'percentage' ? 100 : 999999
                            )
                            ->suffix(
                                fn(Get $get): string =>
                                $get('commission_type') === 'percentage' ? '%' : 'OMR'
                            ),

                        Textarea::make('description.en')
                            ->label(__('commission-setting.description_en'))
                            ->rows(3),

                        Textarea::make('description.ar')
                            ->label(__('commission-setting.description_ar'))
                            ->rows(3),
                    ])->columns(2),

                Section::make(__('commission-setting.validity_period'))
                    ->schema([
                        DatePicker::make('effective_from')
                            ->label(__('commission-setting.effective_from'))
                            ->native(false)
                            ->helperText(__('commission-setting.effective_from_helper')),

                        DatePicker::make('effective_to')
                            ->label(__('commission-setting.effective_to'))
                            ->native(false)
                            ->helperText(__('commission-setting.effective_to_helper')),

                        Toggle::make('is_active')
                            ->label(__('commission-setting.is_active'))
                            ->default(true)
                            ->inline(false),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('commission-setting.scope'))
                    ->badge()
                    ->color(fn($record): string => match (true) {
                        $record->isHallSpecific() => 'success',
                        $record->isOwnerSpecific() => 'warning',
                        default => 'primary',
                    })
                    ->formatStateUsing(function ($record) {
                        if ($record->isHallSpecific()) {
                            return __('commission-setting.hall_specific', ['name' => $record->hall->name]);
                        } elseif ($record->isOwnerSpecific()) {
                            return __('commission-setting.owner_specific', ['name' => $record->owner->name]);
                        }
                        return __('commission-setting.global');
                    }),

                TextColumn::make('commission_type')
                    ->label(__('commission-setting.commission_type'))
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return $state->value === 'percentage'
                            ? __('commission-setting.percentage')
                            : __('commission-setting.fixed');
                    })
                    ->color(fn($state) => match ($state->value) {
                        'percentage' => 'success',
                        'fixed' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('commission_value')
                    ->label(__('commission-setting.value'))
                    ->formatStateUsing(function ($record) {
                        return $record->commission_type->value === 'percentage'
                            ? $record->commission_value . '%'
                            : number_format($record->commission_value, 3) . ' OMR';
                    })
                    ->sortable(),

                TextColumn::make('effective_from')
                    ->label(__('commission-setting.effective_from'))
                    ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('effective_to')
                    ->label(__('commission-setting.effective_to'))
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->placeholder(__('Indefinite')),

                IconColumn::make('is_active')
                    ->label(__('commission-setting.is_active'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('commission-setting.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('commission_type')
                    ->label(__('commission-setting.commission_type'))
                    ->options([
                        'percentage' => __('commission-setting.percentage'),
                        'fixed' => __('commission-setting.fixed'),
                    ]),

                TernaryFilter::make('is_active')
                    ->label(__('commission-setting.filters.active'))
                    ->boolean()
                    ->native(false),

                Filter::make('scope')
                    ->label(__('commission-setting.scope'))
                    ->schema([
                        Select::make('scope_type')
                            ->label(__('commission-setting.filters.scope_type'))
                            ->options([
                                'global' => __('commission-setting.filters.global'),
                                'owner' => __('commission-setting.filters.owner'),
                                'hall' => __('commission-setting.filters.hall'),
                            ]),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['scope_type'] === 'global', fn($q) => $q->whereNull('hall_id')->whereNull('owner_id'))
                            ->when($data['scope_type'] === 'owner', fn($q) => $q->whereNotNull('owner_id')->whereNull('hall_id'))
                            ->when($data['scope_type'] === 'hall', fn($q) => $q->whereNotNull('hall_id'));
                    }),
            ])
            ->recordActions([
                ActionGroup::make([

                EditAction::make()
                    ->label(__('commission-setting.edit')),
                DeleteAction::make()
                    ->label(__('commission-setting.delete')),
                ViewAction::make(),
                // TODO: ActivityLogTimelineTableAction removed (rmsramos v3-only) - replace with v4 equivalent,
                ])

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => ListCommissionSettings::route('/'),
            'create' => CreateCommissionSetting::route('/create'),
            'edit' => EditCommissionSetting::route('/{record}/edit'),
        ];
    }
}
