<?php

declare(strict_types=1);

namespace App\Observers;

use Throwable;
use App\Models\Booking;
use App\Models\HallAvailability;
use Illuminate\Support\Facades\Log;

/**
 * BookingObserver
 *
 * ✅ FIX: Syncs booking status changes to the HallAvailability table.
 *
 * Problem:
 *   When a booking was created (pending/confirmed), the HallAvailability
 *   record was NEVER updated to is_available=false, reason='booked'.
 *   This meant the AvailabilityCalendarWidget only showed "available"
 *   and "past" slots — blocked, booked, maintenance statuses never appeared.
 *
 * Solution:
 *   This observer listens to Booking model events and automatically
 *   updates the corresponding HallAvailability record:
 *   - Booking created (pending/confirmed) → mark slot as booked
 *   - Booking confirmed → mark slot as booked (if not already)
 *   - Booking cancelled/rejected → release the slot back to available
 *   - Booking completed → keep as booked (historical record)
 *
 * Registration:
 *   Add to GuestBookingServiceProvider or AppServiceProvider boot():
 *   \App\Models\Booking::observe(\App\Observers\BookingObserver::class);
 *
 * @package App\Observers
 * @version 1.0.0
 */
class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     *
     * When a new booking is created with an active status (pending/confirmed),
     * mark the corresponding HallAvailability slot as booked.
     *
     * @param Booking $booking
     * @return void
     */
    public function created(Booking $booking): void
    {
        // Only mark as booked for active booking statuses
        if (in_array($booking->status, ['pending', 'confirmed', 'paid'], true)) {
            $this->markSlotAsBooked($booking);
        }
    }

    /**
     * Handle the Booking "updated" event.
     *
     * Reacts to status changes:
     * - Becomes cancelled/rejected → release the availability slot
     * - Becomes confirmed/paid    → ensure slot is marked as booked
     *
     * @param Booking $booking
     * @return void
     */
    public function updated(Booking $booking): void
    {
        // Only act if status actually changed
        if (!$booking->wasChanged('status')) {
            return;
        }

        // Booking::confirm()/cancel()/complete() pass a BookingStatus enum case
        // into update(); since 'status' isn't enum-cast on the model, the
        // in-memory attribute is the enum instance, not its string value.
        $newStatus = $booking->status instanceof \App\Enums\BookingStatus
            ? $booking->status->value
            : $booking->status;

        // Status moved to cancelled or rejected → free the slot
        if (in_array($newStatus, ['cancelled', 'rejected', 'refunded'], true)) {
            $booking->bookingDays()->update(['slot_lock' => null]);
            $this->releaseSlot($booking);
            return;
        }

        // Status moved to confirmed or paid → ensure slot is marked booked
        if (in_array($newStatus, ['confirmed', 'paid'], true)) {
            // Re-claims the days in the unique key; throws if another booking took them meanwhile
            $booking->bookingDays()->update(['slot_lock' => 1]);
            $this->markSlotAsBooked($booking);
            return;
        }

        // Status is completed → keep as booked (no change needed)
        // This preserves the historical record in the calendar
    }

    /**
     * Handle the Booking "deleted" event (soft delete).
     *
     * When a booking is soft-deleted, release the availability slot.
     *
     * @param Booking $booking
     * @return void
     */
    public function deleted(Booking $booking): void
    {
        $booking->bookingDays()->update(['slot_lock' => null]);
        $this->releaseSlot($booking);
    }

    /**
     * Resolve the day/slot pairs to sync for this booking.
     *
     * Uses BookingDay rows when they exist (covers both ordinary single-day
     * and multi-day bookings). Falls back to the legacy single booking_date/
     * time_slot pair for historical bookings created before BookingDay
     * existed, or for the brief window during creation before BookingDay
     * rows have been inserted yet (see BookingService::createBooking()).
     *
     * @param Booking $booking
     * @return array<int, array{date: string, time_slot: string}>
     */
    protected function resolveDays(Booking $booking): array
    {
        $bookingDays = $booking->bookingDays;

        if ($bookingDays->isNotEmpty()) {
            return $bookingDays->map(fn($day) => [
                'date'      => $day->date->format('Y-m-d'),
                'time_slot' => $day->time_slot,
            ])->all();
        }

        return [[
            'date'      => $booking->booking_date->format('Y-m-d'),
            'time_slot' => $booking->time_slot,
        ]];
    }

    /**
     * Mark the HallAvailability slot(s) as booked.
     *
     * Uses updateOrCreate to handle the case where:
     * - An availability record exists → update it to booked
     * - No availability record exists → create one as booked
     *
     * Also handles full_day bookings by marking all individual slots, for
     * every day in resolveDays().
     *
     * Public so BookingService can explicitly re-sync after inserting
     * BookingDay rows for a multi-day booking (the 'created' event fires
     * before those rows exist, so only day 1 is visible at that point).
     *
     * @param Booking $booking
     * @return void
     */
    public function markSlotAsBooked(Booking $booking): void
    {
        try {
            foreach ($this->resolveDays($booking) as $day) {
                $slotsToMark = $this->getSlotsToMark($day['time_slot']);

                foreach ($slotsToMark as $slot) {
                    HallAvailability::updateOrCreate(
                        [
                            'hall_id'   => $booking->hall_id,
                            'date'      => $day['date'],
                            'time_slot' => $slot,
                        ],
                        [
                            'is_available' => false,
                            'reason'       => 'booked',
                            'notes'        => "Booking #{$booking->booking_number}",
                        ]
                    );
                }

                Log::info('HallAvailability synced: slot marked as booked', [
                    'booking_id'     => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'hall_id'        => $booking->hall_id,
                    'date'           => $day['date'],
                    'time_slot'      => $day['time_slot'],
                    'slots_marked'   => $slotsToMark,
                ]);
            }
        } catch (Throwable $e) {
            // Log error but don't break the booking flow
            Log::error('Failed to sync HallAvailability on booking creation', [
                'booking_id' => $booking->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    /**
     * Release the HallAvailability slot(s) back to available, for every
     * day in resolveDays().
     *
     * Only releases slots that are marked with reason='booked'.
     * Does NOT release manually blocked slots (maintenance, holiday, etc.).
     *
     * @param Booking $booking
     * @return void
     */
    protected function releaseSlot(Booking $booking): void
    {
        try {
            foreach ($this->resolveDays($booking) as $day) {
                $slotsToRelease = $this->getSlotsToMark($day['time_slot']);

                // Only release slots that were marked as 'booked'
                // Don't accidentally unblock maintenance/holiday/etc.
                $updated = HallAvailability::where('hall_id', $booking->hall_id)
                    ->where('date', $day['date'])
                    ->whereIn('time_slot', $slotsToRelease)
                    ->where('reason', 'booked')
                    ->update([
                        'is_available' => true,
                        'reason'       => null,
                        'notes'        => null,
                    ]);

                if ($updated > 0) {
                    Log::info('HallAvailability synced: slot released', [
                        'booking_id'     => $booking->id,
                        'booking_number' => $booking->booking_number,
                        'hall_id'        => $booking->hall_id,
                        'date'           => $day['date'],
                        'time_slot'      => $day['time_slot'],
                        'slots_released' => $updated,
                    ]);
                }
            }
        } catch (Throwable $e) {
            Log::error('Failed to sync HallAvailability on booking cancellation', [
                'booking_id' => $booking->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    /**
     * Determine which time slots to mark based on the booking's time_slot.
     *
     * For full_day bookings, we mark ALL individual slots as booked
     * so they correctly show as unavailable in the calendar.
     *
     * For individual slots (morning/afternoon/evening), we only mark
     * that specific slot. The full_day conflict check is handled
     * dynamically at booking time by BookingService::checkAvailability().
     *
     * @param string $timeSlot The booking's time slot
     * @return array<string> List of slot keys to mark
     */
    protected function getSlotsToMark(string $timeSlot): array
    {
        // Full day booking → mark ALL slots including full_day
        // This ensures the calendar correctly shows all sub-slots as booked
        if ($timeSlot === 'full_day') {
            return ['morning', 'afternoon', 'evening', 'full_day'];
        }

        // Individual slot → only mark that specific slot
        // The full_day availability is checked dynamically at booking time
        return [$timeSlot];
    }
}
