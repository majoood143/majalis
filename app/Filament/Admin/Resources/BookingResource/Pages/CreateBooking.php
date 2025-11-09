<?php

namespace App\Filament\Admin\Resources\BookingResource\Pages;

use App\Filament\Admin\Resources\BookingResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate booking number if not provided
        if (empty($data['booking_number'])) {
            $data['booking_number'] = $this->generateBookingNumber();
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);

        // Calculate pricing automatically
        $record->calculateTotals();
        $record->save();

        return $record;
    }

    protected function generateBookingNumber(): string
    {
        $year = date('Y');
        $lastBooking = static::getModel()::whereYear('created_at', $year)
            ->latest('id')
            ->first();

        $sequence = $lastBooking ? intval(substr($lastBooking->booking_number, -5)) + 1 : 1;

        return 'BK-' . $year . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Booking created successfully';
    }
}
