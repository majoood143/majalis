<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\HallResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('owner.services.basic_info'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('name.en')
                            ->label(__('owner.services.name_en'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('name.ar')
                            ->label(__('owner.services.name_ar'))
                            ->required()
                            ->maxLength(255)
                            ->extraInputAttributes(['dir' => 'rtl']),

                        Textarea::make('description.en')
                            ->label(__('owner.services.description_en'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('description.ar')
                            ->label(__('owner.services.description_ar'))
                            ->rows(3)
                            ->extraInputAttributes(['dir' => 'rtl'])
                            ->columnSpanFull(),
                    ]),

                Section::make(__('owner.services.pricing'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('price')
                            ->label(__('owner.services.price'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(0.001)
                            ->prefix('OMR'),

                        Select::make('pricing_unit')
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

                        TextInput::make('min_quantity')
                            ->label(__('owner.services.min_quantity'))
                            ->numeric()
                            ->minValue(1)
                            ->default(1),

                        TextInput::make('max_quantity')
                            ->label(__('owner.services.max_quantity'))
                            ->numeric()
                            ->minValue(1)
                            ->nullable(),
                    ]),

                Section::make(__('owner.services.settings'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label(__('owner.services.is_active'))
                            ->default(true),

                        Toggle::make('is_required')
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
                TextColumn::make('name')
                    ->label(__('owner.services.name'))
                    ->formatStateUsing(fn ($record) => $record->getTranslation('name', app()->getLocale()))
                    ->searchable(['name->en', 'name->ar'])
                    ->sortable(),

                TextColumn::make('price')
                    ->label(__('owner.services.price'))
                    ->money('OMR')
                    ->sortable(),

                TextColumn::make('pricing_unit')
                    ->label(__('owner.services.pricing_unit'))
                    ->formatStateUsing(fn (string $state): string => __("owner.services.units.{$state}"))
                    ->badge(),

                IconColumn::make('is_required')
                    ->label(__('owner.services.required'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                IconColumn::make('is_active')
                    ->label(__('owner.services.active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('owner.services.active')),

                TernaryFilter::make('is_required')
                    ->label(__('owner.services.required')),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('toggle')
                    ->label(fn ($record): string => $record->is_active 
                        ? __('owner.services.deactivate') 
                        : __('owner.services.activate'))
                    ->icon(fn ($record): string => $record->is_active 
                        ? 'heroicon-o-pause' 
                        : 'heroicon-o-play')
                    ->color(fn ($record): string => $record->is_active ? 'warning' : 'success')
                    ->action(fn ($record) => $record->update(['is_active' => !$record->is_active])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
