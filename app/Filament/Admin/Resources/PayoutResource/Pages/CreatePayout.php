<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PayoutResource\Pages;

use App\Enums\PayoutStatus;
use App\Filament\Admin\Resources\PayoutResource;
use App\Models\Booking;
use App\Models\OwnerPayout;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

/**
 * CreatePayout Page
 *
 * Handles creation of new owner payouts.
 * Supports auto-calculation from booking data.
 *
 * @package App\Filament\Admin\Resources\PayoutResource\Pages
 */
class CreatePayout extends CreateRecord
{
    /**
     * The resource this page belongs to.
     *
     * @var string
     */
    protected static string $resource = PayoutResource::class;

    /**
     * Mutate form data before creating the record.
     *
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default status
        if (empty($data['status'])) {
            $data['status'] = PayoutStatus::PENDING;
        }

        // Auto-calculate from bookings if owner and period are set
        if (!empty($data['owner_id']) && !empty($data['period_start']) && !empty($data['period_end'])) {
            $bookings = Booking::whereHas('hall', function ($q) use ($data): void {
                $q->where('owner_id', $data['owner_id']);
            })
                ->whereBetween('booking_date', [
                    $data['period_start'],
                    $data['period_end'],
                ])
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('payment_status', 'paid')
                ->get();

            if ($bookings->isNotEmpty()) {
                $data['bookings_count'] = $bookings->count();
                $data['gross_revenue'] = (float) $bookings->sum('total_amount');
                $data['commission_amount'] = (float) $bookings->sum('commission_amount');
                $data['net_payout'] = (float) $bookings->sum('owner_payout') + (float) ($data['adjustments'] ?? 0);

                // Calculate commission rate
                if ($data['gross_revenue'] > 0) {
                    $data['commission_rate'] = ($data['commission_amount'] / $data['gross_revenue']) * 100;
                }
            }
        }

        return $data;
    }

    /**
     * Handle actions after creating the record.
     *
     * @return void
     */
    protected function afterCreate(): void
    {
        $record = $this->record;

        // Send notification
        Notification::make()
            ->title(__('admin.payout.notifications.created'))
            ->body(__('admin.payout.notifications.created_body', [
                'number' => $record->payout_number,
                'owner' => $record->owner->name,
                'amount' => number_format((float) $record->net_payout, 3),
            ]))
            ->success()
            ->send();
    }

    /**
     * Get the header actions for this page.
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('calculate')
                ->label(__('admin.payout.actions.calculate'))
                ->icon('heroicon-o-calculator')
                ->color('warning')
                ->action(function (): void {
                    $data = $this->form->getState();

                    if (empty($data['owner_id']) || empty($data['period_start']) || empty($data['period_end'])) {
                        Notification::make()
                            ->title(__('admin.payout.notifications.missing_data'))
                            ->body(__('admin.payout.notifications.missing_data_body'))
                            ->warning()
                            ->send();
                        return;
                    }

                    // Calculate from bookings
                    $bookings = Booking::whereHas('hall', function ($q) use ($data): void {
                        $q->where('owner_id', $data['owner_id']);
                    })
                        ->whereBetween('booking_date', [
                            $data['period_start'],
                            $data['period_end'],
                        ])
                        ->whereIn('status', ['confirmed', 'completed'])
                        ->where('payment_status', 'paid')
                        ->get();

                    if ($bookings->isEmpty()) {
                        Notification::make()
                            ->title(__('admin.payout.notifications.no_bookings'))
                            ->body(__('admin.payout.notifications.no_bookings_body'))
                            ->warning()
                            ->send();
                        return;
                    }

                    // Update form with calculated values
                    $this->form->fill([
                        ...$data,
                        'bookings_count' => $bookings->count(),
                        'gross_revenue' => number_format((float) $bookings->sum('total_amount'), 3, '.', ''),
                        'commission_amount' => number_format((float) $bookings->sum('commission_amount'), 3, '.', ''),
                        'net_payout' => number_format((float) $bookings->sum('owner_payout'), 3, '.', ''),
                        'commission_rate' => $bookings->sum('total_amount') > 0
                            ? number_format(
                                ($bookings->sum('commission_amount') / $bookings->sum('total_amount')) * 100,
                                2,
                                '.',
                                ''
                            )
                            : '0.00',
                    ]);

                    Notification::make()
                        ->title(__('admin.payout.notifications.calculated'))
                        ->body(__('admin.payout.notifications.calculated_body', [
                            'count' => $bookings->count(),
                        ]))
                        ->success()
                        ->send();
                }),
        ];
    }

    /**
     * Get the redirect URL after creating the record.
     *
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
