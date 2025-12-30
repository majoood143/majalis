<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\HallResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * ExtraServicesRelationManager for Owner Panel
 *
 * Allows hall owners to manage extra services for their halls.
 */
class ExtraServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'extraServices';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('owner.relation.extra_services');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('owner.services.basic_info'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name.en')
                            ->label(__('owner.services.name_en'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name.ar')
                            ->label(__('owner.services.name_ar'))
                            ->required()
                            ->maxLength(255)
                            ->extraInputAttributes(['dir' => 'rtl']),

                        Forms\Components\Textarea::make('description.en')
                            ->label(__('owner.services.description_en'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description.ar')
                            ->label(__('owner.services.description_ar'))
                            ->rows(3)
                            ->extraInputAttributes(['dir' => 'rtl'])
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(__('owner.services.pricing'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label(__('owner.services.price'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(0.001)
                            ->prefix('OMR'),

                        Forms\Components\Select::make('pricing_unit')
                            ->label(__('owner.services.pricing_unit'))
                            ->required()
                            ->options([
                                'fixed' => __('owner.services.units.fixed'),
                                'per_person' => __('owner.services.units.per_person'),
                                'per_hour' => __('owner.services.units.per_hour'),
                                'per_item' => __('owner.services.units.per_item'),
                                'per_table' => __('owner.services.units.per_table'),
                                'per_plate' => __('owner.services.units.per_plate'),
                            ])
                            ->default('fixed'),

                        Forms\Components\TextInput::make('min_quantity')
                            ->label(__('owner.services.min_quantity'))
                            ->numeric()
                            ->minValue(1)
                            ->default(1),

                        Forms\Components\TextInput::make('max_quantity')
                            ->label(__('owner.services.max_quantity'))
                            ->numeric()
                            ->minValue(1)
                            ->nullable(),
                    ]),

                Forms\Components\Section::make(__('owner.services.settings'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('owner.services.is_active'))
                            ->default(true),

                        Forms\Components\Toggle::make('is_required')
                            ->label(__('owner.services.is_required'))
                            ->default(false)
                            ->helperText(__('owner.services.is_required_help')),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('owner.services.name'))
                    ->formatStateUsing(fn ($record) => $record->getTranslation('name', app()->getLocale()))
                    ->searchable(['name->en', 'name->ar'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label(__('owner.services.price'))
                    ->money('OMR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pricing_unit')
                    ->label(__('owner.services.pricing_unit'))
                    ->formatStateUsing(fn (string $state): string => __("owner.services.units.{$state}"))
                    ->badge(),

                Tables\Columns\IconColumn::make('is_required')
                    ->label(__('owner.services.required'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('owner.services.active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('owner.services.active')),

                Tables\Filters\TernaryFilter::make('is_required')
                    ->label(__('owner.services.required')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle')
                    ->label(fn ($record): string => $record->is_active 
                        ? __('owner.services.deactivate') 
                        : __('owner.services.activate'))
                    ->icon(fn ($record): string => $record->is_active 
                        ? 'heroicon-o-pause' 
                        : 'heroicon-o-play')
                    ->color(fn ($record): string => $record->is_active ? 'warning' : 'success')
                    ->action(fn ($record) => $record->update(['is_active' => !$record->is_active])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
