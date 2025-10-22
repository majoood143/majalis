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

class HallAvailabilityResource extends Resource
{
    protected static ?string $model = HallAvailability::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Hall Management';

    protected static ?int $navigationSort = 5;

    protected static ?string $label = 'Hall Availability';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Availability Details')
                    ->schema([
                        Forms\Components\Select::make('hall_id')
                            ->label('Hall')
                            ->options(Hall::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('date')
                            ->required()
                            ->native(false)
                            ->minDate(now()),

                        Forms\Components\Select::make('time_slot')
                            ->options([
                                'morning' => 'Morning (8 AM - 12 PM)',
                                'afternoon' => 'Afternoon (12 PM - 5 PM)',
                                'evening' => 'Evening (5 PM - 11 PM)',
                                'full_day' => 'Full Day (8 AM - 11 PM)',
                            ])
                            ->required(),

                        Forms\Components\Toggle::make('is_available')
                            ->label('Available')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Uncheck to block this slot'),
                    ])->columns(2),

                Forms\Components\Section::make('Block Reason')
                    ->schema([
                        Forms\Components\Select::make('reason')
                            ->options([
                                'maintenance' => 'Under Maintenance',
                                'blocked' => 'Blocked by Owner',
                                'holiday' => 'Holiday',
                                'custom' => 'Custom Block',
                            ])
                            ->visible(fn($get) => !$get('is_available')),

                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(1)
                    ->visible(fn($get) => !$get('is_available')),

                Forms\Components\Section::make('Custom Pricing')
                    ->description('Override the default hall pricing for this specific date and slot')
                    ->schema([
                        Forms\Components\TextInput::make('custom_price')
                            ->label('Custom Price')
                            ->numeric()
                            ->prefix('OMR')
                            ->step(0.001)
                            ->helperText('Leave empty to use default pricing'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hall.name')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->hall->name),

                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('time_slot')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'morning' => 'info',
                        'afternoon' => 'warning',
                        'evening' => 'success',
                        'full_day' => 'primary',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst(str_replace('_', ' ', $state))),

                Tables\Columns\IconColumn::make('is_available')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reason_label')
                    ->label('Reason')
                    ->badge()
                    ->color('danger')
                    ->visible(fn($record) => !$record->is_available),

                Tables\Columns\TextColumn::make('custom_price')
                    ->money('OMR')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('effective_price')
                    ->label('Effective Price')
                    ->money('OMR')
                    ->formatStateUsing(fn($record) => $record->getEffectivePrice())
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hall_id')
                    ->relationship('hall', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('time_slot')
                    ->options([
                        'morning' => 'Morning',
                        'afternoon' => 'Afternoon',
                        'evening' => 'Evening',
                        'full_day' => 'Full Day',
                    ]),

                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Available')
                    ->boolean()
                    ->trueLabel('Available only')
                    ->falseLabel('Blocked only')
                    ->native(false),

                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->native(false),
                        Forms\Components\DatePicker::make('until')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('date', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('date', '<=', $data['until']));
                    }),

                Tables\Filters\TernaryFilter::make('custom_price')
                    ->label('Has Custom Price')
                    ->queries(
                        true: fn($query) => $query->whereNotNull('custom_price'),
                        false: fn($query) => $query->whereNull('custom_price'),
                    )
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('block')
                        ->label('Block Selected')
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Select::make('reason')
                                ->label('Block Reason')
                                ->options([
                                    'maintenance' => 'Under Maintenance',
                                    'blocked' => 'Blocked by Owner',
                                    'holiday' => 'Holiday',
                                    'custom' => 'Custom Block',
                                ])
                                ->required(),
                            Forms\Components\Textarea::make('notes')
                                ->label('Notes'),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update([
                                    'is_available' => false,
                                    'reason' => $data['reason'],
                                    'notes' => $data['notes'] ?? null,
                                ]);
                            });
                        }),

                    Tables\Actions\BulkAction::make('unblock')
                        ->label('Unblock Selected')
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
        ];
    }
}
