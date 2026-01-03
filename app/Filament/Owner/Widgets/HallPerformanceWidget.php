<?php

declare(strict_types=1);

namespace App\Filament\Owner\Widgets;

use App\Models\Hall;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class HallPerformanceWidget extends BaseWidget
{
    /**
     * Widget column span
     */
    protected int | string | array $columnSpan = 'full';

    /**
     * Widget heading
     */
    public function getTableHeading(): ?string
    {
        return __('owner.widgets.hall_performance');
    }

    /**
     * Configure the table
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Hall::query()
                    ->where('owner_id', Auth::id())
                    ->withCount([
                        'bookings as total_bookings',
                        'bookings as confirmed_bookings' => function ($q) {
                            $q->where('bookings.status', 'confirmed');
                        },
                        'bookings as this_month_bookings' => function ($q) {
                            $q->whereMonth('booking_date', now()->month)
                                ->whereYear('booking_date', now()->year);
                        },
                    ])
                    ->withSum([
                        'payments as total_revenue' => function ($q) {
                            $q->where('bookings.payment_status', 'paid');
                        }
                    ], 'amount')
                    ->withAvg('reviews', 'rating')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('owner.halls.name'))
                    //->getStateUsing(fn($record) => $record->name[app()->getLocale()] ?? $record->name['en'])
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\IconColumn::make('is_active')
                    //->label(__('owner.halls.status'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('total_bookings')
                    ->label(__('owner.halls.total_bookings'))
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('this_month_bookings')
                    ->label(__('owner.halls.this_month'))
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('total_revenue')
                    ->label(__('owner.halls.revenue'))
                    ->money('OMR')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                Tables\Columns\ViewColumn::make('occupancy_rate')
                    ->label(__('owner.halls.occupancy'))
                    ->view('filament.owner.widgets.occupancy-rate')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('reviews_avg_rating')
                    ->label(__('owner.halls.rating'))
                    ->formatStateUsing(fn($state) => $state ? number_format($state, 1) . ' ⭐' : '-')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('next_booking')
                    ->label(__('owner.halls.next_booking'))
                    ->getStateUsing(function ($record) {
                        $nextBooking = $record->bookings()
                            ->where('bookings.status', 'confirmed')
                            ->where('booking_date', '>=', now())
                            ->orderBy('booking_date')
                            ->first();

                        return $nextBooking
                            ? $nextBooking->booking_date->format('M j')
                            : '-';
                    })
                    ->badge()
                    ->color('warning'),
            ])
            ->actions([
                Tables\Actions\Action::make('manage')
                    ->label(__('owner.actions.manage'))
                    ->icon('heroicon-m-cog')
                    //->url(fn($record) => route('filament.owner.resources.halls.edit', $record))
                    ->button()
                    ->outlined(),
            ])
            ->paginated(false)
            ->poll('60s');
    }
}
