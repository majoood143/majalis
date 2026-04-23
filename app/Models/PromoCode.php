<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromoCode extends Model
{
    use SoftDeletes;

    protected $table = 'promo_codes';

    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'max_uses',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
        'hall_id',
        'created_by_type',
        'created_by_id',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'max_uses'       => 'integer',
        'used_count'     => 'integer',
        'is_active'      => 'boolean',
        'valid_from'     => 'datetime',
        'valid_until'    => 'datetime',
    ];

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(PromoCodeUsage::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Check if the code is currently valid for the given hall.
     *
     * @param int|null $hallId The hall being booked (null skips hall check)
     */
    public function isValid(?int $hallId = null): bool
    {
        if (!$this->is_active) {
            return false;
        }
        if ($this->valid_from && now()->lt($this->valid_from)) {
            return false;
        }
        if ($this->valid_until && now()->gt($this->valid_until)) {
            return false;
        }
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }
        // Hall-specific code: only valid for that hall
        if ($this->hall_id !== null && $hallId !== null && $this->hall_id !== $hallId) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the discount amount on the given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->discount_type === 'percentage') {
            return round(($subtotal * (float) $this->discount_value) / 100, 2);
        }

        // Fixed: cannot exceed the subtotal
        return min((float) $this->discount_value, $subtotal);
    }

    /**
     * Human-readable discount label, e.g. "10%" or "5.000 OMR".
     */
    public function getDiscountLabelAttribute(): string
    {
        if ($this->discount_type === 'percentage') {
            return (float) $this->discount_value . '%';
        }

        return number_format((float) $this->discount_value, 3) . ' ' . __('currency.omr');
    }
}
