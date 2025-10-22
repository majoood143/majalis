<?php

namespace App\Models;

use App\Enums\CommissionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class CommissionSetting extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'hall_id',
        'owner_id',
        'commission_type',
        'commission_value',
        'name',
        'description',
        'is_active',
        'effective_from',
        'effective_to',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'commission_type' => CommissionType::class,
        'commission_value' => 'decimal:2',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public $translatable = ['name', 'description'];

    // Relationships
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeGlobal($query)
    {
        return $query->whereNull('hall_id')->whereNull('owner_id');
    }

    public function scopeForHall($query, int $hallId)
    {
        return $query->where('hall_id', $hallId);
    }

    public function scopeForOwner($query, int $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeEffectiveOn($query, string $date)
    {
        return $query->where(function ($q) use ($date) {
            $q->whereNull('effective_from')
                ->orWhere('effective_from', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('effective_to')
                ->orWhere('effective_to', '>=', $date);
        });
    }

    public function scopeCurrent($query)
    {
        return $query->effectiveOn(now()->toDateString());
    }

    // Helper Methods
    public function isGlobal(): bool
    {
        return is_null($this->hall_id) && is_null($this->owner_id);
    }

    public function isHallSpecific(): bool
    {
        return !is_null($this->hall_id);
    }

    public function isOwnerSpecific(): bool
    {
        return !is_null($this->owner_id) && is_null($this->hall_id);
    }

    public function isEffectiveOn(string $date): bool
    {
        $effectiveFrom = $this->effective_from ? $this->effective_from->format('Y-m-d') : null;
        $effectiveTo = $this->effective_to ? $this->effective_to->format('Y-m-d') : null;

        if ($effectiveFrom && $date < $effectiveFrom) {
            return false;
        }

        if ($effectiveTo && $date > $effectiveTo) {
            return false;
        }

        return true;
    }

    public function calculateCommission(float $amount): float
    {
        if ($this->commission_type === CommissionType::PERCENTAGE) {
            return ($amount * $this->commission_value) / 100;
        }

        return $this->commission_value;
    }

    public function getFormattedValueAttribute(): string
    {
        if ($this->commission_type === CommissionType::PERCENTAGE) {
            return $this->commission_value . '%';
        }

        return number_format($this->commission_value, 3) . ' OMR';
    }

    public function getScopeNameAttribute(): string
    {
        if ($this->isHallSpecific()) {
            return __('Hall Specific') . ': ' . $this->hall->name;
        }

        if ($this->isOwnerSpecific()) {
            return __('Owner Specific') . ': ' . $this->owner->name;
        }

        return __('Global');
    }
}
