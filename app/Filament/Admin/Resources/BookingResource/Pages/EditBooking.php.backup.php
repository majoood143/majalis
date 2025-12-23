<?php

namespace App\Filament\Admin\Resources\BookingResource\Pages;

use App\Filament\Admin\Resources\BookingResource;
use App\Models\Booking;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditBooking_ extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Check if hall, date, or time slot changed
        if (
            $data['hall_id'] != $this->record->hall_id ||
            $data['booking_date'] != $this->record->booking_date->format('Y-m-d') ||
            $data['time_slot'] != $this->record->time_slot
        ) {
            // Validate no double booking
            $existingBooking = Booking::where('hall_id', $data['hall_id'])
                ->where('booking_date', $data['booking_date'])
                ->where('time_slot', $data['time_slot'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('id', '!=', $this->record->id) // Exclude current booking
                ->first();

            if ($existingBooking) {
                Notification::make()
                    ->danger()
                    ->title('Slot Already Booked')
                    ->body("This time slot is already booked (Booking #{$existingBooking->booking_number}). Cannot change to this slot.")
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Booking updated successfully';
    }
}

