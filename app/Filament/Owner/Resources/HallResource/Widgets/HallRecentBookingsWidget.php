<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\HallResource\Widgets;

use App\Models\Hall;
use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class HallRecentBookingsWidget extends BaseWidget
{
    public ?Model $record = null;
    protected static ?string $pollingInterval = '60s';
    protected int|string|array $columnSpan = 'full';
    protected int $defaultPaginationPageSize = 5;

    protected function getTableHeading(): ?string
    {
        return __('widgets.hall-recent-bookings.heading');
    }

    protected function getTableDescription(): ?string
    {
        return __('widgets.hall-recent-bookings.description');
    }

    protected function getTableQuery(): Builder
    {
        if (!$this->record instanceof Hall) {
            return Booking::query()->whereRaw('1 = 0');
        }

        return Booking::query()
            ->where('hall_id', $this->record->id)
            ->with(['user'])
            ->latest('booking_date')
            ->latest('created_at');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('booking_number')
                    ->label(__('widgets.hall-recent-bookings.columns.booking_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('widgets.hall-recent-bookings.messages.copy_success'))
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('widgets.hall-recent-bookings.columns.customer'))
                    ->searchable()
                    ->sortable()
                    ->default(__('common.guest'))
                    ->icon('heroicon-o-user'),

                Tables\Columns\TextColumn::make('booking_date')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                Tables\Columns\TextColumn::make('time_slot')
                    ->label(__('widgets.hall-recent-bookings.columns.time_slot'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => __("common.time_slots.{$state}"))
                    ->color(fn(string $state): string => match ($state) {
                        'morning' => 'info',
                        'afternoon' => 'warning',
                        'evening' => 'primary',
                        'full_day' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('widgets.hall-recent-bookings.columns.status'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => __("common.booking_status.{$state}"))
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => __("common.payment_status.{$state}"))
                    ->color(fn(string $state): string => match ($state) {
                        'unpaid' => 'danger',
                        'partial' => 'warning',
                        'paid' => 'success',
                        'refunded' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('widgets.hall-recent-bookings.columns.amount'))
                    ->money('OMR')
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('widgets.hall-recent-bookings.columns.booked'))
                    ->dateTime('d M Y')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('widgets.hall-recent-bookings.filters.status'))
                    ->options([
                        'pending' => __('common.booking_status.pending'),
                        'confirmed' => __('common.booking_status.confirmed'),
                        'completed' => __('common.booking_status.completed'),
                        'cancelled' => __('common.booking_status.cancelled'),
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label(__('widgets.hall-recent-bookings.filters.payment'))
                    ->options([
                        'unpaid' => __('common.payment_status.unpaid'),
                        'partial' => __('common.payment_status.partial'),
                        'paid' => __('common.payment_status.paid'),
                        'refunded' => __('common.payment_status.refunded'),
                    ]),

                Tables\Filters\Filter::make('upcoming')
                    ->label(__('widgets.hall-recent-bookings.filters.upcoming'))
                    ->query(fn(Builder $query): Builder => $query->where('booking_date', '>=', now()->toDateString())),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('common.actions.view'))
                    ->icon('heroicon-o-eye')
                    ->url(fn(Booking $record): string => route('filament.owner.resources.bookings.view', $record))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading(__('widgets.hall-recent-bookings.empty_state.heading'))
            ->emptyStateDescription(__('widgets.hall-recent-bookings.empty_state.description'))
            ->emptyStateIcon('heroicon-o-calendar')
            ->defaultSort('booking_date', 'desc')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->poll('60s');
    }
}
