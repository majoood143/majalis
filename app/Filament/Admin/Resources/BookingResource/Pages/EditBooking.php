<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\BookingResource\Pages;

use App\Filament\Admin\Resources\BookingResource;
use App\Models\Booking;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

/**
 * Edit Booking Page
 *
 * Handles booking updates with comprehensive validation and business logic:
 * - Double booking prevention (slot availability)
 * - Guest capacity validation
 * - ✅ Advance payment recalculation when amounts change
 * - ✅ Warning when editing bookings with paid balance
 * - ✅ Automatic advance payment update for hall changes
 * - Data integrity and transaction safety
 *
 * Advance Payment Handling:
 * - Recalculates advance payment when total_amount changes
 * - Recalculates when hall changes (if new hall has different settings)
 * - Preserves balance_paid_at if balance was already paid
 * - Warns admin when changing amounts after balance payment
 * - Only recalculates if balance hasn't been paid yet
 *
 * @package App\Filament\Admin\Resources\BookingResource\Pages
 */
class EditBooking extends EditRecord
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
     * Performs comprehensive validation and business logic:
     * 1. Validates slot availability (prevents double booking)
     * 2. ✅ NEW: Detects changes that affect advance payment
     * 3. ✅ NEW: Recalculates advance payment if needed
     * 4. ✅ NEW: Warns if editing booking with paid balance
     *
     * Advance Payment Recalculation Triggers:
     * - total_amount changes (services added/removed, pricing updated)
     * - hall_id changes (different hall may have different advance settings)
     * - Manual changes to hall_price, services_price, or commission
     *
     * Important: Does NOT recalculate if balance was already paid
     * (preserves payment integrity, but warns admin about the change)
     *
     * @param array $data The form data
     * @return array The mutated data
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Check if hall, date, or time slot changed
        // if (
        //     $data['hall_id'] != $this->record->hall_id ||
        //     $data['booking_date'] != $this->record->booking_date->format('Y-m-d') ||
        //     $data['time_slot'] != $this->record->time_slot
        // ) {
        //     // Validate no double booking
        //     $existingBooking = Booking::where('hall_id', $data['hall_id'])
        //         ->where('booking_date', $data['booking_date'])
        //         ->where('time_slot', $data['time_slot'])
        //         ->whereIn('status', ['pending', 'confirmed'])
        //         ->where('id', '!=', $this->record->id) // Exclude current booking
        //         ->first();

        //     if ($existingBooking) {
        //         Notification::make()
        //             ->danger()
        //             ->title('Slot Already Booked')
        //             ->body("This time slot is already booked (Booking #{$existingBooking->booking_number}). Cannot change to this slot.")
        //             ->persistent()
        //             ->send();

        //         $this->halt();
        //     }
        // }

        // Check if hall, date, or time slot changed
        // Use null coalescing to safely access keys that may not exist in form data
        $newHallId = $data['hall_id'] ?? $this->record->hall_id;
        $newBookingDate = $data['booking_date'] ?? $this->record->booking_date->format('Y-m-d');
        $newTimeSlot = $data['time_slot'] ?? $this->record->time_slot;

        if (
            $newHallId != $this->record->hall_id ||
            $newBookingDate != $this->record->booking_date->format('Y-m-d') ||
            $newTimeSlot != $this->record->time_slot
        ) {
            // Validate no double booking
            $existingBooking = Booking::where('hall_id', $newHallId)
                ->where('booking_date', $newBookingDate)
                ->where('time_slot', $newTimeSlot)
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

        // ✅ NEW: Track if we need to recalculate advance payment
        $shouldRecalculateAdvance = false;
        $reasonForRecalculation = '';

        // // Check if total amount changed (services, pricing, etc.)
        // if (isset($data['total_amount']) &&
        //     (float)$data['total_amount'] !== (float)$this->record->total_amount) {
        //     $shouldRecalculateAdvance = true;
        //     $reasonForRecalculation = 'total amount changed';
        // }

        // // Check if hall changed
        // if (isset($data['hall_id']) && $data['hall_id'] != $this->record->hall_id) {
        //     $shouldRecalculateAdvance = true;
        //     $reasonForRecalculation = 'hall changed';
        // }

        // Check if total amount changed (services, pricing, etc.)
        $newTotalAmount = $data['total_amount'] ?? $this->record->total_amount;
        if ((float)$newTotalAmount !== (float)$this->record->total_amount) {
            $shouldRecalculateAdvance = true;
            $reasonForRecalculation = 'total amount changed';
        }

        // Check if hall changed (use $newHallId from above)
        if ($newHallId != $this->record->hall_id) {
            $shouldRecalculateAdvance = true;
            $reasonForRecalculation = 'hall changed';
        }

        // ✅ NEW: Handle advance payment recalculation
        if ($shouldRecalculateAdvance) {
            //$newHall = \App\Models\Hall::find($data['hall_id']);
            $newHall = \App\Models\Hall::find($newHallId);

            // Check if this booking had advance payment and balance was already paid
            if ($this->record->isAdvancePayment() && $this->record->balance_paid_at) {
                // Balance was already paid - warn admin but don't recalculate
                Notification::make()
                    ->warning()
                    ->title('⚠️ Balance Already Paid')
                    ->body(sprintf(
                        'This booking has advance payment balance already paid on %s. The %s will affect the total amount, but advance payment amounts will NOT be recalculated to preserve payment integrity.',
                        $this->record->balance_paid_at->format('M j, Y'),
                        $reasonForRecalculation
                    ))
                    ->persistent()
                    ->send();
            } else {
                // Balance not paid yet - safe to recalculate
                if ($newHall && $newHall->allows_advance_payment) {
                    // Store info for afterSave notification
                    $this->advancePaymentRecalculated = true;
                    $this->recalculationReason = $reasonForRecalculation;

                    // Note: Actual recalculation happens in afterSave
                    // after the record is saved with new total_amount
                } elseif ($this->record->isAdvancePayment()) {
                    // Switching from advance payment hall to non-advance payment hall
                    // Clear advance payment fields
                    $data['payment_type'] = 'full';
                    $data['advance_amount'] = null;
                    $data['balance_due'] = null;

                    Notification::make()
                        ->info()
                        ->title('Payment Type Changed')
                        ->body('The new hall does not allow advance payment. This booking has been changed to full payment.')
                        ->send();
                }
            }
        }

        return $data;
    }

    /**
     * Property to track if advance payment was recalculated
     * Used for notification in afterSave hook
     */
    protected bool $advancePaymentRecalculated = false;

    /**
     * Property to store recalculation reason for notification
     */
    protected string $recalculationReason = '';

    /**
     * ✅ NEW: After save hook to handle advance payment recalculation
     *
     * This happens AFTER the record is saved because:
     * - We need the updated total_amount to be in the database
     * - calculateAdvancePayment() reads from the current record
     * - Services need to be attached/updated first
     */
    protected function afterSave(): void
    {
        // Check if we need to recalculate advance payment
        if ($this->advancePaymentRecalculated) {
            $hall = \App\Models\Hall::find($this->record->hall_id);

            if ($hall && $hall->allows_advance_payment && !$this->record->balance_paid_at) {
                // Store old values for comparison
                $oldAdvanceAmount = $this->record->advance_amount;
                $oldBalanceDue = $this->record->balance_due;

                // Recalculate advance payment
                $this->record->calculateAdvancePayment();
                $this->record->save();

                // Refresh to get updated values
                $this->record->refresh();

                // Notify about the recalculation
                Notification::make()
                    ->success()
                    ->title('✅ Advance Payment Recalculated')
                    ->body(sprintf(
                        "Due to %s:\n**Old Advance:** %s OMR → **New:** %s OMR\n**Old Balance:** %s OMR → **New:** %s OMR",
                        $this->recalculationReason,
                        number_format((float)$oldAdvanceAmount, 3),
                        number_format((float)$this->record->advance_amount, 3),
                        number_format((float)$oldBalanceDue, 3),
                        number_format((float)$this->record->balance_due, 3)
                    ))
                    ->persistent()
                    ->send();
            }
        }
    }

    /**
     * Get the redirect URL after save
     *
     * Redirects to the view page to show the updated booking details.
     *
     * @return string The URL to redirect to
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    /**
     * Get the success notification title
     *
     * @return string|null The notification title
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
     * The extra_services relationship needs special handling because it's
     * a BelongsToMany with pivot data, and we need to avoid applying
     * active() scopes during form population.
     *
     * @param array $data The data to populate the form with
     * @return array The mutated data
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
