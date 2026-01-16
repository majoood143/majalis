<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ExtraServiceResource\Pages;
use App\Models\ExtraService;
use App\Models\Hall;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;

class ExtraServiceResource extends Resource
{
    protected static ?string $model = ExtraService::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    //protected static ?string $navigationGroup = 'Hall Management';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.hall_navigation_group');
    }

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return __('extra-service.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('extra-service.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('extra-service.navigation_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('extra-service.service_information'))
                    ->schema([
                        Forms\Components\Select::make('hall_id')
                            ->label(__('extra-service.hall'))
                            ->options(function () {
                                return Hall::with(['city', 'owner'])
                                    ->get()
                                    ->mapWithKeys(function ($hall) {
                                        // Get hall name with fallback
                                        $hallName = $hall->name ?? __('extra-service.unnamed_hall');

                                        // Get city name with fallback
                                        $cityName = $hall->city->name ?? __('extra-service.unknown_city');

                                        // Get owner name with fallback
                                        $ownerName = $hall->owner->name ?? __('extra-service.no_owner');

                                        // Format label using translation
                                        $label = __('extra-service.hall_label_format', [
                                            'hall_name' => $hallName,
                                            'city_name' => $cityName,
                                            'owner_name' => $ownerName,
                                        ]);

                                        return [$hall->id => $label];
                                    });
                            })
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('name.en')
                            ->label(__('extra-service.name_en'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name.ar')
                            ->label(__('extra-service.name_ar'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\RichEditor::make('description.en')
                            ->label(__('extra-service.description_en'))
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('description.ar')
                            ->label(__('extra-service.description_ar'))
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make(__('extra-service.pricing'))
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label(__('extra-service.price'))
                            ->numeric()
                            ->required()
                            ->prefix('OMR')
                            ->step(0.001),

                        Forms\Components\Select::make('unit')
                            ->label(__('extra-service.unit'))
                            ->options([
                                'per_person' => __('extra-service.units.per_person'),
                                'per_item' => __('extra-service.units.per_item'),
                                'per_hour' => __('extra-service.units.per_hour'),
                                'fixed' => __('extra-service.units.fixed'),
                            ])
                            ->default('fixed'),

                        Forms\Components\TextInput::make('minimum_quantity')
                            ->label(__('extra-service.minimum_quantity'))
                            ->numeric()
                            ->default(1)
                            ->minValue(1),

                        Forms\Components\TextInput::make('maximum_quantity')
                            ->label(__('extra-service.maximum_quantity'))
                            ->numeric()
                            ->minValue(1)
                            ->helperText(__('extra-service.maximum_quantity_helper')),
                    ])->columns(2),

                Forms\Components\Section::make(__('extra-service.media'))
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label(__('extra-service.image'))
                            ->image()
                            ->directory('services')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(__('extra-service.settings'))
                    ->schema([
                        Forms\Components\TextInput::make('order')
                            ->label(__('extra-service.order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('extra-service.is_active'))
                            ->default(true)
                            ->inline(false),

                        Forms\Components\Toggle::make('is_required')
                            ->label(__('extra-service.is_required'))
                            ->helperText(__('extra-service.is_required_helper'))
                            ->inline(false),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('extra-service.name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->name),

                Tables\Columns\TextColumn::make('hall.name')
                    ->label(__('extra-service.hall_name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->hall->name),

                Tables\Columns\TextColumn::make('price')
                    ->label(__('extra-service.price'))
                    ->money('OMR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit')
                    ->label(__('extra-service.unit_label'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'per_person' => __('extra-service.units.per_person'),
                        'per_item' => __('extra-service.units.per_item'),
                        'per_hour' => __('extra-service.units.per_hour'),
                        'fixed' => __('extra-service.units.fixed'),
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('is_required')
                    ->label(__('extra-service.required'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('extra-service.active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order')
                    ->label(__('extra-service.order'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hall_id')
                    ->label(__('extra-service.filters.hall'))
                    ->relationship('hall', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('unit')
                    ->label(__('extra-service.filters.unit'))
                    ->options([
                        'per_person' => __('extra-service.units.per_person'),
                        'per_item' => __('extra-service.units.per_item'),
                        'per_hour' => __('extra-service.units.per_hour'),
                        'fixed' => __('extra-service.units.fixed'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('extra-service.filters.active'))
                    ->boolean()
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_required')
                    ->label(__('extra-service.filters.required'))
                    ->boolean()
                    ->native(false),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label(__('extra-service.edit')),
                    Tables\Actions\DeleteAction::make()
                        ->label(__('extra-service.delete')),
                ])
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
            'index' => Pages\ListExtraServices::route('/'),
            'create' => Pages\CreateExtraService::route('/create'),
            'edit' => Pages\EditExtraService::route('/{record}/edit'),
        ];
    }
}
