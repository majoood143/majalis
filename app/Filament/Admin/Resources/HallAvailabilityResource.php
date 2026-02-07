<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HallAvailabilityResource\Pages;
use App\Models\HallAvailability;
use App\Models\Hall;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;

class HallAvailabilityResource extends Resource
{
    protected static ?string $model = HallAvailability::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    //protected static ?string $navigationGroup = 'Hall Management';

    protected static ?int $navigationSort = 5;

    //protected static ?string $label = 'Hall Availability';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.hall_navigation_group');
    }

    public static function getModelLabel(): string
    {
        return __('hall-availability.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('hall-availability.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('hall-availability.navigation_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('hall-availability.availability_details'))
                    ->schema([
                        Forms\Components\Select::make('hall_id')
                            ->label(__('hall-availability.hall'))
                            ->relationship('hall', 'name')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->translated_name ?? __('hall-availability.unnamed_hall'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('date')
                            ->label(__('hall-availability.date'))
                            ->required()
                            ->native(false)
                            ->minDate(now()),

                        Forms\Components\Select::make('time_slot')
                            ->label(__('hall-availability.time_slot'))
                            ->options([
                                'morning' => __('hall-availability.time_slots.morning'),
                                'afternoon' => __('hall-availability.time_slots.afternoon'),
                                'evening' => __('hall-availability.time_slots.evening'),
                                'full_day' => __('hall-availability.time_slots.full_day'),
                            ])
                            ->required(),

                        Forms\Components\Toggle::make('is_available')
                            ->label(__('hall-availability.is_available'))
                            ->default(true)
                            ->inline(false)
                            ->helperText(__('hall-availability.is_available_helper')),
                    ])->columns(2),

                Forms\Components\Section::make(__('hall-availability.block_reason'))
                    ->schema([
                        Forms\Components\Select::make('reason')
                            ->label(__('hall-availability.reason'))
                            ->options([
                                'maintenance' => __('hall-availability.reasons.maintenance'),
                                'blocked' => __('hall-availability.reasons.blocked'),
                                'holiday' => __('hall-availability.reasons.holiday'),
                                'custom' => __('hall-availability.reasons.custom'),
                            ])
                            ->visible(fn($get) => !$get('is_available')),

                        Forms\Components\Textarea::make('notes')
                            ->label(__('hall-availability.notes'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(1)
                    ->visible(fn($get) => !$get('is_available')),

                Forms\Components\Section::make(__('hall-availability.custom_pricing'))
                    ->description(__('hall-availability.custom_pricing_description'))
                    ->schema([
                        Forms\Components\TextInput::make('custom_price')
                            ->label(__('hall-availability.custom_price'))
                            ->numeric()
                            ->prefix('OMR')
                            ->step(0.001)
                            ->helperText(__('hall-availability.custom_price_helper')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hall.name')
                    ->label(__('hall-availability.hall_name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        if (!$record->hall) {
                            return __('hall-availability.hall_deleted');
                        }
                        return $record->hall->translated_name ?? __('hall-availability.unnamed_hall');
                    })
                    ->badge()
                    ->color(fn($record) => $record->hall ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('hall-availability.date'))
                    ->date()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('time_slot')
                    ->label(__('hall-availability.time_slot_label'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'morning' => 'info',
                        'afternoon' => 'warning',
                        'evening' => 'success',
                        'full_day' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst(str_replace('_', ' ', $state))),

                Tables\Columns\IconColumn::make('is_available')
                    ->label(__('hall-availability.is_available'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reason_label')
                    ->label(__('hall-availability.reason_label'))
                    ->badge()
                    ->color('danger')
                    ->visible(fn($record) => $record && !$record->is_available),

                Tables\Columns\TextColumn::make('custom_price')
                    ->label(__('hall-availability.custom_price'))
                    ->money('OMR')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('effective_price')
                    ->label(__('hall-availability.effective_price'))
                    ->money('OMR')
                    ->formatStateUsing(function ($record) {
                        if (!$record->hall) {
                            return __('hall-availability.not_applicable');
                        }
                        return $record->getEffectivePrice();
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('hall-availability.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hall_id')
                    ->label(__('hall-availability.filters.hall'))
                    ->relationship('hall', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('time_slot')
                    ->label(__('hall-availability.filters.time_slot'))
                    ->options([
                        'morning' => __('hall-availability.time_slots_short.morning'),
                        'afternoon' => __('hall-availability.time_slots_short.afternoon'),
                        'evening' => __('hall-availability.time_slots_short.evening'),
                        'full_day' => __('hall-availability.time_slots_short.full_day'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_available')
                    ->label(__('hall-availability.filters.available'))
                    ->boolean()
                    ->trueLabel(__('hall-availability.filters.available_only'))
                    ->falseLabel(__('hall-availability.filters.blocked_only'))
                    ->native(false),

                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(__('hall-availability.filters.from'))
                            ->native(false),
                        Forms\Components\DatePicker::make('until')
                            ->label(__('hall-availability.filters.until'))
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($query, $date) => $query->whereDate('date', '>=', $date))
                            ->when($data['until'], fn($query, $date) => $query->whereDate('date', '<=', $date));
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label(__('hall-availability.table_actions.view')),
                    Tables\Actions\EditAction::make()
                        ->label(__('hall-availability.table_actions.edit')),
                    Tables\Actions\DeleteAction::make()
                        ->label(__('hall-availability.table_actions.delete')),

                    Tables\Actions\Action::make('toggle')
                        ->label(fn($record) => $record->is_available ?
                            __('hall-availability.table_actions.block') :
                            __('hall-availability.table_actions.unblock'))
                        ->icon(fn($record) => $record->is_available ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                        ->color(fn($record) => $record->is_available ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $record->update([
                                'is_available' => !$record->is_available,
                                'reason' => !$record->is_available ? 'blocked' : null,
                                'notes' => !$record->is_available ? $record->notes : null,
                            ]);
                        }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('block')
                        ->label(__('hall-availability.bulk_actions.block_selected'))
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Select::make('reason')
                                ->label(__('hall-availability.reason'))
                                ->options([
                                    'maintenance' => __('hall-availability.reasons.maintenance'),
                                    'blocked' => __('hall-availability.reasons.blocked'),
                                    'holiday' => __('hall-availability.reasons.holiday'),
                                    'custom' => __('hall-availability.reasons.custom'),
                                ])
                                ->required(),
                            Forms\Components\Textarea::make('notes')
                                ->label(__('hall-availability.notes'))
                                ->rows(3),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each->update([
                                'is_available' => false,
                                'reason' => $data['reason'] ?? 'blocked',
                                'notes' => $data['notes'] ?? null,
                            ]);
                        }),

                    Tables\Actions\BulkAction::make('unblock')
                        ->label(__('hall-availability.bulk_actions.unblock_selected'))
                        ->icon('heroicon-o-lock-open')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->update([
                            'is_available' => true,
                            'reason' => null,
                            'notes' => null,
                        ])),
                ]),
            ])
            ->defaultSort('date', 'asc');
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
            'index' => Pages\ListHallAvailabilities::route('/'),
            'create' => Pages\CreateHallAvailability::route('/create'),
            'edit' => Pages\EditHallAvailability::route('/{record}/edit'),
            'view' => Pages\ViewHallAvailability::route('/{record}'),
        ];
    }
}
