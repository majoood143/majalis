<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkAction;
use App\Filament\Admin\Resources\HallAvailabilityResource\Pages\ListHallAvailabilities;
use App\Filament\Admin\Resources\HallAvailabilityResource\Pages\CreateHallAvailability;
use App\Filament\Admin\Resources\HallAvailabilityResource\Pages\EditHallAvailability;
use App\Filament\Admin\Resources\HallAvailabilityResource\Pages\ViewHallAvailability;
use App\Filament\Admin\Resources\HallAvailabilityResource\Pages;
use App\Models\HallAvailability;
use App\Models\Hall;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;



class HallAvailabilityResource extends Resource
{
    protected static ?string $model = HallAvailability::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('hall-availability.availability_details'))
                    ->schema([
                        Select::make('hall_id')
                            ->label(__('hall-availability.hall'))
                            ->relationship('hall', 'name')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->translated_name ?? __('hall-availability.unnamed_hall'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        DatePicker::make('date')
                            ->label(__('hall-availability.date'))
                            ->required()
                            ->native(false)
                            ->minDate(now()),

                        Select::make('time_slot')
                            ->label(__('hall-availability.time_slot'))
                            ->options([
                                'morning' => __('hall-availability.time_slots.morning'),
                                'afternoon' => __('hall-availability.time_slots.afternoon'),
                                'evening' => __('hall-availability.time_slots.evening'),
                                'full_day' => __('hall-availability.time_slots.full_day'),
                            ])
                            ->required(),

                        Toggle::make('is_available')
                            ->label(__('hall-availability.is_available'))
                            ->default(true)
                            ->inline(false)
                            ->helperText(__('hall-availability.is_available_helper')),
                    ])->columns(2),

                Section::make(__('hall-availability.block_reason'))
                    ->schema([
                        Select::make('reason')
                            ->label(__('hall-availability.reason'))
                            ->options([
                                'maintenance' => __('hall-availability.reasons.maintenance'),
                                'blocked' => __('hall-availability.reasons.blocked'),
                                'holiday' => __('hall-availability.reasons.holiday'),
                                'custom' => __('hall-availability.reasons.custom'),
                            ])
                            ->visible(fn($get) => !$get('is_available')),

                        Textarea::make('notes')
                            ->label(__('hall-availability.notes'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(1)
                    ->visible(fn($get) => !$get('is_available')),

                Section::make(__('hall-availability.custom_pricing'))
                    ->description(__('hall-availability.custom_pricing_description'))
                    ->schema([
                        TextInput::make('custom_price')
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
                TextColumn::make('hall.name')
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

                TextColumn::make('date')
                    ->label(__('hall-availability.date'))
                    ->date()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('time_slot')
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

                IconColumn::make('is_available')
                    ->label(__('hall-availability.is_available'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('reason_label')
                    ->label(__('hall-availability.reason_label'))
                    ->badge()
                    ->color('danger')
                    ->visible(fn($record) => $record && !$record->is_available),

                TextColumn::make('custom_price')
                    ->label(__('hall-availability.custom_price'))
                    ->money('OMR')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('effective_price')
                    ->label(__('hall-availability.effective_price'))
                    ->state(function ($record) {
                        if (!$record->hall) {
                            return __('hall-availability.not_applicable');
                        }
                        return number_format($record->getEffectivePrice(), 3) . ' OMR';
                    })
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('hall-availability.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('hall_id')
                    ->label(__('hall-availability.filters.hall'))
                    ->relationship('hall', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('time_slot')
                    ->label(__('hall-availability.filters.time_slot'))
                    ->options([
                        'morning' => __('hall-availability.time_slots_short.morning'),
                        'afternoon' => __('hall-availability.time_slots_short.afternoon'),
                        'evening' => __('hall-availability.time_slots_short.evening'),
                        'full_day' => __('hall-availability.time_slots_short.full_day'),
                    ]),

                TernaryFilter::make('is_available')
                    ->label(__('hall-availability.filters.available'))
                    ->boolean()
                    ->trueLabel(__('hall-availability.filters.available_only'))
                    ->falseLabel(__('hall-availability.filters.blocked_only'))
                    ->native(false),

                Filter::make('date')
                    ->schema([
                        DatePicker::make('from')
                            ->label(__('hall-availability.filters.from'))
                            ->native(false),
                        DatePicker::make('until')
                            ->label(__('hall-availability.filters.until'))
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($query, $date) => $query->whereDate('date', '>=', $date))
                            ->when($data['until'], fn($query, $date) => $query->whereDate('date', '<=', $date));
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label(__('hall-availability.table_actions.view')),
                    EditAction::make()
                        ->label(__('hall-availability.table_actions.edit')),
                    DeleteAction::make()
                        ->label(__('hall-availability.table_actions.delete')),

                    Action::make('toggle')
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

                // TODO: ActivityLogTimelineTableAction removed (rmsramos v3-only) - replace with v4 equivalent,
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('block')
                        ->label(__('hall-availability.bulk_actions.block_selected'))
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->schema([
                            Select::make('reason')
                                ->label(__('hall-availability.reason'))
                                ->options([
                                    'maintenance' => __('hall-availability.reasons.maintenance'),
                                    'blocked' => __('hall-availability.reasons.blocked'),
                                    'holiday' => __('hall-availability.reasons.holiday'),
                                    'custom' => __('hall-availability.reasons.custom'),
                                ])
                                ->required(),
                            Textarea::make('notes')
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

                    BulkAction::make('unblock')
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
            'index' => ListHallAvailabilities::route('/'),
            'create' => CreateHallAvailability::route('/create'),
            'edit' => EditHallAvailability::route('/{record}/edit'),
            'view' => ViewHallAvailability::route('/{record}'),
        ];
    }
}
