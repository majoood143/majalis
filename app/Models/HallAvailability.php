<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;

/**
 * HallAvailability Model
 *
 * Represents the availability status of a hall for a specific date and time slot.
 * Each record tracks whether a slot is available, blocked, booked, or under maintenance.
 *
 * ✅ FIX: Added block() and unblock() helper methods that were being called
 *    from AvailabilityCalendar.php and AvailabilityResource.php but were missing.
 *    Without these methods, manual blocking/unblocking from the owner panel
 *    threw BadMethodCallException, preventing status changes from being saved.
 *
 * ✅ FIX: Added isBooked() helper to check if a slot is booked (prevents
 *    accidental unblocking of booked slots).
 *
 * @property int         $id
 * @property int         $hall_id
 * @property \Carbon\Carbon $date
 * @property string      $time_slot      (morning|afternoon|evening|full_day)
 * @property bool        $is_available
 * @property string|null $reason         (blocked|booked|maintenance|holiday|private_event|renovation|other)
 * @property string|null $notes
 * @property float|null  $custom_price
 *
 * @property-read Hall   $hall
 * @property-read string $reason_label
 *
 * @package App\Models
 */
class HallAvailability extends Model
{
    use HasFactory;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hall_id',
        'date',
        'time_slot',
        'is_available',
        'reason',
        'notes',
        'custom_price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date'         => 'date',
        'is_available' => 'boolean',
        'custom_price' => 'decimal:2',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Get the hall this availability belongs to.
     *
     * @return BelongsTo<Hall, HallAvailability>
     */
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope: only unavailable (blocked/booked/maintenance) slots.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnavailable($query)
    {
        return $query->where('is_available', false);
    }

    /**
     * Scope: only available slots.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope: filter by specific date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDate($query, string $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope: filter by time slot.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $slot
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForSlot($query, string $slot)
    {
        return $query->where('time_slot', $slot);
    }

    /**
     * Scope: only records with a custom price.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCustomPrice($query)
    {
        return $query->whereNotNull('custom_price');
    }

    /**
     * Scope: only future (today or later) dates.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFuture($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    /**
     * Scope: only slots marked as booked.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBooked($query)
    {
        return $query->where('is_available', false)->where('reason', 'booked');
    }

    // =========================================================================
    // HELPER METHODS — ✅ FIX: These were missing and called from multiple files
    // =========================================================================

    /**
     * Block this availability slot.
     *
     * Sets is_available to false with the given reason.
     * Called from:
     *  - AvailabilityCalendar.php (owner resource page, toggleSlot method)
     *  - AvailabilityResource.php (bulk block action via unblock counterpart)
     *
     * @param string      $reason  Block reason (blocked|maintenance|holiday|private_event|renovation|other)
     * @param string|null $notes   Optional notes about why the slot is blocked
     * @return bool Whether the update was successful
     */
    public function block(string $reason = 'blocked', ?string $notes = null): bool
    {
        return $this->update([
            'is_available' => false,
            'reason'       => $reason,
            'notes'        => $notes ?? $this->notes,
        ]);
    }

    /**
     * Unblock this availability slot (make it available again).
     *
     * Sets is_available to true and clears the reason/notes.
     * Will NOT unblock a slot that is booked (reason = 'booked') to prevent
     * accidentally freeing a slot that has an active booking.
     *
     * Called from:
     *  - AvailabilityCalendar.php (owner resource page, toggleSlot method)
     *  - AvailabilityResource.php (bulk unblock action)
     *
     * @return bool Whether the update was successful
     */
    public function unblock(): bool
    {
        // Safety: don't unblock booked slots via this method
        // Booked slots should only be freed when a booking is cancelled
        if ($this->isBooked()) {
            return false;
        }

        return $this->update([
            'is_available' => true,
            'reason'       => null,
            'notes'        => null,
        ]);
    }

    /**
     * Mark this slot as booked by a booking.
     *
     * Used by BookingObserver when a booking is created or confirmed.
     *
     * @param string|null $notes Optional notes (e.g. booking number)
     * @return bool
     */
    public function markAsBooked(?string $notes = null): bool
    {
        return $this->update([
            'is_available' => false,
            'reason'       => 'booked',
            'notes'        => $notes,
        ]);
    }

    /**
     * Release a booked slot (make available again).
     *
     * Used by BookingObserver when a booking is cancelled or rejected.
     *
     * @return bool
     */
    public function releaseBooking(): bool
    {
        // Only release if currently marked as booked
        if ($this->reason !== 'booked') {
            return false;
        }

        return $this->update([
            'is_available' => true,
            'reason'       => null,
            'notes'        => null,
        ]);
    }

    /**
     * Check if this slot is currently booked.
     *
     * @return bool
     */
    public function isBooked(): bool
    {
        return !$this->is_available && $this->reason === 'booked';
    }

    /**
     * Check if this slot is blocked (not booked, but unavailable).
     *
     * @return bool
     */
    public function isBlocked(): bool
    {
        return !$this->is_available && $this->reason !== 'booked';
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Get a human-readable label for the block reason.
     *
     * @return string
     */
    public function getReasonLabelAttribute(): string
    {
        return match ($this->reason) {
            'maintenance'   => __('Under Maintenance'),
            'blocked'       => __('Blocked by Owner'),
            'booked'        => __('Booked'),
            'holiday'       => __('Holiday'),
            'private_event' => __('Private Event'),
            'renovation'    => __('Renovation'),
            'custom'        => __('Custom Block'),
            'other'         => __('Other'),
            default         => $this->reason ?? __('Unavailable'),
        };
    }

    /**
     * Check whether this slot has a custom price set.
     *
     * @return bool
     */
    public function hasCustomPrice(): bool
    {
        return $this->custom_price !== null;
    }

    /**
     * Get the effective price for this slot.
     *
     * Falls back to the hall's default slot price if no custom price is set.
     *
     * @return float
     */
    public function getEffectivePrice(): float
    {
        return $this->custom_price ?? $this->hall->getPriceForSlot($this->time_slot);
    }
}
