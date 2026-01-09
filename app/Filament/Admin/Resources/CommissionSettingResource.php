<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CommissionSettingResource\Pages;
use App\Models\CommissionSetting;
use App\Models\Hall;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CommissionSettingResource extends Resource
{
    protected static ?string $model = CommissionSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('commission-setting.commission_scope'))
                    ->description(__('commission-setting.scope_description'))
                    ->schema([
                        Forms\Components\Select::make('hall_id')
                            ->label(__('commission-setting.hall'))
                            ->options(Hall::all()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->helperText(__('commission-setting.hall_helper')),

                        Forms\Components\Select::make('owner_id')
                            ->label(__('commission-setting.owner'))
                            ->options(User::where('role', 'hall_owner')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->helperText(__('commission-setting.owner_helper')),

                        Forms\Components\Placeholder::make('scope_note')
                            ->content(__('commission-setting.scope_note'))
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make(__('commission-setting.commission_details'))
                    ->schema([
                        Forms\Components\TextInput::make('name.en')
                            ->label(__('commission-setting.name_en'))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name.ar')
                            ->label(__('commission-setting.name_ar'))
                            ->maxLength(255),

                        Forms\Components\Select::make('commission_type')
                            ->options([
                                'percentage' => __('commission-setting.percentage'),
                                'fixed' => __('commission-setting.fixed'),
                            ])
                            ->label(__('commission-setting.commission_type'))
                            ->required()
                            ->reactive(),

                        Forms\Components\TextInput::make('commission_value')
                            ->label(__('commission-setting.commission_value'))
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->suffix(fn($get) => $get('commission_type') === 'percentage' ? '%' : 'OMR'),

                        Forms\Components\Textarea::make('description.en')
                            ->label(__('commission-setting.description_en'))
                            ->rows(3),

                        Forms\Components\Textarea::make('description.ar')
                            ->label(__('commission-setting.description_ar'))
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make(__('commission-setting.validity_period'))
                    ->schema([
                        Forms\Components\DatePicker::make('effective_from')
                            ->label(__('commission-setting.effective_from'))
                            ->native(false)
                            ->helperText(__('commission-setting.effective_from_helper')),

                        Forms\Components\DatePicker::make('effective_to')
                            ->label(__('commission-setting.effective_to'))
                            ->native(false)
                            ->helperText(__('commission-setting.effective_to_helper')),

                        Forms\Components\Toggle::make('is_active')
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
                Tables\Columns\TextColumn::make('scope')
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

                Tables\Columns\TextColumn::make('commission_type')
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

                Tables\Columns\TextColumn::make('commission_value')
                    ->label(__('commission-setting.value'))
                    ->formatStateUsing(function ($record) {
                        return $record->commission_type->value === 'percentage'
                            ? $record->commission_value . '%'
                            : number_format($record->commission_value, 3) . ' OMR';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('effective_from')
                    ->label(__('commission-setting.effective_from'))
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('effective_to')
                    ->label(__('commission-setting.effective_to'))
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->placeholder(__('Indefinite')),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('commission-setting.is_active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('commission-setting.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('commission_type')
                    ->label(__('commission-setting.commission_type'))
                    ->options([
                        'percentage' => __('commission-setting.percentage'),
                        'fixed' => __('commission-setting.fixed'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('commission-setting.filters.active'))
                    ->boolean()
                    ->native(false),

                Tables\Filters\Filter::make('scope')
                    ->label(__('commission-setting.scope'))
                    ->form([
                        Forms\Components\Select::make('scope_type')
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
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('commission-setting.edit')),
                Tables\Actions\DeleteAction::make()
                    ->label(__('commission-setting.delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCommissionSettings::route('/'),
            'create' => Pages\CreateCommissionSetting::route('/create'),
            'edit' => Pages\EditCommissionSetting::route('/{record}/edit'),
        ];
    }
}
