<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\BookingResource\Pages;

use Exception;
use Throwable;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Resources\BookingResource;
use Illuminate\Support\Facades\Log;

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
 * - ✅ Comprehensive error handling with user-friendly notifications
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
        try {
            // Check for existing booking FIRST before generating booking number
            $existingBooking = Booking::where('hall_id', $data['hall_id'])
                ->where('booking_date', $data['booking_date'])
                ->where('time_slot', $data['time_slot'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->first();

            if ($existingBooking) {
                Notification::make()
                    ->danger()
                    ->title(__('booking.notifications.slot_already_booked_title'))
                    ->body(__('booking.notifications.slot_already_booked_body', [
                        'booking_number' => $existingBooking->booking_number
                    ]))
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
                        ->title(__('booking.notifications.guest_count_below_min_title'))
                        ->body(__('booking.notifications.guest_count_below_min_body', [
                            'capacity_min' => $hall->capacity_min
                        ]))
                        ->send();

                    $data['number_of_guests'] = $hall->capacity_min;
                } elseif ($data['number_of_guests'] > $hall->capacity_max) {
                    Notification::make()
                        ->danger()
                        ->title(__('booking.notifications.guest_count_exceeds_max_title'))
                        ->body(__('booking.notifications.guest_count_exceeds_max_body', [
                            'capacity_max' => $hall->capacity_max
                        ]))
                        ->persistent()
                        ->send();

                    $this->halt();
                }
            }

            // Generate unique booking number
            $data['booking_number'] = $this->generateUniqueBookingNumber();

            return $data;
        } catch (Exception $e) {
            //Log the error (you can add logging here if needed)
            Log::error('Error in mutateFormDataBeforeCreate: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $data
            ]);

            Notification::make()
                ->danger()
                ->title(__('booking.notifications.validation_error_title'))
                ->body(__('booking.notifications.validation_error_body', [
                    'error' => $e->getMessage()
                ]))
                ->persistent()
                ->send();

            $this->halt();

            // Return original data to satisfy return type
            return $data;
        }
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
        try {
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
                        ->title(__('booking.notifications.slot_just_booked_title'))
                        ->body(__('booking.notifications.slot_just_booked_body'))
                        ->persistent()
                        ->send();

                    throw new Exception(__('booking.exceptions.slot_already_booked'));
                }

                // Separate extra services for pivot table
                $extraServices = $data['extra_services'] ?? [];
                unset($data['extra_services']);

                // Create booking
                $record = static::getModel()::create($data);


                if (!empty($extraServices)) {
                    foreach ($extraServices as $service) {
                        $record->extraServices()->create([
                            'extra_service_id' => $service['service_id'],
                            'service_name'     => json_encode($service['service_name']), // Already an array
                            'unit_price'       => $service['unit_price'] ?? 0,
                            'quantity'          => $service['quantity'] ?? 1,
                            'total_price'      => $service['total_price'] ?? 0,
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
                        ->title(__('booking.notifications.advance_payment_booking_title'))
                        ->body(__('booking.notifications.advance_payment_booking_body', [
                            'advance_amount' => number_format((float)$record->advance_amount, 3),
                            'balance_due' => number_format((float)$record->balance_due, 3)
                        ]))
                        ->send();
                }

                return $record;
            });
        } catch (Exception $e) {
            // Log the error (you can add logging here if needed)
            Log::error('Error in handleRecordCreation: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $data
            ]);

            // Rollback happens automatically when exception is thrown in transaction

            Notification::make()
                ->danger()
                ->title(__('booking.notifications.creation_error_title'))
                ->body(__('booking.notifications.creation_error_body', [
                    'error' => $e->getMessage()
                ]))
                ->persistent()
                ->send();

            // Re-throw the exception to prevent the creation from proceeding
            throw $e;
        }
    }

    /**
     * Override the create method to handle overall process errors
     */
    public function create(bool $another = false): void
    {
        try {
            parent::create($another);
        } catch (Throwable $e) {
            // Catch any unexpected errors during the entire creation process

            // Log the error (you can add logging here if needed)
            Log::error('Unexpected error in booking creation: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            Notification::make()
                ->danger()
                ->title(__('booking.notifications.unexpected_error_title'))
                ->body(__('booking.notifications.unexpected_error_body', [
                    'error' => $e->getMessage()
                ]))
                ->persistent()
                ->send();

            // Optional: You could redirect back to the form here
            // $this->redirect($this->getResource()::getUrl('create'));
        }
    }

    /**
     * Generate a unique booking number with retry logic
     */
    protected function generateUniqueBookingNumber(): string
    {
        try {
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
        } catch (Exception $e) {
            // Fallback to timestamp-based booking number if generation fails
            Log::error('Error generating booking number: ' . $e->getMessage());

            return 'BK-' . date('Y') . '-' . time() . '-' . rand(100, 999);
        }
    }

    /**
     * Generate booking number based on the latest booking
     */
    protected function generateBookingNumber(): string
    {
        try {
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
        } catch (Exception $e) {
            // Fallback to simple timestamp-based number
            Log::error('Error in generateBookingNumber: ' . $e->getMessage());

            return 'BK-' . date('Y') . '-' . time() . '-' . rand(100, 999);
        }
    }

    /**
     * Get the title for the creation success notification
     *
     * @return string|null Notification title
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        return __('booking.notifications.booking_created_title');
    }

    /**
     * ✅ NEW: After create hook to provide additional information
     *
     * Shows helpful information about the created booking,
     * especially for advance payment bookings.
     */
    protected function afterCreate(): void
    {
        try {
            $record = $this->record;


            // =====================================================
            // ✅ SEND EMAIL NOTIFICATION TO CUSTOMER
            // =====================================================
            $this->sendBookingCreatedEmail($record);

            // If this is an advance payment booking, provide detailed info
            if ($record->isAdvancePayment()) {
                Notification::make()
                    ->info()
                    ->title(__('booking.notifications.booking_summary_title'))
                    ->body(__('booking.notifications.booking_summary_body', [
                        'booking_number' => $record->booking_number,
                        'total_amount' => number_format((float)$record->total_amount, 3),
                        'advance_amount' => number_format((float)$record->advance_amount, 3),
                        'balance_due' => number_format((float)$record->balance_due, 3)
                    ]))
                    ->persistent()
                    ->send();
            }
        } catch (Exception $e) {
            // Silently fail for afterCreate errors to not disrupt the flow
            Log::error('Error in afterCreate: ' . $e->getMessage());
        }
    }

    protected function sendBookingCreatedEmail(Booking $booking): void
    {
        // Skip if no customer email
        if (empty($booking->customer_email)) {
            Log::warning('Cannot send booking created email: No customer email', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
            ]);
            return;
        }

        try {
            // Load relationships for email
            $booking->load(['hall.owner', 'hall.city.region', 'extraServices', 'user']);

            // Use NotificationService (already exists in your project)
            $notificationService = app(NotificationService::class);
            $notificationService->sendBookingCreatedNotification($booking);

            // Show success notification in admin panel
            Notification::make()
                ->success()
                ->title(__('booking.notifications.email_sent_title'))
                ->body(__('booking.notifications.email_sent_body', [
                    'email' => $booking->customer_email
                ]))
                ->send();

            Log::info('Booking created email sent', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'customer_email' => $booking->customer_email,
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the booking creation
            Log::error('Failed to send booking created email', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'error' => $e->getMessage(),
            ]);

            // Notify admin about the email failure
            Notification::make()
                ->warning()
                ->title(__('booking.notifications.email_failed_title'))
                ->body(__('booking.notifications.email_failed_body', [
                    'error' => $e->getMessage()
                ]))
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
            ->modalHeading(__('booking.actions.create_modal_heading'))
            ->modalDescription(__('booking.actions.create_modal_description'))
            ->modalSubmitActionLabel(__('booking.actions.create_modal_submit_label'));
    }
}
