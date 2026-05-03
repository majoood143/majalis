<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\CityResource\Pages\ListCities;
use App\Filament\Admin\Resources\CityResource\Pages\CreateCity;
use App\Filament\Admin\Resources\CityResource\Pages\ViewCity;
use App\Filament\Admin\Resources\CityResource\Pages\EditCity;
use App\Filament\Admin\Resources\CityResource\Pages;
use App\Models\City;
use App\Models\Region;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office';

    protected static string | \UnitEnum | null $navigationGroup = 'Location Management';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('city.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('city.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('city.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('city.city_information'))
                    ->schema([
                        Select::make('region_id')
                            ->label(__('city.region'))
                            ->options(function () {
                                return Region::all()->mapWithKeys(function ($region) {
                                    return [$region->id => $region->name];
                                });
                            })
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('name.en')
                            ->label(__('city.name_en'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('name.ar')
                            ->label(__('city.name_ar'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('code')
                            ->label(__('city.code'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(10)
                            ->alphaDash(),

                        Textarea::make('description.en')
                            ->label(__('city.description_en'))
                            ->rows(3),

                        Textarea::make('description.ar')
                            ->label(__('city.description_ar'))
                            ->rows(3),
                    ])->columns(2),

                Section::make(__('city.location'))
                    ->schema([
                        TextInput::make('latitude')
                            ->label(__('city.latitude'))
                            ->numeric()
                            ->step(0.0000001)
                            ->minValue(-90)
                            ->maxValue(90),

                        TextInput::make('longitude')
                            ->label(__('city.longitude'))
                            ->numeric()
                            ->step(0.0000001)
                            ->minValue(-180)
                            ->maxValue(180),
                    ])->columns(2),

                Section::make(__('city.settings'))
                    ->schema([
                        TextInput::make('order')
                            ->label(__('city.order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Toggle::make('is_active')
                            ->label(__('city.is_active'))
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('city.name'))
                    ->searchable(query: function ($query, $search) {
                        return $query->where(function ($query) use ($search) {
                            $query->where('name->en', 'like', "%{$search}%")
                                ->orWhere('name->ar', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('name->en', $direction);
                    }),

                TextColumn::make('region.name')
                    ->label(__('city.region_name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->region->name),

                TextColumn::make('code')
                    ->label(__('city.code'))
                    ->searchable()
                    ->badge(),

                TextColumn::make('halls_count')
                    ->counts('halls')
                    ->label(__('city.halls'))
                    ->badge()
                    ->color('info'),

                IconColumn::make('is_active')
                    ->label(__('city.is_active'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('order')
                    ->label(__('city.order'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('city.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('region_id')
                    ->label(__('city.filters.region'))
                    ->relationship('region', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_active')
                    ->label(__('city.filters.active'))
                    ->boolean()
                    ->trueLabel(__('city.filters.active_only'))
                    ->falseLabel(__('city.filters.inactive_only'))
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label(__('city.table_actions.edit')),
                    DeleteAction::make()
                        ->label(__('city.table_actions.delete')),
                ViewAction::make(),
                // TODO: ActivityLogTimelineTableAction removed (rmsramos v3-only) - replace with v4 equivalent,
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order');
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
            'index' => ListCities::route('/'),
            'create' => CreateCity::route('/create'),
            'view' => ViewCity::route('/{record}'),
            'edit' => EditCity::route('/{record}/edit'),
        ];
    }
}
