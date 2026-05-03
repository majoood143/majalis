<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\HallResource\RelationManagers;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use App\Models\HallAvailability;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * BookingsRelationManager for Owner Panel
 *
 * Displays bookings for a specific hall owned by the current user.
 * Owners can view, confirm, and manage bookings.
 */
class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('owner.relation.bookings');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('booking_number')
            ->defaultSort('booking_date', 'desc')
            ->columns([
                TextColumn::make('booking_number')
                    ->label(__('owner.bookings.number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('booking_date')
                    ->label(__('owner.bookings.date'))
                    ->date('d M Y')
                    ->sortable()
                    ->description(fn ($record): string => __("owner.slots.{$record->time_slot}")),

                TextColumn::make('customer_name')
                    ->label(__('owner.bookings.customer'))
                    ->searchable()
                    ->description(fn ($record): string => $record->customer_phone ?? ''),

                TextColumn::make('number_of_guests')
                    ->label(__('owner.bookings.guests'))
                    ->numeric()
                    ->alignCenter()
                    ->icon('heroicon-o-users'),

                TextColumn::make('total_amount')
                    ->label(__('owner.bookings.total'))
                    ->money('OMR')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('payment_status')
                    ->label(__('owner.bookings.payment'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("owner.payment.{$state}"))
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'partial' => 'warning',
                        'refunded' => 'gray',
                        'failed' => 'danger',
                        default => 'warning',
                    }),

                TextColumn::make('status')
                    ->label(__('owner.bookings.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("owner.status.{$state}"))
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label(__('owner.bookings.booked_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('owner.bookings.status'))
                    ->options([
                        'pending' => __('owner.status.pending'),
                        'confirmed' => __('owner.status.confirmed'),
                        'completed' => __('owner.status.completed'),
                        'cancelled' => __('owner.status.cancelled'),
                    ]),

                SelectFilter::make('payment_status')
                    ->label(__('owner.bookings.payment'))
                    ->options([
                        'pending' => __('owner.payment.pending'),
                        'paid' => __('owner.payment.paid'),
                        'partial' => __('owner.payment.partial'),
                        'refunded' => __('owner.payment.refunded'),
                    ]),

                Filter::make('upcoming')
                    ->label(__('owner.bookings.upcoming'))
                    ->query(fn (Builder $query): Builder => $query->where('booking_date', '>=', now()->toDateString())),

                Filter::make('date_range')
                    ->schema([
                        DatePicker::make('from')
                            ->label(__('owner.bookings.from_date')),
                        DatePicker::make('until')
                            ->label(__('owner.bookings.until_date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date) => $q->whereDate('booking_date', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date) => $q->whereDate('booking_date', '<=', $date));
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading(fn ($record): string => __('owner.bookings.view_title', ['number' => $record->booking_number])),

                ActionGroup::make([
                    // Confirm Booking
                    Action::make('confirm')
                        ->label(__('owner.bookings.confirm'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn ($record): bool => $record->status === 'pending')
                        ->action(function ($record): void {
                            $record->update([
                                'status' => 'confirmed',
                                'confirmed_at' => now(),
                            ]);

                            Notification::make()
                                ->success()
                                ->title(__('owner.bookings.confirmed_success'))
                                ->send();
                        }),

                    // Mark as Completed
                    Action::make('complete')
                        ->label(__('owner.bookings.complete'))
                        ->icon('heroicon-o-check-badge')
                        ->color('info')
                        ->requiresConfirmation()
                        ->visible(fn ($record): bool => $record->status === 'confirmed')
                        ->action(function ($record): void {
                            $record->update([
                                'status' => 'completed',
                                'completed_at' => now(),
                            ]);

                            $this->ownerRecord->updateStatistics();

                            Notification::make()
                                ->success()
                                ->title(__('owner.bookings.completed_success'))
                                ->send();
                        }),

                    // Cancel Booking
                    Action::make('cancel')
                        ->label(__('owner.bookings.cancel'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->schema([
                            Textarea::make('cancellation_reason')
                                ->label(__('owner.bookings.cancellation_reason'))
                                ->required()
                                ->maxLength(500),
                        ])
                        ->visible(fn ($record): bool => !in_array($record->status, ['cancelled', 'completed']))
                        ->action(function ($record, array $data): void {
                            $record->update([
                                'status' => 'cancelled',
                                'cancelled_at' => now(),
                                'cancellation_reason' => $data['cancellation_reason'],
                            ]);

                            // Free up the availability slot
                            HallAvailability::where('hall_id', $record->hall_id)
                                ->where('date', $record->booking_date)
                                ->where('time_slot', $record->time_slot)
                                ->where('reason', 'booked')
                                ->update(['is_available' => true, 'reason' => null]);

                            Notification::make()
                                ->warning()
                                ->title(__('owner.bookings.cancelled_success'))
                                ->send();
                        }),

                    // Contact Customer
                    Action::make('contact')
                        ->label(__('owner.bookings.contact'))
                        ->icon('heroicon-o-phone')
                        ->color('gray')
                        ->url(fn ($record): string => "tel:{$record->customer_phone}")
                        ->openUrlInNewTab(),
                ])->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('confirm_selected')
                        ->label(__('owner.bookings.confirm_selected'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->update([
                                        'status' => 'confirmed',
                                        'confirmed_at' => now(),
                                    ]);
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->success()
                                ->title(__('owner.bookings.bulk_confirmed', ['count' => $count]))
                                ->send();
                        }),
                ]),
            ])
            ->emptyStateHeading(__('owner.bookings.empty_heading'))
            ->emptyStateDescription(__('owner.bookings.empty_description'))
            ->emptyStateIcon('heroicon-o-calendar');
    }
}
