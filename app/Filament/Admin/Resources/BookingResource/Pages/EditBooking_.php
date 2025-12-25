<?php

namespace App\Filament\Admin\Resources\BookingResource\Pages;

use App\Filament\Admin\Resources\BookingResource;
use App\Models\Booking;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

/**
 * Edit Booking Page
 *
 * Handles booking updates with validation for:
 * - Double booking prevention
 * - Slot availability checks
 * - Data integrity
 *
 * @package App\Filament\Admin\Resources\BookingResource\Pages
 */
class EditBooking_ extends EditRecord
{
    protected static string $resource = BookingResource::class;

    /**
     * Get the header actions for the page
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Mutate form data before saving
     *
     * Validates that the booking slot is available when hall, date, or time slot changes.
     * Prevents double bookings by checking for existing confirmed/pending bookings.
     *
     * @param array $data The form data
     * @return array The mutated data
     */
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

    /**
     * Get the redirect URL after save
     *
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    /**
     * Get the success notification title
     *
     * @return string|null
     */
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Booking updated successfully';
    }

    /**
     * Mutate form data before fill
     *
     * This ensures relationships are loaded properly without triggering
     * scope errors on BelongsToMany relationships.
     *
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load extra services relationship properly
        // This prevents the "active()" scope error by loading the relationship
        // directly without applying scopes during form population
        if ($this->record->relationLoaded('extraServices')) {
            $data['extra_services'] = $this->record->extraServices->pluck('id')->toArray();
        }

        return $data;
    }
}
