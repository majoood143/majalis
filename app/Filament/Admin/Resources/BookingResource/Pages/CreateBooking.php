<?php

namespace App\Filament\Admin\Resources\BookingResource\Pages;

use App\Filament\Admin\Resources\BookingResource;
use App\Models\Booking;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Check for existing booking FIRST before generating booking number
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

        // Generate unique booking number
        $data['booking_number'] = $this->generateUniqueBookingNumber();

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            // Double-check slot availability within transaction
            $existingBooking = Booking::where('hall_id', $data['hall_id'])
                ->where('booking_date', $data['booking_date'])
                ->where('time_slot', $data['time_slot'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->lockForUpdate() // Lock the rows to prevent race conditions
                ->first();

            if ($existingBooking) {
                Notification::make()
                    ->danger()
                    ->title('Slot Already Booked')
                    ->body("This time slot was just booked by another user. Please select a different time slot.")
                    ->persistent()
                    ->send();

                throw new \Exception('Slot already booked');
            }

            // Separate extra services for pivot table
            $extraServices = $data['extra_services'] ?? [];
            unset($data['extra_services']);

            // Create booking
            $record = static::getModel()::create($data);

            // Attach extra services if any
            if (!empty($extraServices)) {
                foreach ($extraServices as $service) {
                    $record->extraServices()->attach($service['service_id'], [
                        'service_name' => json_encode($service['service_name']), // Already an array
                        'unit_price' => $service['unit_price'] ?? 0,
                        'quantity' => $service['quantity'] ?? 1,
                        'total_price' => $service['total_price'] ?? 0,
                    ]);
                }
            }

            return $record;
        });
    }

    /**
     * Generate a unique booking number with retry logic
     */
    protected function generateUniqueBookingNumber(): string
    {
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $attempt++;
            $bookingNumber = $this->generateBookingNumber();

            // Check if this booking number already exists
            $exists = Booking::where('booking_number', $bookingNumber)->exists();

            if (!$exists) {
                return $bookingNumber;
            }

            // If exists, wait a tiny bit and try again
            usleep(100000); // 0.1 seconds

        } while ($attempt < $maxAttempts);

        // If we couldn't generate a unique number after max attempts, use timestamp
        return 'BK-' . date('Y') . '-' . time() . '-' . rand(100, 999);
    }

    /**
     * Generate booking number based on the latest booking
     */
    protected function generateBookingNumber(): string
    {
        $year = date('Y');

        // Get the last booking number for this year
        $lastBooking = Booking::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->lockForUpdate()
            ->first();

        if (!$lastBooking || !$lastBooking->booking_number) {
            $sequence = 1;
        } else {
            // Extract sequence number from booking number (BK-2025-00009 -> 00009)
            preg_match('/BK-\d{4}-(\d+)/', $lastBooking->booking_number, $matches);

            if (isset($matches[1])) {
                $sequence = intval($matches[1]) + 1;
            } else {
                // If pattern doesn't match, count all bookings this year + 1
                $sequence = Booking::whereYear('created_at', $year)->count() + 1;
            }
        }

        return 'BK-' . $year . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Booking created successfully';
    }

    /**
     * Disable the create another button to prevent accidental duplicate submissions
     */
    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->disabled();
    }

    /**
     * Add confirmation before creating to prevent accidental clicks
     */
    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->requiresConfirmation()
            ->modalHeading('Confirm Booking Creation')
            ->modalDescription('Are you sure you want to create this booking? Please verify all details are correct.')
            ->modalSubmitActionLabel('Yes, Create Booking');
    }
}
