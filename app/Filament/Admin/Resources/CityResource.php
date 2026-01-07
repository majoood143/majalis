<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CityResource\Pages;
use App\Models\City;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Location Management';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('city.city_information'))
                    ->schema([
                        Forms\Components\Select::make('region_id')
                            ->label(__('city.region'))
                            ->options(function () {
                                return Region::all()->mapWithKeys(function ($region) {
                                    return [$region->id => $region->name];
                                });
                            })
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('name.en')
                            ->label(__('city.name_en'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name.ar')
                            ->label(__('city.name_ar'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label(__('city.code'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(10)
                            ->alphaDash(),

                        Forms\Components\Textarea::make('description.en')
                            ->label(__('city.description_en'))
                            ->rows(3),

                        Forms\Components\Textarea::make('description.ar')
                            ->label(__('city.description_ar'))
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make(__('city.location'))
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->label(__('city.latitude'))
                            ->numeric()
                            ->step(0.0000001)
                            ->minValue(-90)
                            ->maxValue(90),

                        Forms\Components\TextInput::make('longitude')
                            ->label(__('city.longitude'))
                            ->numeric()
                            ->step(0.0000001)
                            ->minValue(-180)
                            ->maxValue(180),
                    ])->columns(2),

                Forms\Components\Section::make(__('city.settings'))
                    ->schema([
                        Forms\Components\TextInput::make('order')
                            ->label(__('city.order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Forms\Components\Toggle::make('is_active')
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
                Tables\Columns\TextColumn::make('name')
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

                Tables\Columns\TextColumn::make('region.name')
                    ->label(__('city.region_name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->region->name),

                Tables\Columns\TextColumn::make('code')
                    ->label(__('city.code'))
                    ->searchable()
                    ->badge(),

                Tables\Columns\TextColumn::make('halls_count')
                    ->counts('halls')
                    ->label(__('city.halls'))
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('city.is_active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order')
                    ->label(__('city.order'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('city.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('region_id')
                    ->label(__('city.filters.region'))
                    ->relationship('region', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('city.filters.active'))
                    ->boolean()
                    ->trueLabel(__('city.filters.active_only'))
                    ->falseLabel(__('city.filters.inactive_only'))
                    ->native(false),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label(__('city.table_actions.edit')),
                    Tables\Actions\DeleteAction::make()
                        ->label(__('city.table_actions.delete')),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}