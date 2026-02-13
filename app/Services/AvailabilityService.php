<?php

declare(strict_types=1);

/**
 * AvailabilityService - Core business logic for hall availability checking.
 *
 * This service centralizes all availability-related queries so that both
 * the customer search page and the booking form use the same truth source.
 * It cross-references three data sources:
 *   1. hall_availabilities  — owner-defined open slots per date
 *   2. bookings             — confirmed/pending reservations
 *   3. halls.pricing_override — slot-specific pricing
 *
 * @package App\Services
 * @since   2.0.0  (Smart Search feature)
 *
 * Installation path: app/Services/AvailabilityService.php
 */

namespace App\Services;

use App\Models\Hall;
use App\Models\Booking;
use App\Models\HallAvailability;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

class AvailabilityService
{
    /**
     * The four time slots supported by the platform.
     *
     * @var array<string, string>
     */
    public const TIME_SLOTS = [
        'morning'   => 'Morning (8 AM – 12 PM)',
        'afternoon' => 'Afternoon (12 PM – 5 PM)',
        'evening'   => 'Evening (5 PM – 11 PM)',
        'full_day'  => 'Full Day (8 AM – 11 PM)',
    ];

    /**
     * Booking statuses that "consume" a slot (block availability).
     *
     * @var array<string>
     */
    private const BLOCKING_STATUSES = ['confirmed', 'pending'];

    // ──────────────────────────────────────────────────────────
    //  PUBLIC API — used by controllers and views
    // ──────────────────────────────────────────────────────────

    /**
     * Search halls available on a given date and (optional) time slot.
     *
     * Returns a Collection of halls sorted by:
     *   1. Available halls first (most available slots → top)
     *   2. Featured halls get a boost
     *   3. Then by average_rating DESC
     *
     * Each Hall object gets three dynamic attributes appended:
     *   - available_slots : array of slot keys that are free
     *   - slot_prices     : associative array  [slot => price]
     *   - is_available    : bool (true if at least 1 slot is free)
     *
     * @param  string      $date       ISO date string (Y-m-d)
     * @param  string|null $timeSlot   Specific slot key, or null for "any"
     * @param  int|null    $cityId     Filter by city
     * @param  int|null    $minGuests  Minimum capacity required
     * @param  float|null  $maxPrice   Maximum price per slot (OMR)
     * @param  array|null  $featureIds Filter by hall feature IDs
     * @return Collection<Hall>
     */
    public function searchAvailableHalls(
        string  $date,
        ?string $timeSlot = null,
        ?int    $cityId = null,
        ?int    $minGuests = null,
        ?float  $maxPrice = null,
        ?array  $featureIds = null,
    ): Collection {
        // ── Step 1: Base query — active, non-deleted halls with relationships ──
        $query = Hall::query()
            ->with(['city.region', 'owner'])
            ->where('is_active', true)
            ->whereNull('deleted_at');

        // ── Step 2: Apply optional filters ──
        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        if ($minGuests) {
            $query->where('capacity_max', '>=', $minGuests);
        }

        if ($featureIds && count($featureIds) > 0) {
            /**
             * The `features` column is a JSON array of feature IDs.
             * We check that the hall's features contain ALL requested IDs.
             */
            foreach ($featureIds as $featureId) {
                $query->whereJsonContains('features', (int) $featureId);
            }
        }

        // ── Step 3: Fetch all candidate halls ──
        $halls = $query->get();

        // ── Step 4: For each hall, compute availability + pricing ──
        $halls->each(function (Hall $hall) use ($date, $timeSlot) {
            $availableSlots = $this->getAvailableSlotsForHall($hall, $date);
            $slotPrices     = $this->getSlotPrices($hall, $date);

            // If a specific slot was requested, narrow down
            if ($timeSlot) {
                $availableSlots = in_array($timeSlot, $availableSlots, true)
                    ? [$timeSlot]
                    : [];
            }

            // Attach dynamic attributes to the model
            $hall->setAttribute('available_slots', $availableSlots);
            $hall->setAttribute('slot_prices', $slotPrices);
            $hall->setAttribute('is_available', count($availableSlots) > 0);
        });

        // ── Step 5: Apply max price filter (post-query, needs slot prices) ──
        if ($maxPrice) {
            $halls = $halls->map(function (Hall $hall) use ($maxPrice) {
                // Keep only slots within budget
                $affordable = array_filter(
                    $hall->available_slots,
                    fn(string $slot) => ($hall->slot_prices[$slot] ?? PHP_FLOAT_MAX) <= $maxPrice
                );
                $hall->setAttribute('available_slots', array_values($affordable));
                $hall->setAttribute('is_available', count($affordable) > 0);

                return $hall;
            });
        }

        // ── Step 6: Sort — available first, then featured, then rating ──
        return $halls->sortBy([
            // Available halls first (false = 0, true = 1, so we sort DESC)
            fn(Hall $a, Hall $b) => $b->is_available <=> $a->is_available,
            // More available slots = higher rank
            fn(Hall $a, Hall $b) => count($b->available_slots) <=> count($a->available_slots),
            // Featured halls get priority
            fn(Hall $a, Hall $b) => $b->is_featured <=> $a->is_featured,
            // Then by rating
            fn(Hall $a, Hall $b) => (float) $b->average_rating <=> (float) $a->average_rating,
        ])->values();
    }

