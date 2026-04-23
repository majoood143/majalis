<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PromoCodeResource\RelationManagers;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('promo.rel_bookings_title');
    }

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_number')
                    ->label(__('promo.rel_col_booking_number'))
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label(__('promo.rel_col_customer'))
                    ->searchable()
                    ->description(fn (Booking $record) => $record->customer_email),

                Tables\Columns\TextColumn::make('hall.name')
                    ->label(__('promo.rel_col_hall'))
                    ->getStateUsing(fn (Booking $record): string => $record->hall
                        ? $record->hall->getTranslation('name', app()->getLocale())
                        : '-'),

                Tables\Columns\TextColumn::make('booking_date')
                    ->label(__('promo.rel_col_booking_date'))
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount_amount')
                    ->label(__('promo.rel_col_discount'))
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 3) . ' ' . __('currency.omr'))
                    ->color('success'),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('promo.rel_col_total'))
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 3) . ' ' . __('currency.omr')),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('promo.rel_col_status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof BookingStatus ? $state->label() : ucfirst($state))
                    ->color(fn ($state) => $state instanceof BookingStatus ? $state->color() : 'gray'),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label(__('promo.rel_col_payment_status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof PaymentStatus ? $state->label() : ucfirst($state))
                    ->color(fn ($state) => $state instanceof PaymentStatus ? $state->color() : 'gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('promo.rel_col_created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('promo.rel_col_status'))
                    ->options(BookingStatus::class),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label(__('promo.rel_col_payment_status'))
                    ->options(PaymentStatus::class),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_bookings')
                    ->label(__('promo.rel_export_bookings'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        $promoCode = $this->getOwnerRecord();
                        $bookings  = $promoCode->bookings()->with('hall')->get();

                        $filename = 'promo_' . $promoCode->code . '_bookings_' . now()->format('Y_m_d_His') . '.csv';
                        $path     = storage_path('app/public/exports/' . $filename);

                        if (!file_exists(dirname($path))) {
                            mkdir(dirname($path), 0755, true);
                        }

                        $file = fopen($path, 'w');

                        fputcsv($file, [
                            __('promo.rel_col_booking_number'),
                            __('promo.rel_col_customer'),
                            __('promo.export_col_email'),
                            __('promo.export_col_phone'),
                            __('promo.rel_col_hall'),
                            __('promo.rel_col_booking_date'),
                            __('promo.rel_col_discount'),
                            __('promo.rel_col_total'),
                            __('promo.rel_col_status'),
                            __('promo.rel_col_payment_status'),
                            __('promo.rel_col_created_at'),
                        ]);

                        foreach ($bookings as $booking) {
                            fputcsv($file, [
                                $booking->booking_number,
                                $booking->customer_name,
                                $booking->customer_email,
                                $booking->customer_phone ?? '',
                                $booking->hall ? $booking->hall->getTranslation('name', 'en') : '',
                                $booking->booking_date?->format('Y-m-d'),
                                number_format((float) $booking->discount_amount, 3),
                                number_format((float) $booking->total_amount, 3),
                                $booking->status instanceof BookingStatus ? $booking->status->label() : ucfirst($booking->status),
                                $booking->payment_status instanceof PaymentStatus ? $booking->payment_status->label() : ucfirst($booking->payment_status),
                                $booking->created_at?->format('Y-m-d H:i:s'),
                            ]);
                        }

                        fclose($file);

                        Notification::make()
                            ->success()
                            ->title(__('promo.export_success_title'))
                            ->body(__('promo.export_success_body', ['filename' => $filename]))
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('download')
                                    ->label(__('promo.export_download'))
                                    ->url(asset('storage/exports/' . $filename))
                                    ->openUrlInNewTab(),
                            ])
                            ->send();
                    }),
            ])
            ->actions([])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
