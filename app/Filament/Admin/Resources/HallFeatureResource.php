<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HallFeatureResource\Pages;
use App\Models\HallFeature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;

class HallFeatureResource extends Resource
{
    protected static ?string $model = HallFeature::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    //protected static ?string $navigationGroup = 'Hall Management';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.hall_navigation_group');
    }

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('hall-feature.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('hall-feature.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('hall-feature.navigation_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('hall-feature.feature_information'))
                    ->schema([
                        Forms\Components\TextInput::make('name.en')
                            ->label(__('hall-feature.name_en'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name.ar')
                            ->label(__('hall-feature.name_ar'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->label(__('hall-feature.slug'))
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText(__('hall-feature.slug_helper')),

                        Forms\Components\TextInput::make('icon')
                            ->label(__('hall-feature.icon'))
                            ->maxLength(255)
                            ->helperText(__('hall-feature.icon_helper')),

                        Forms\Components\Textarea::make('description.en')
                            ->label(__('hall-feature.description_en'))
                            ->rows(3),

                        Forms\Components\Textarea::make('description.ar')
                            ->label(__('hall-feature.description_ar'))
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make(__('hall-feature.settings'))
                    ->schema([
                        Forms\Components\TextInput::make('order')
                            ->label(__('hall-feature.order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('hall-feature.is_active'))
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
                    ->label(__('hall-feature.name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->name),

                Tables\Columns\TextColumn::make('slug')
                    ->label(__('hall-feature.slug'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('icon')
                    ->label(__('hall-feature.icon'))
                    ->badge()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('hall-feature.is_active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order')
                    ->label(__('hall-feature.order'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('hall-feature.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('hall-feature.active'))
                    ->boolean()
                    ->trueLabel(__('hall-feature.active_only'))
                    ->falseLabel(__('hall-feature.inactive_only'))
                    ->native(false),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label(__('hall-feature.edit')),
                    Tables\Actions\DeleteAction::make()
                        ->label(__('hall-feature.delete')),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order')
            ->reorderable('order');
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
            'index' => Pages\ListHallFeatures::route('/'),
            'create' => Pages\CreateHallFeature::route('/create'),
            'edit' => Pages\EditHallFeature::route('/{record}/edit'),
        ];
    }
}
