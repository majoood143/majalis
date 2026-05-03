<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\HallResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->label(__('owner.availability.date'))
                    ->required()
                    ->native(false)
                    ->minDate(now()),

                Select::make('time_slot')
                    ->label(__('owner.availability.time_slot'))
                    ->required()
                    ->options([
                        'morning' => __('owner.slots.morning'),
                        'afternoon' => __('owner.slots.afternoon'),
                        'evening' => __('owner.slots.evening'),
                        'full_day' => __('owner.slots.full_day'),
                    ]),

                Toggle::make('is_available')
                    ->label(__('owner.availability.is_available'))
                    ->default(true),

                Select::make('reason')
                    ->label(__('owner.availability.reason'))
                    ->options([
                        'blocked' => __('owner.availability.reasons.blocked'),
                        'maintenance' => __('owner.availability.reasons.maintenance'),
                        'holiday' => __('owner.availability.reasons.holiday'),
                        'private_event' => __('owner.availability.reasons.private_event'),
                        'renovation' => __('owner.availability.reasons.renovation'),
                        'other' => __('owner.availability.reasons.other'),
                    ])
                    ->visible(fn (Get $get): bool => !$get('is_available')),

                TextInput::make('custom_price')
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
                TextColumn::make('date')
                    ->label(__('owner.availability.date'))
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('time_slot')
                    ->label(__('owner.availability.time_slot'))
                    ->formatStateUsing(fn (string $state): string => __("owner.slots.{$state}"))
                    ->badge(),

                IconColumn::make('is_available')
                    ->label(__('owner.availability.status'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('reason')
                    ->label(__('owner.availability.reason'))
                    ->formatStateUsing(fn (?string $state): string => $state ? __("owner.availability.reasons.{$state}") : '-')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'booked' => 'info',
                        'maintenance' => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('custom_price')
                    ->label(__('owner.availability.custom_price'))
                    ->money('OMR')
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('time_slot')
                    ->label(__('owner.availability.time_slot'))
                    ->options([
                        'morning' => __('owner.slots.morning'),
                        'afternoon' => __('owner.slots.afternoon'),
                        'evening' => __('owner.slots.evening'),
                        'full_day' => __('owner.slots.full_day'),
                    ]),

                TernaryFilter::make('is_available')
                    ->label(__('owner.availability.status')),

                Filter::make('future')
                    ->label(__('owner.availability.future_only'))
                    ->query(fn (Builder $query): Builder => $query->where('date', '>=', now()->toDateString()))
                    ->default(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('toggle')
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
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('block')
                        ->label(__('owner.availability.block_selected'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_available' => false, 'reason' => 'blocked'])),
                    
                    BulkAction::make('unblock')
                        ->label(__('owner.availability.unblock_selected'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_available' => true, 'reason' => null])),
                    
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
