<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\HallResource\RelationManagers;

use App\Models\Hall;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * AvailabilitiesRelationManager for Owner Panel
 *
 * Allows hall owners to manage availability slots for their halls.
 */
class AvailabilitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'availabilities';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('owner.relation.availabilities');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label(__('owner.availability.date'))
                    ->required()
                    ->native(false)
                    ->minDate(now()),

                Forms\Components\Select::make('time_slot')
                    ->label(__('owner.availability.time_slot'))
                    ->required()
                    ->options([
                        'morning' => __('owner.slots.morning'),
                        'afternoon' => __('owner.slots.afternoon'),
                        'evening' => __('owner.slots.evening'),
                        'full_day' => __('owner.slots.full_day'),
                    ]),

                Forms\Components\Toggle::make('is_available')
                    ->label(__('owner.availability.is_available'))
                    ->default(true),

                Forms\Components\Select::make('reason')
                    ->label(__('owner.availability.reason'))
                    ->options([
                        'blocked' => __('owner.availability.reasons.blocked'),
                        'maintenance' => __('owner.availability.reasons.maintenance'),
                        'holiday' => __('owner.availability.reasons.holiday'),
                        'private_event' => __('owner.availability.reasons.private_event'),
                        'renovation' => __('owner.availability.reasons.renovation'),
                        'other' => __('owner.availability.reasons.other'),
                    ])
                    ->visible(fn (Forms\Get $get): bool => !$get('is_available')),

                Forms\Components\TextInput::make('custom_price')
                    ->label(__('owner.availability.custom_price'))
                    ->numeric()
                    ->minValue(0)
                    ->step(0.001)
                    ->prefix('OMR'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->defaultSort('date', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label(__('owner.availability.date'))
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('time_slot')
                    ->label(__('owner.availability.time_slot'))
                    ->formatStateUsing(fn (string $state): string => __("owner.slots.{$state}"))
                    ->badge(),

                Tables\Columns\IconColumn::make('is_available')
                    ->label(__('owner.availability.status'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('reason')
                    ->label(__('owner.availability.reason'))
                    ->formatStateUsing(fn (?string $state): string => $state ? __("owner.availability.reasons.{$state}") : '-')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'booked' => 'info',
                        'maintenance' => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('custom_price')
                    ->label(__('owner.availability.custom_price'))
                    ->money('OMR')
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('time_slot')
                    ->label(__('owner.availability.time_slot'))
                    ->options([
                        'morning' => __('owner.slots.morning'),
                        'afternoon' => __('owner.slots.afternoon'),
                        'evening' => __('owner.slots.evening'),
                        'full_day' => __('owner.slots.full_day'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_available')
                    ->label(__('owner.availability.status')),

                Tables\Filters\Filter::make('future')
                    ->label(__('owner.availability.future_only'))
                    ->query(fn (Builder $query): Builder => $query->where('date', '>=', now()->toDateString()))
                    ->default(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle')
                    ->label(fn ($record): string => $record->is_available 
                        ? __('owner.availability.block') 
                        : __('owner.availability.unblock'))
                    ->icon(fn ($record): string => $record->is_available 
                        ? 'heroicon-o-x-circle' 
                        : 'heroicon-o-check-circle')
                    ->color(fn ($record): string => $record->is_available ? 'danger' : 'success')
                    ->action(function ($record): void {
                        $record->update([
                            'is_available' => !$record->is_available,
                            'reason' => $record->is_available ? 'blocked' : null,
                        ]);

                        Notification::make()
                            ->success()
                            ->title($record->is_available 
                                ? __('owner.availability.notifications.unblocked') 
                                : __('owner.availability.notifications.blocked'))
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('block')
                        ->label(__('owner.availability.block_selected'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_available' => false, 'reason' => 'blocked'])),
                    
                    Tables\Actions\BulkAction::make('unblock')
                        ->label(__('owner.availability.unblock_selected'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_available' => true, 'reason' => null])),
                    
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
