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

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'phone_country_code',
        'is_active',
        'phone_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
        'is_active' => 'boolean',
    ];

    // Filament User Interface
    public function canAccessPanel(Panel $panel): bool
    {
        // Admin panel
        if ($panel->getId() === 'admin') {
            return $this->isAdmin() && $this->is_active;
        }

        // Hall Owner panel
        if ($panel->getId() === 'owner') {
            return $this->isHallOwner() && $this->is_active && $this->hallOwner?->is_verified;
        }

        return false;
    }

    // Relationships
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function halls(): HasMany
    {
        return $this->hasMany(Hall::class, 'owner_id');
    }

    public function hallOwner(): HasOne
    {
        return $this->hasOne(HallOwner::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function verifiedOwners(): HasMany
    {
        return $this->hasMany(HallOwner::class, 'verified_by');
    }

    // Role Methods
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isHallOwner(): bool
    {
        return $this->role === UserRole::HALL_OWNER;
    }

    public function isCustomer(): bool
    {
        return $this->role === UserRole::CUSTOMER;
    }

    public function hasRole(string|UserRole $role): bool
    {
        if ($role instanceof UserRole) {
            return $this->role === $role;
        }

        return $this->role->value === $role;
    }

    // Scopes
    public function scopeAdmins($query)
    {
        return $query->where('role', UserRole::ADMIN);
    }

    public function scopeHallOwners($query)
    {
        return $query->where('role', UserRole::HALL_OWNER);
    }

    public function scopeCustomers($query)
    {
        return $query->where('role', UserRole::CUSTOMER);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    // Helper Methods
    public function getFullPhoneAttribute(): string
    {
        return $this->phone_country_code . $this->phone;
    }

    public function hasVerifiedPhone(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    // Owner Specific Methods
    public function getOwnedHallsCount(): int
    {
        return $this->halls()->count();
    }

    public function getActiveHallsCount(): int
    {
        return $this->halls()->where('is_active', true)->count();
    }

    public function getTotalBookingsAsOwner(): int
    {
        return Booking::whereHas('hall', function ($query) {
            $query->where('owner_id', $this->id);
        })->count();
    }

    public function getTotalRevenueAsOwner(): float
    {
        return Booking::whereHas('hall', function ($query) {
            $query->where('owner_id', $this->id);
        })
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->sum('owner_payout');
    }

    // Customer Specific Methods
    public function getTotalBookingsAsCustomer(): int
    {
        return $this->bookings()->count();
    }

    public function getTotalSpent(): float
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->sum('total_amount');
    }

    public function getUpcomingBookings()
    {
        return $this->bookings()
            ->where('booking_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('booking_date')
            ->get();
    }

    public function getPastBookings()
    {
        return $this->bookings()
            ->where('booking_date', '<', now()->toDateString())
            ->orderByDesc('booking_date')
            ->get();
    }

    public function canReviewBooking(Booking $booking): bool
    {
        return $booking->user_id === $this->id
            && $booking->canBeReviewed();
    }
}
