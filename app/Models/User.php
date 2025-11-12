<?php

namespace App\Models;

use App\Enums\UserRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;

/**
 * User Model
 *
 * This model represents authenticated users in the Majalis system.
 * It supports both custom role enums (for basic role identification)
 * and Spatie Permission roles (for granular permission management via Shield).
 *
 * Role System:
 * - Custom 'role' enum field: Basic role identification (UserRole enum)
 * - Spatie Permission roles: Granular permissions managed by Filament Shield
 *
 * The system uses BOTH:
 * - Enum roles for simple role checks (isAdmin(), isHallOwner(), etc.)
 * - Permission roles for detailed access control (view_hall, create_hall, etc.)
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property UserRole $role Custom role enum
 * @property string $phone
 * @property string $phone_country_code
 * @property bool $is_active
 * @property \DateTime|null $email_verified_at
 * @property \DateTime|null $phone_verified_at
 * @property \DateTime|null $deleted_at
 */
class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /**
     * Use Laravel's core traits for factory, notifications, and soft deletes
     */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * HasRoles trait from Spatie Permission package
     * Provides role and permission functionality for Shield
     *
     * IMPORTANT: This trait provides its own hasRole() method
     * Our custom hasRole() method is renamed to hasCustomRole()
     */
    use HasRoles;

    /**
     * HasPanelShield trait from Filament Shield
     * Integrates Shield's panel-level access control
     */
    use HasPanelShield;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'phone_country_code',
        'is_active',
        'phone_verified_at',
        'language_preference',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
        'is_active' => 'boolean',
    ];

    // ==================== FILAMENT PANEL ACCESS ====================

    /**
     * Determine if the user can access the given Filament panel.
     *
     * This method implements Filament's access control and integrates
     * with both the custom role enum and Shield permission system.
     *
     * Access Logic:
     * - Admin Panel: Requires admin enum role + active status + Shield permissions
     * - Owner Panel: Requires hall_owner enum role + active + verified owner status
     *
     * @param Panel $panel The Filament panel instance
     * @return bool True if user can access the panel, false otherwise
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Check if user is active (applies to all panels)
        if (!$this->is_active) {
            return false;
        }

        // Admin panel access control
        if ($panel->getId() === 'admin') {
            // Must be an admin (enum role check)
            if (!$this->isAdmin()) {
                return false;
            }

            // Check Shield permissions
            // Super admins always have access
            if ($this->hasRole('super_admin')) {
                return true;
            }

            // Other users need panel_user permission
            // This is set in Shield configuration
            return $this->can('page_Dashboard')
                || $this->hasPermissionTo('page_Dashboard')
                || $this->hasAnyRole(['super_admin', 'admin', 'panel_user']);
        }

        // Hall Owner panel access control
        if ($panel->getId() === 'owner') {
            // Must be a hall owner (enum role check)
            if (!$this->isHallOwner()) {
                return false;
            }

            // Must have a verified hall owner profile
            if (!$this->hallOwner || !$this->hallOwner->is_verified) {
                return false;
            }

            return true;
        }

        // Deny access to unknown panels
        return false;
    }

    /**
     * Get the user's preferred language
     *
     * @return string
     */
    public function getPreferredLanguage(): string
    {
        return $this->language_preference ?? config('app.locale', 'en');
    }

    /**
     * Set the user's preferred language
     *
     * @param string $language
     * @return void
     */
    public function setPreferredLanguage(string $language): void
    {
        $this->update(['language_preference' => $language]);
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get all bookings made by this user (as a customer).
     *
     * @return HasMany
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all halls owned by this user.
     *
     * @return HasMany
     */
    public function halls(): HasMany
    {
        return $this->hasMany(Hall::class, 'owner_id');
    }

    /**
     * Get the hall owner profile for this user.
     *
     * @return HasOne
     */
    public function hallOwner(): HasOne
    {
        return $this->hasOne(HallOwner::class);
    }

    /**
     * Get all reviews written by this user.
     *
     * @return HasMany
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get all hall owners verified by this user (admin action).
     *
     * @return HasMany
     */
    public function verifiedOwners(): HasMany
    {
        return $this->hasMany(HallOwner::class, 'verified_by');
    }

    // ==================== CUSTOM ROLE METHODS (Enum-based) ====================

    /**
     * Check if user has admin role (custom enum check).
     *
     * This checks the 'role' enum column, not Spatie permissions.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    /**
     * Check if user has hall owner role (custom enum check).
     *
     * @return bool
     */
    public function isHallOwner(): bool
    {
        return $this->role === UserRole::HALL_OWNER;
    }

    /**
     * Check if user has customer role (custom enum check).
     *
     * @return bool
     */
    public function isCustomer(): bool
    {
        return $this->role === UserRole::CUSTOMER;
    }

    /**
     * Check if user has a specific custom role (enum-based).
     *
     * RENAMED from hasRole() to avoid conflict with Spatie's hasRole().
     * Use this method for checking custom enum roles.
     * Use Spatie's hasRole() for checking permission-based roles.
     *
     * @param string|UserRole $role The role to check
     * @return bool True if user has the custom role, false otherwise
     */
    public function hasCustomRole(string|UserRole $role): bool
    {
        if ($role instanceof UserRole) {
            return $this->role === $role;
        }

        return $this->role->value === $role;
    }

    /**
     * Check if user is a super admin (Shield role check).
     *
     * Super admins have unrestricted access to all features
     * and bypass all permission checks.
     *
     * This uses Spatie's hasRole() method, not the custom enum.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        // This calls Spatie's hasRole() method
        return $this->hasRole('super_admin');
    }

    // ==================== QUERY SCOPES ====================

    /**
     * Scope a query to only include admin users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', UserRole::ADMIN);
    }

    /**
     * Scope a query to only include hall owner users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHallOwners($query)
    {
        return $query->where('role', UserRole::HALL_OWNER);
    }

    /**
     * Scope a query to only include customer users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCustomers($query)
    {
        return $query->where('role', UserRole::CUSTOMER);
    }

    /**
     * Scope a query to only include active users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include verified users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get the full phone number with country code.
     *
     * @return string
     */
    public function getFullPhoneAttribute(): string
    {
        return $this->phone_country_code . $this->phone;
    }

    /**
     * Check if the user has verified their phone number.
     *
     * @return bool
     */
    public function hasVerifiedPhone(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    /**
     * Check if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    // ==================== OWNER SPECIFIC METHODS ====================

    /**
     * Get the total number of halls owned by this user.
     *
     * @return int
     */
    public function getOwnedHallsCount(): int
    {
        return $this->halls()->count();
    }

    /**
     * Get the number of active halls owned by this user.
     *
     * @return int
     */
    public function getActiveHallsCount(): int
    {
        return $this->halls()->where('is_active', true)->count();
    }

    /**
     * Get the total number of bookings for halls owned by this user.
     *
     * @return int
     */
    public function getTotalBookingsAsOwner(): int
    {
        return Booking::whereHas('hall', function ($query) {
            $query->where('owner_id', $this->id);
        })->count();
    }

    /**
     * Get the total revenue from bookings for halls owned by this user.
     *
     * Only counts confirmed/completed bookings that have been paid.
     *
     * @return float
     */
    public function getTotalRevenueAsOwner(): float
    {
        return Booking::whereHas('hall', function ($query) {
            $query->where('owner_id', $this->id);
        })
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->sum('owner_payout');
    }

    // ==================== CUSTOMER SPECIFIC METHODS ====================

    /**
     * Get the total number of bookings made by this user (as customer).
     *
     * @return int
     */
    public function getTotalBookingsAsCustomer(): int
    {
        return $this->bookings()->count();
    }

    /**
     * Get the total amount spent by this user on bookings.
     *
     * Only counts confirmed/completed bookings that have been paid.
     *
     * @return float
     */
    public function getTotalSpent(): float
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->sum('total_amount');
    }

    /**
     * Get all upcoming bookings for this user.
     *
     * Returns bookings that are in the future and not cancelled.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUpcomingBookings()
    {
        return $this->bookings()
            ->where('booking_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('booking_date')
            ->get();
    }

    /**
     * Get all past bookings for this user.
     *
     * Returns bookings that have already occurred.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPastBookings()
    {
        return $this->bookings()
            ->where('booking_date', '<', now()->toDateString())
            ->orderByDesc('booking_date')
            ->get();
    }

    /**
     * Check if the user can review a specific booking.
     *
     * Users can review bookings they made that are eligible for review.
     *
     * @param Booking $booking The booking to check
     * @return bool True if user can review, false otherwise
     */
    public function canReviewBooking(Booking $booking): bool
    {
        return $booking->user_id === $this->id
            && $booking->canBeReviewed();
    }
}
