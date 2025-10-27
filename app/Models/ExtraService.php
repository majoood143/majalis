<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class ExtraService extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'hall_id',
        'name',
        'description',
        'price',
        'unit',
        'minimum_quantity',
        'maximum_quantity',
        'image',
        'is_active',
        'is_required',
        'order',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'price' => 'decimal:2',
        'minimum_quantity' => 'integer',
        'maximum_quantity' => 'integer',
        'is_active' => 'boolean',
        'is_required' => 'boolean',
        'order' => 'integer',
    ];

    public $translatable = ['name', 'description'];

    // Relationships
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_extra_services')
            ->withPivot(['service_name', 'unit_price', 'quantity', 'total_price'])
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeOptional($query)
    {
        return $query->where('is_required', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name->en');
    }

    // Accessors
    // public function getNameAttribute($value)
    // {
    //     $decoded = json_decode($value, true);
    //     $locale = app()->getLocale();
    //     return $decoded[$locale] ?? $decoded['en'] ?? '';
    // }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 3) . ' OMR';
    }

    public function getUnitLabelAttribute(): string
    {
        return match ($this->unit) {
            'per_person' => __('per person'),
            'per_item' => __('per item'),
            'per_hour' => __('per hour'),
            'fixed' => __('fixed price'),
            default => $this->unit,
        };
    }

    // Helper Methods
    public function calculatePrice(int $quantity): float
    {
        // Validate quantity
        if ($quantity < $this->minimum_quantity) {
            $quantity = $this->minimum_quantity;
        }

        if ($this->maximum_quantity && $quantity > $this->maximum_quantity) {
            $quantity = $this->maximum_quantity;
        }

        return $this->price * $quantity;
    }

    public function isValidQuantity(int $quantity): bool
    {
        if ($quantity < $this->minimum_quantity) {
            return false;
        }

        if ($this->maximum_quantity && $quantity > $this->maximum_quantity) {
            return false;
        }

        return true;
    }

    public function getTotalBookings(): int
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->count();
    }

    public function getTotalRevenue(): float
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->sum('booking_extra_services.total_price');
    }
}
