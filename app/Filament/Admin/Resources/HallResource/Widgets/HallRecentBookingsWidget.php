<?php

declare(strict_types=1);

/**
 * HallRecentBookingsWidget - Recent Bookings Table Widget
 *
 * Displays a table of recent bookings for the specific hall.
 * Shows key information at a glance:
 * - Booking number and customer
 * - Date and time slot
 * - Status with color coding
 * - Payment status
 * - Total amount
 *
 * @package App\Filament\Admin\Resources\HallResource\Widgets
 * @version 1.0.0
 * @author Majalis Development Team
 */

namespace App\Filament\Admin\Resources\HallResource\Widgets;

use App\Models\Hall;
use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class HallRecentBookingsWidget extends BaseWidget
{
    /**
     * The hall record being viewed.
     *
     * @var Hall|Model|null
     */
    public ?Model $record = null;

    /**
     * Polling interval for auto-refresh.
     *
     * @var string|null
     */
    protected static ?string $pollingInterval = '60s';

    /**
     * Widget column span - full width.
     *
     * @var int|string|array
     */
    protected int|string|array $columnSpan = 'full';

    /**
     * Number of records to display per page.
     *
     * @var int
     */
    protected int $defaultPaginationPageSize = 5;

    /**
     * Get the widget heading.
     *
     * @return string|null
     */
    protected function getTableHeading(): ?string
    {
        return __('Recent Bookings');
    }

    /**
     * Get the widget description.
     *
     * @return string|null
     */
    protected function getTableDescription(): ?string
    {
        return __('Latest booking activity for this hall');
    }

    /**
     * Build the query for the table.
     *
     * @return Builder
     */
    protected function getTableQuery(): Builder
    {
        // Return empty query if no record
        if (!$this->record instanceof Hall) {
            return Booking::query()->whereRaw('1 = 0');
        }

        return Booking::query()
            ->where('hall_id', $this->record->id)
            ->with(['user']) // Eager load customer relationship
            ->latest('booking_date')
            ->latest('created_at');
    }

    /**
     * Define the table structure.
     *
     * @param Table $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                // Booking Number
                Tables\Columns\TextColumn::make('booking_number')
                    ->label(__('Booking #'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('Booking number copied'))
                    ->weight('bold')
                    ->color('primary'),

                // Customer Name
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('Customer'))
                    ->searchable()
                    ->sortable()
                    ->default(__('Guest'))
                    ->icon('heroicon-o-user'),

                // Booking Date
                Tables\Columns\TextColumn::make('booking_date')
                    ->label(__('Date'))
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                // Time Slot
                Tables\Columns\TextColumn::make('time_slot')
                    ->label(__('Time Slot'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'morning' => __('Morning'),
                        'afternoon' => __('Afternoon'),
                        'evening' => __('Evening'),
                        'full_day' => __('Full Day'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'morning' => 'info',
                        'afternoon' => 'warning',
                        'evening' => 'primary',
                        'full_day' => 'success',
                        default => 'gray',
                    }),

                // Booking Status
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => __('Pending'),
                        'confirmed' => __('Confirmed'),
                        'completed' => __('Completed'),
                        'cancelled' => __('Cancelled'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                // Payment Status
                Tables\Columns\TextColumn::make('payment_status')
                    ->label(__('Payment'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unpaid' => __('Unpaid'),
                        'partial' => __('Partial'),
                        'paid' => __('Paid'),
                        'refunded' => __('Refunded'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'unpaid' => 'danger',
                        'partial' => 'warning',
                        'paid' => 'success',
                        'refunded' => 'info',
                        default => 'gray',
                    }),

                // Total Amount
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('Amount'))
                    ->money('OMR')
                    ->sortable()
                    ->alignEnd(),

                // Created Date
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Booked'))
                    ->dateTime('d M Y')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Status Filter
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'pending' => __('Pending'),
                        'confirmed' => __('Confirmed'),
                        'completed' => __('Completed'),
                        'cancelled' => __('Cancelled'),
                    ]),

                // Payment Status Filter
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label(__('Payment'))
                    ->options([
                        'unpaid' => __('Unpaid'),
                        'partial' => __('Partial'),
                        'paid' => __('Paid'),
                        'refunded' => __('Refunded'),
                    ]),

                // Date Range Filter
                Tables\Filters\Filter::make('upcoming')
                    ->label(__('Upcoming Only'))
                    ->query(fn (Builder $query): Builder => $query->where('booking_date', '>=', now()->toDateString())),
            ])
            ->actions([
                // View Booking Action
                Tables\Actions\Action::make('view')
                    ->label(__('View'))
                    ->icon('heroicon-o-eye')
                    ->url(fn (Booking $record): string => route('filament.admin.resources.bookings.view', $record))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading(__('No Bookings Yet'))
            ->emptyStateDescription(__('This hall has no booking records.'))
            ->emptyStateIcon('heroicon-o-calendar')
            ->defaultSort('booking_date', 'desc')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->poll('60s');
    }
}
