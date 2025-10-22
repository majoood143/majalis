<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HallAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'hall_id',
        'date',
        'time_slot',
        'is_available',
        'reason',
        'notes',
        'custom_price',
    ];

    protected $casts = [
        'date' => 'date',
        'is_available' => 'boolean',
        'custom_price' => 'decimal:2',
    ];

    // Relationships
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    // Scopes
    public function scopeUnavailable($query)
    {
        return $query->where('is_available', false);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForSlot($query, string $slot)
    {
        return $query->where('time_slot', $slot);
    }

    public function scopeWithCustomPrice($query)
    {
        return $query->whereNotNull('custom_price');
    }

    public function scopeFuture($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    // Helper Methods
    public function getReasonLabelAttribute(): string
    {
        return match ($this->reason) {
            'maintenance' => __('Under Maintenance'),
            'blocked' => __('Blocked by Owner'),
            'custom' => __('Custom Block'),
            'holiday' => __('Holiday'),
            default => $this->reason ?? __('Unavailable'),
        };
    }

    public function hasCustomPrice(): bool
    {
        return $this->custom_price !== null;
    }

    public function getEffectivePrice(): float
    {
        return $this->custom_price ?? $this->hall->getPriceForSlot($this->time_slot);
    }
}