    /**
     * Get all available time slots for a hall on a specific date.
     *
     * Logic:
     *   1. Check hall_availabilities table for owner-defined open slots
     *   2. Remove slots that are already booked
     *   3. Apply full_day ↔ individual slot conflict rules
     *
     * @param  Hall   $hall
     * @param  string $date  ISO date (Y-m-d)
     * @return array<string>  List of available slot keys
     */
    public function getAvailableSlotsForHall(Hall $hall, string $date): array
    {
        // ── Fetch owner-defined availability records for this date ──
        $availabilityRecords = HallAvailability::where('hall_id', $hall->id)
            ->where('date', $date)
            ->where('is_available', true)
            ->pluck('time_slot')
            ->toArray();

        // If no availability records exist for this date, the hall is closed
        if (empty($availabilityRecords)) {
            return [];
        }

        // ── Fetch booked slots (confirmed or pending) ──
        $bookedSlots = $this->getBookedSlots($hall->id, $date);

        // ── Determine which slots are truly free ──
        $available = [];

        foreach ($availabilityRecords as $slot) {
            if ($this->isSlotFree($slot, $bookedSlots)) {
                $available[] = $slot;
            }
        }

        return $available;
    }

    /**
     * Check if a single specific slot is available for a hall on a date.
     *
     * @param  Hall   $hall
     * @param  string $date
     * @param  string $timeSlot
     * @return bool
     */
    public function isSlotAvailable(Hall $hall, string $date, string $timeSlot): bool
    {
        $availableSlots = $this->getAvailableSlotsForHall($hall, $date);

        return in_array($timeSlot, $availableSlots, true);
    }

    /**
     * Get pricing for each time slot on a given date.
     *
     * Priority:
     *   1. hall_availabilities.custom_price  (date-specific override)
     *   2. halls.pricing_override            (slot-level default override)
     *   3. halls.price_per_slot              (base fallback)
     *
     * @param  Hall   $hall
     * @param  string $date
     * @return array<string, float>  [slot_key => price_in_omr]
     */
    public function getSlotPrices(Hall $hall, string $date): array
    {
        $prices         = [];
        $basePrice      = (float) $hall->price_per_slot;
        $pricingOverride = is_array($hall->pricing_override)
            ? $hall->pricing_override
            : (json_decode((string) $hall->pricing_override, true) ?? []);

        // Fetch any custom prices from hall_availabilities for this date
        $customPrices = HallAvailability::where('hall_id', $hall->id)
            ->where('date', $date)
            ->whereNotNull('custom_price')
            ->pluck('custom_price', 'time_slot')
            ->toArray();

        foreach (array_keys(self::TIME_SLOTS) as $slot) {
            // Priority 1: Date-specific custom price
            if (isset($customPrices[$slot])) {
                $prices[$slot] = (float) $customPrices[$slot];
                continue;
            }

            // Priority 2: Hall-level slot override
            if (isset($pricingOverride[$slot]) && $pricingOverride[$slot] !== null && $pricingOverride[$slot] !== '') {
                $prices[$slot] = (float) $pricingOverride[$slot];
                continue;
            }

            // Priority 3: Base price
            $prices[$slot] = $basePrice;
        }

        return $prices;
    }

