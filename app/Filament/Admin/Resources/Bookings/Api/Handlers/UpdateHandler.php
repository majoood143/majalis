<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Bookings\Api\Handlers;

use App\Enums\BookingStatus;
use App\Filament\Admin\Resources\BookingResource;
use App\Filament\Admin\Resources\Bookings\Api\Requests\UpdateBookingRequest;
use App\Filament\Admin\Resources\Bookings\Api\Transformers\BookingTransformer;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a Booking record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = BookingResource::class;

    protected static string $permission = 'Update:Booking';

    protected static string $transformer = BookingTransformer::class;

    /**
     * Update Booking
     */
    public function handler(UpdateBookingRequest $request)
    {
        return $this->modify($request);
    }

    /**
     * Status changes go through Booking::confirm()/cancel()/complete() so timestamps
     * and refund amounts match the panel.
     */
    protected function updateRecord(Model $record, array $data): void
    {
        $status = $data['status'] ?? null;
        unset($data['status']);

        $record->update($data);

        $current = $record->status instanceof \BackedEnum ? $record->status->value : $record->status;

        if ($status === null || $status === $current) {
            return;
        }

        match ($status) {
            BookingStatus::CONFIRMED->value => $record->confirm(),
            BookingStatus::CANCELLED->value => $record->cancel($data['cancellation_reason'] ?? $record->cancellation_reason),
            BookingStatus::COMPLETED->value => $record->complete(),
            default => $record->update(['status' => $status]),
        };
    }
}
