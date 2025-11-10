<?php

namespace App\Filament\Admin\Resources\BookingResource\Pages;

use App\Filament\Admin\Resources\BookingResource;
use App\Models\Booking;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

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

        // Validate no double booking
        $existingBooking = Booking::where('hall_id', $data['hall_id'])
            ->where('booking_date', $data['booking_date'])
            ->where('time_slot', $data['time_slot'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingBooking) {
            Notification::make()
                ->danger()
                ->title('Slot Already Booked')
                ->body("This time slot is already booked (Booking #{$existingBooking->booking_number}). Please select a different date or time slot.")
                ->persistent()
                ->send();

            $this->halt();
        }

        // Validate hall capacity
        $hall = \App\Models\Hall::find($data['hall_id']);
        if ($hall) {
            if ($data['number_of_guests'] < $hall->capacity_min) {
                Notification::make()
                    ->warning()
                    ->title('Guest Count Below Minimum')
                    ->body("Minimum capacity is {$hall->capacity_min} guests. Guest count has been adjusted.")
                    ->send();

                $data['number_of_guests'] = $hall->capacity_min;
            } elseif ($data['number_of_guests'] > $hall->capacity_max) {
                Notification::make()
                    ->danger()
                    ->title('Guest Count Exceeds Maximum')
                    ->body("Maximum capacity is {$hall->capacity_max} guests.")
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Separate extra services for pivot table
        $extraServices = $data['extra_services'] ?? [];
        unset($data['extra_services']);

        // Create booking
        $record = static::getModel()::create($data);

        // Attach extra services if any
        if (!empty($extraServices)) {
            foreach ($extraServices as $service) {
                $record->extraServices()->attach($service['service_id'], [
                    'service_name' => $service['service_name'],
                    'unit_price' => $service['unit_price'],
                    'quantity' => $service['quantity'],
                    'total_price' => $service['total_price'],
                ]);
            }
        }

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