    /**
     * Suggest nearby dates with availability when the requested date has no results.
     *
     * Scans ±7 days from the requested date and returns dates that have
     * at least one hall with availability matching the filters.
     *
     * @param  string      $date       The originally requested date
     * @param  string|null $timeSlot   Specific slot or null
     * @param  int|null    $cityId     City filter
     * @param  int|null    $minGuests  Guest count filter
     * @param  int         $range      Days to scan in each direction (default 7)
     * @return array<array{date: string, hall_count: int, formatted: string}>
     */
    public function suggestNearbyDates(
        string  $date,
        ?string $timeSlot = null,
        ?int    $cityId = null,
        ?int    $minGuests = null,
        int     $range = 7,
    ): array {
        $suggestions = [];
        $baseDate    = Carbon::parse($date);
        $today       = Carbon::today();

        for ($i = -$range; $i <= $range; $i++) {
            // Skip the original date itself
            if ($i === 0) {
                continue;
            }

            $checkDate = $baseDate->copy()->addDays($i);

            // Skip past dates
            if ($checkDate->lt($today)) {
                continue;
            }

            $dateStr = $checkDate->format('Y-m-d');

            // ── Quick count: how many halls have availability records on this date? ──
            $hallCount = $this->countAvailableHallsOnDate($dateStr, $timeSlot, $cityId, $minGuests);

            if ($hallCount > 0) {
                $suggestions[] = [
                    'date'       => $dateStr,
                    'hall_count' => $hallCount,
                    'formatted'  => $checkDate->format('D, M j'),  // e.g. "Sat, Feb 14"
                    'day_name'   => $checkDate->format('l'),
                    'diff'       => $i > 0 ? "+{$i} day(s)" : "{$i} day(s)",
                ];
            }
        }

        // Sort by closest to original date
        usort($suggestions, fn($a, $b) => abs((int) Carbon::parse($a['date'])->diffInDays($baseDate))
            <=> abs((int) Carbon::parse($b['date'])->diffInDays($baseDate)));

        // Return max 6 suggestions
        return array_slice($suggestions, 0, 6);
    }

    // ──────────────────────────────────────────────────────────
    //  PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────

    /**
     * Get all booked (consumed) slots for a hall on a date.
     *
     * @param  int    $hallId
     * @param  string $date
     * @return array<string>
     */
    private function getBookedSlots(int $hallId, string $date): array
    {
        return Booking::where('hall_id', $hallId)
            ->where('booking_date', $date)
            ->whereIn('status', self::BLOCKING_STATUSES)
            ->whereNull('deleted_at')
            ->pluck('time_slot')
            ->toArray();
    }

    /**
     * Determine if a slot is free considering booking conflict rules.
     *
     * Conflict rules:
     *   - If 'full_day' is booked → ALL individual slots are blocked
     *   - If ANY individual slot is booked → 'full_day' is blocked
     *   - If a specific slot is booked → that slot is blocked
     *
     * @param  string        $slot        The slot to check
     * @param  array<string> $bookedSlots Currently booked slots
     * @return bool
     */
    private function isSlotFree(string $slot, array $bookedSlots): bool
    {
        // If full_day is already booked, nothing is available
        if (in_array('full_day', $bookedSlots, true)) {
            return false;
        }

        // If checking full_day, it's blocked if ANY individual slot is taken
        if ($slot === 'full_day') {
            $individualSlots = ['morning', 'afternoon', 'evening'];
            foreach ($individualSlots as $individual) {
                if (in_array($individual, $bookedSlots, true)) {
                    return false;
                }
            }
            return true;
        }

        // For individual slots, just check if that specific slot is taken
        return !in_array($slot, $bookedSlots, true);
    }

    /**
     * Quick count of halls with availability on a date (for suggestions).
     *
     * This uses a lightweight query — no full slot computation.
     *
     * @param  string      $date
     * @param  string|null $timeSlot
     * @param  int|null    $cityId
     * @param  int|null    $minGuests
     * @return int
     */
    private function countAvailableHallsOnDate(
        string  $date,
        ?string $timeSlot = null,
        ?int    $cityId = null,
        ?int    $minGuests = null,
    ): int {
        $query = Hall::query()
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->whereHas('availabilities', function (Builder $q) use ($date, $timeSlot) {
                $q->where('date', $date)->where('is_available', true);

                if ($timeSlot) {
                    $q->where('time_slot', $timeSlot);
                }
            });

        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        if ($minGuests) {
            $query->where('capacity_max', '>=', $minGuests);
        }

        return $query->count();
    }
}
