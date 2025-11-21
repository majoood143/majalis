<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Booking Model
 *
 * @property int $id
 * @property string $booking_number
 * @property int $hall_id
 * @property int $user_id
 * @property string $booking_date
 * @property string $time_slot
 * @property int $number_of_guests
 * @property string $customer_name
 * @property string $customer_email
 * @property string $customer_phone
 * @property string|null $customer_notes
 * @property string|null $event_type
 * @property array|null $event_details
 * @property float $hall_price
 * @property float $services_price
 * @property float $subtotal
 * @property float $platform_fee
 * @property float $total_amount
 * @property float $commission_amount
 * @property string|null $commission_type
 * @property float|null $commission_value
 * @property float $owner_payout
 * @property string $status
 * @property string $payment_status
 */
class Booking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_number',
        'hall_id',
        'user_id',
        'booking_date',
        'time_slot',
        'number_of_guests',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_notes',
        'event_type',
        'event_details',
        'hall_price',
        'services_price',
        'subtotal',
        'platform_fee',
        'total_amount',
        'commission_amount',
        'commission_type',
        'commission_value',
        'owner_payout',
        'status',
        'payment_status',
        'cancelled_at',
        'cancellation_reason',
        'refund_amount',
        'confirmed_at',
        'completed_at',
        'invoice_path',
        'admin_notes',
        'payment_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'booking_date' => 'date',
        'hall_price' => 'decimal:3',
        'services_price' => 'decimal:3',
        'subtotal' => 'decimal:3',
        'platform_fee' => 'decimal:3',
        'total_amount' => 'decimal:3',
        'commission_amount' => 'decimal:3',
        'commission_value' => 'decimal:3',
        'owner_payout' => 'decimal:3',
        'refund_amount' => 'decimal:3',
        'event_details' => 'array',
        'cancelled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the hall that owns the booking.
     */
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    /**
     * Get the user that owns the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The extra services that belong to the booking.
     */
    public function extraServices(): BelongsToMany
    {
        return $this->belongsToMany(ExtraService::class, 'booking_extra_services')
            ->withPivot('service_name', 'unit_price', 'quantity', 'total_price')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include bookings of a given status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include pending bookings.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Check if booking is cancellable
     */
    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Check if booking is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Get the payments for the booking.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the latest payment for the booking.
     */
    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    /**
     * Confirm the booking
     *
     * Changes status from pending to confirmed and sets confirmed_at timestamp.
     *
     * @return bool
     */
    public function confirm(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        return $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }
}
