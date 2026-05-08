<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\HallFeatureResource\Pages\ListHallFeatures;
use App\Filament\Admin\Resources\HallFeatureResource\Pages\CreateHallFeature;
use App\Filament\Admin\Resources\HallFeatureResource\Pages\EditHallFeature;
use App\Filament\Admin\Resources\HallFeatureResource\Pages;
use App\Models\HallFeature;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HallFeatureResource extends Resource
{
    protected static ?string $model = HallFeature::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-star';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('hall-feature.feature_information'))
                    ->schema([
                        TextInput::make('name.en')
                            ->label(__('hall-feature.name_en'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('name.ar')
                            ->label(__('hall-feature.name_ar'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label(__('hall-feature.slug'))
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText(__('hall-feature.slug_helper')),

                        TextInput::make('icon')
                            ->label(__('hall-feature.icon'))
                            ->maxLength(255)
                            ->helperText(__('hall-feature.icon_helper')),

                        Textarea::make('description.en')
                            ->label(__('hall-feature.description_en'))
                            ->rows(3),

                        Textarea::make('description.ar')
                            ->label(__('hall-feature.description_ar'))
                            ->rows(3),
                    ])->columns(2),

                Section::make(__('hall-feature.settings'))
                    ->schema([
                        TextInput::make('order')
                            ->label(__('hall-feature.order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Toggle::make('is_active')
                            ->label(__('hall-feature.is_active'))
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('hall-feature.name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->name),

                TextColumn::make('slug')
                    ->label(__('hall-feature.slug'))
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('icon')
                    ->label(__('hall-feature.icon'))
                    ->badge()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label(__('hall-feature.is_active'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('order')
                    ->label(__('hall-feature.order'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('hall-feature.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('hall-feature.active'))
                    ->boolean()
                    ->trueLabel(__('hall-feature.active_only'))
                    ->falseLabel(__('hall-feature.inactive_only'))
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label(__('hall-feature.edit')),
                    DeleteAction::make()
                        ->label(__('hall-feature.delete')),
                // TODO: ActivityLogTimelineTableAction removed (rmsramos v3-only) - replace with v4 equivalent,
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListHallFeatures::route('/'),
            'create' => CreateHallFeature::route('/create'),
            'edit' => EditHallFeature::route('/{record}/edit'),
        ];
    }
}
