<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\ExtraServiceResource\Pages\ListExtraServices;
use App\Filament\Admin\Resources\ExtraServiceResource\Pages\CreateExtraService;
use App\Filament\Admin\Resources\ExtraServiceResource\Pages\ViewExtraService;
use App\Filament\Admin\Resources\ExtraServiceResource\Pages\EditExtraService;
use App\Filament\Admin\Resources\ExtraServiceResource\Pages;
use App\Models\ExtraService;
use App\Models\Hall;
use Filament\Forms;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class ExtraServiceResource extends Resource
{
    protected static ?string $model = ExtraService::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-gift';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('extra-service.service_information'))
                    ->schema([
                        Select::make('hall_id')
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

                        TextInput::make('name.en')
                            ->label(__('extra-service.name_en'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('name.ar')
                            ->label(__('extra-service.name_ar'))
                            ->required()
                            ->maxLength(255),

                        RichEditor::make('description.en')
                            ->label(__('extra-service.description_en'))
                            ->required()
                            ->columnSpanFull(),

                        RichEditor::make('description.ar')
                            ->label(__('extra-service.description_ar'))
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make(__('extra-service.pricing'))
                    ->schema([
                        TextInput::make('price')
                            ->label(__('extra-service.price'))
                            ->numeric()
                            ->required()
                            ->prefix('OMR')
                            ->step(0.001),

                        Select::make('unit')
                            ->label(__('extra-service.unit'))
                            ->options([
                                'per_person' => __('extra-service.units.per_person'),
                                'per_item' => __('extra-service.units.per_item'),
                                'per_hour' => __('extra-service.units.per_hour'),
                                'fixed' => __('extra-service.units.fixed'),
                            ])
                            ->default('fixed'),

                        TextInput::make('minimum_quantity')
                            ->label(__('extra-service.minimum_quantity'))
                            ->numeric()
                            ->default(1)
                            ->minValue(1),

                        TextInput::make('maximum_quantity')
                            ->label(__('extra-service.maximum_quantity'))
                            ->numeric()
                            ->minValue(1)
                            ->helperText(__('extra-service.maximum_quantity_helper')),
                    ])->columns(2),

                Section::make(__('extra-service.media'))
                    ->schema([
                        FileUpload::make('image')
                            ->label(__('extra-service.image'))
                            ->image()
                            ->directory('services')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('extra-service.settings'))
                    ->schema([
                        TextInput::make('order')
                            ->label(__('extra-service.order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Toggle::make('is_active')
                            ->label(__('extra-service.is_active'))
                            ->default(true)
                            ->inline(false),

                        Toggle::make('is_required')
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
                ImageColumn::make('image')
                    ->circular(),

                TextColumn::make('name')
                    ->label(__('extra-service.name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->name),

                TextColumn::make('hall.name')
                    ->label(__('extra-service.hall_name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->hall->name),

                TextColumn::make('price')
                    ->label(__('extra-service.price'))
                    ->money('OMR')
                    ->sortable(),

                TextColumn::make('unit')
                    ->label(__('extra-service.unit_label'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'per_person' => __('extra-service.units.per_person'),
                        'per_item' => __('extra-service.units.per_item'),
                        'per_hour' => __('extra-service.units.per_hour'),
                        'fixed' => __('extra-service.units.fixed'),
                        default => $state,
                    }),

                IconColumn::make('is_required')
                    ->label(__('extra-service.required'))
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('extra-service.active'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('order')
                    ->label(__('extra-service.order'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('hall_id')
                    ->label(__('extra-service.filters.hall'))
                    ->relationship('hall', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('unit')
                    ->label(__('extra-service.filters.unit'))
                    ->options([
                        'per_person' => __('extra-service.units.per_person'),
                        'per_item' => __('extra-service.units.per_item'),
                        'per_hour' => __('extra-service.units.per_hour'),
                        'fixed' => __('extra-service.units.fixed'),
                    ]),

                TernaryFilter::make('is_active')
                    ->label(__('extra-service.filters.active'))
                    ->boolean()
                    ->native(false),

                TernaryFilter::make('is_required')
                    ->label(__('extra-service.filters.required'))
                    ->boolean()
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label(__('extra-service.view')),
                    EditAction::make()
                        ->label(__('extra-service.edit')),
                    DeleteAction::make()
                        ->label(__('extra-service.delete')),
                // TODO: ActivityLogTimelineTableAction removed (rmsramos v3-only) - replace with v4 equivalent,
                ])
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
            'index' => ListExtraServices::route('/'),
            'create' => CreateExtraService::route('/create'),
            'view' => ViewExtraService::route('/{record}'),
            'edit' => EditExtraService::route('/{record}/edit'),
        ];
    }
}
