<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\User;
use App\Models\GuestSession;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * HasGuestBooking Trait
 *
 * Adds guest booking functionality to the Booking model.
 * This trait provides methods for handling bookings made without
 * user authentication, including token generation, account linking,
 * and guest-specific queries.
 *
 * Usage:
 * Add `use HasGuestBooking;` to the Booking model class.
 *
 * Features:
 * - Guest token generation and validation
 * - Account linking when guest registers
 * - Guest booking scopes for filtering
 * - Helper methods for guest status checks
 *
 * @package App\Models\Traits
 * @version 1.0.0
 *
 * @property bool $is_guest_booking
 * @property string|null $guest_token
 * @property \Carbon\Carbon|null $guest_token_expires_at
 * @property \Carbon\Carbon|null $account_created_at
 */
trait HasGuestBooking
{
    /**
     * Token expiration time in days.
     *
     * @var int
     */
    public const GUEST_TOKEN_EXPIRY_DAYS = 365;

    // =========================================================================
    // BOOT METHOD
    // =========================================================================

    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootHasGuestBooking(): void
    {
        // Auto-generate guest token for guest bookings
        static::creating(function ($booking): void {
            if ($booking->is_guest_booking && empty($booking->guest_token)) {
                $booking->guest_token = self::generateGuestToken();
                $booking->guest_token_expires_at = now()->addDays(self::GUEST_TOKEN_EXPIRY_DAYS);
            }
        });
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Get the guest session associated with this booking.
     *
     * @return HasOne
     */
    public function guestSession(): HasOne
    {
        return $this->hasOne(GuestSession::class, 'booking_id');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope to filter only guest bookings.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGuestBookings($query)
    {
        return $query->where('is_guest_booking', true);
    }

    /**
     * Scope to filter only registered user bookings.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRegisteredBookings($query)
    {
        return $query->where('is_guest_booking', false);
    }

    /**
     * Scope to filter guest bookings by email.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $email
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGuestByEmail($query, string $email)
    {
        return $query->guestBookings()
            ->where('customer_email', strtolower($email));
    }

    /**
     * Scope to filter guest bookings with active tokens.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithActiveGuestToken($query)
    {
        return $query->guestBookings()
            ->where(function ($q) {
                $q->whereNull('guest_token_expires_at')
                    ->orWhere('guest_token_expires_at', '>', now());
            });
    }

    /**
     * Scope to filter unlinked guest bookings (not converted to user).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnlinkedGuest($query)
    {
        return $query->guestBookings()
            ->whereNull('user_id')
            ->whereNull('account_created_at');
    }

    // =========================================================================
    // GUEST STATUS METHODS
    // =========================================================================

    /**
     * Check if this is a guest booking.
     *
     * @return bool
     */
    public function isGuestBooking(): bool
    {
        return (bool) $this->is_guest_booking;
    }

    /**
     * Check if this is a registered user booking.
     *
     * @return bool
     */
    public function isRegisteredBooking(): bool
    {
        return !$this->is_guest_booking && $this->user_id !== null;
    }

    /**
     * Check if guest has been converted to registered user.
     *
     * @return bool
     */
    public function hasConvertedToUser(): bool
    {
        return $this->is_guest_booking && $this->account_created_at !== null;
    }

    /**
     * Check if guest token is valid and not expired.
     *
     * @return bool
     */
    public function hasValidGuestToken(): bool
    {
        if (!$this->is_guest_booking || empty($this->guest_token)) {
            return false;
        }

        if ($this->guest_token_expires_at === null) {
            return true;
        }

        return $this->guest_token_expires_at->isFuture();
    }

    /**
     * Check if guest can create an account from this booking.
     *
     * @return bool
     */
    public function canCreateAccount(): bool
    {
        // Must be a guest booking that hasn't been linked yet
        if (!$this->is_guest_booking || $this->user_id !== null) {
            return false;
        }

        // Check if email is already registered
        return !User::where('email', strtolower($this->customer_email))->exists();
    }

    // =========================================================================
    // TOKEN METHODS
    // =========================================================================

    /**
     * Generate a unique guest token.
     *
     * @return string 64-character secure token
     */
    public static function generateGuestToken(): string
    {
        do {
            $token = bin2hex(random_bytes(32));
        } while (self::where('guest_token', $token)->exists());

        return $token;
    }

    /**
     * Find booking by guest token.
     *
     * @param string $token Guest token
     * @return static|null
     */
    public static function findByGuestToken(string $token): ?self
    {
        return self::where('guest_token', $token)
            ->withActiveGuestToken()
            ->first();
    }

    /**
     * Refresh the guest token with new expiration.
     *
     * @return bool
     */
    public function refreshGuestToken(): bool
    {
        if (!$this->is_guest_booking) {
            return false;
        }

        return $this->update([
            'guest_token' => self::generateGuestToken(),
            'guest_token_expires_at' => now()->addDays(self::GUEST_TOKEN_EXPIRY_DAYS),
        ]);
    }

    /**
     * Invalidate the guest token (e.g., after account creation).
     *
     * @return bool
     */
    public function invalidateGuestToken(): bool
    {
        return $this->update([
            'guest_token_expires_at' => now(),
        ]);
    }

    // =========================================================================
    // ACCOUNT LINKING METHODS
    // =========================================================================

    /**
     * Link this guest booking to a user account.
     *
     * @param User $user The user to link to
     * @param bool $isNewAccount Whether this is a newly created account
     * @return bool
     */
    public function linkToUser(User $user, bool $isNewAccount = false): bool
    {
        if (!$this->is_guest_booking) {
            return false;
        }

        $data = [
            'user_id' => $user->id,
        ];

        if ($isNewAccount) {
            $data['account_created_at'] = now();
        }

        return $this->update($data);
    }

    /**
     * Link all guest bookings with matching email to a user.
     *
     * @param User $user The user to link to
     * @return int Number of bookings linked
     */
    public static function linkGuestBookingsToUser(User $user): int
    {
        return self::guestByEmail($user->email)
            ->whereNull('user_id')
            ->update([
                'user_id' => $user->id,
                'account_created_at' => now(),
            ]);
    }

    // =========================================================================
    // STATIC QUERY METHODS
    // =========================================================================

    /**
     * Get pending guest bookings count for an email.
     *
     * @param string $email Email address
     * @return int
     */
    public static function countPendingGuestBookings(string $email): int
    {
        return self::guestByEmail($email)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('booking_date', '>=', now()->toDateString())
            ->count();
    }

    /**
     * Check if guest has reached pending booking limit.
     *
     * @param string $email Email address
     * @param int $limit Maximum allowed (default: 3)
     * @return bool
     */
    public static function guestHasReachedLimit(string $email, int $limit = 3): bool
    {
        return self::countPendingGuestBookings($email) >= $limit;
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Get the booking type label (Guest or Registered).
     *
     * @return string
     */
    public function getBookingTypeLabelAttribute(): string
    {
        if ($this->is_guest_booking) {
            if ($this->hasConvertedToUser()) {
                return __('Guest (Converted)');
            }
            return __('Guest');
        }

        return __('Registered');
    }

    /**
     * Get the customer display name with type indicator.
     *
     * @return string
     */
    public function getCustomerDisplayWithTypeAttribute(): string
    {
        $name = $this->customer_name ?? $this->user?->name ?? __('Unknown');

        if ($this->is_guest_booking) {
            return $name . ' ' . __('(Guest)');
        }

        return $name;
    }

    /**
     * Get the guest booking URL using token.
     *
     * @return string|null
     */
    public function getGuestBookingUrlAttribute(): ?string
    {
        if (!$this->is_guest_booking || empty($this->guest_token)) {
            return null;
        }

        return route('guest.booking.show', ['guest_token' => $this->guest_token]);
    }
}
