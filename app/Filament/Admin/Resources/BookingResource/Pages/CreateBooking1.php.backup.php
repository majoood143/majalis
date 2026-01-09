<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\BookingResource\Pages;

use App\Filament\Admin\Resources\BookingResource;
use App\Models\Booking;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * CreateBooking Page
 *
 * Handles the creation of new bookings through the admin panel.
 *
 * Features:
 * - Validates slot availability (prevents double bookings)
 * - Validates guest capacity against hall limits
 * - Generates unique booking numbers
 * - Attaches extra services with pricing
 * - ✅ Calculates advance payment if hall allows it
 * - Uses database transactions for data integrity
 * - Implements confirmation dialogs for safety
 *
 * Flow:
 * 1. Validate form data (mutateFormDataBeforeCreate)
 * 2. Check slot availability (with locking)
 * 3. Create booking record
 * 4. Attach extra services
 * 5. ✅ Calculate advance payment (if applicable)
 * 6. Send notifications
 * 7. Redirect to view page
 *
 * @package App\Filament\Admin\Resources\BookingResource\Pages
 */
class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    /**
     * Mutate form data before creating the booking
     *
     * Performs validation and data preparation:
     * - Checks for existing bookings on the same slot (prevents double booking)
     * - Validates guest count against hall capacity (min/max)
     * - Generates unique booking number
     *
     * Note: Advance payment calculation happens AFTER record creation
     * in handleRecordCreation() to ensure total_amount is finalized.
     *
     * @param array $data Form data
     * @return array Mutated data
     * @throws \Exception If validation fails (halts process)
     */
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

    /**
     * Handle the actual record creation with transaction safety
     *
     * Process:
     * 1. Start database transaction
     * 2. Double-check slot availability with row locking
     * 3. Create booking record
     * 4. Attach extra services to booking
     * 5. ✅ NEW: Calculate advance payment if hall allows it
     * 6. Commit transaction
     *
     * ✅ Advance Payment Logic:
     * - Checks if hall has allows_advance_payment enabled
     * - Calls calculateAdvancePayment() on the booking model
     * - Sets payment_type, advance_amount, balance_due fields
     * - Sends notification about advance payment requirement
     *
     * The advance payment calculation happens AFTER services are attached
     * to ensure the total_amount includes all charges.
     *
     * @param array $data Validated form data
     * @return Model Created booking record
     * @throws \Exception If slot becomes unavailable during creation
     */
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

            // ✅ NEW: Calculate advance payment if hall allows it
            // This must happen AFTER creating the booking and attaching services
            // so that total_amount is correctly calculated
            $hall = \App\Models\Hall::find($record->hall_id);
            if ($hall && $hall->allows_advance_payment) {
                // Calculate advance payment based on hall settings
                $record->calculateAdvancePayment();
                $record->save();

                // Notify admin that this is an advance payment booking
                Notification::make()
                    ->success()
                    ->title('Advance Payment Booking')
                    ->body(sprintf(
                        'This booking requires advance payment. Customer must pay %s OMR upfront. Balance of %s OMR due before event.',
                        number_format((float)$record->advance_amount, 3),
                        number_format((float)$record->balance_due, 3)
                    ))
                    ->send();
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

        return 'BK-' . $year . '-' . str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the title for the creation success notification
     *
     * @return string|null Notification title
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Booking created successfully';
    }

    /**
     * ✅ NEW: After create hook to provide additional information
     *
     * Shows helpful information about the created booking,
     * especially for advance payment bookings.
     */
    protected function afterCreate(): void
    {
        $record = $this->record;

        // If this is an advance payment booking, provide detailed info
        if ($record->isAdvancePayment()) {
            Notification::make()
                ->info()
                ->title('📋 Booking Summary')
                ->body(sprintf(
                    "**Booking:** %s\n**Total Amount:** %s OMR\n**Payment Type:** Advance Payment\n**Advance Required:** %s OMR\n**Balance Due:** %s OMR\n\nCustomer must pay advance amount before event confirmation.",
                    $record->booking_number,
                    number_format((float)$record->total_amount, 3),
                    number_format((float)$record->advance_amount, 3),
                    number_format((float)$record->balance_due, 3)
                ))
                ->persistent()
                ->send();
        }
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
