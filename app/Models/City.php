<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class City extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'region_id',
        'name',
        'code',
        'description',
        'latitude',
        'longitude',
        'is_active',
        'order',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public $translatable = ['name', 'description'];

    // Relationships
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function halls(): HasMany
    {
        return $this->hasMany(Hall::class);
    }

    public function activeHalls(): HasMany
    {
        return $this->halls()->where('is_active', true);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name->en');
    }

    public function scopeInRegion($query, $regionId)
    {
        return $query->where('region_id', $regionId);
    }

    // Accessors
    // public function getNameAttribute($value)
    // {
    //     $decoded = json_decode($value, true);
    //     $locale = app()->getLocale();
    //     return $decoded[$locale] ?? $decoded['en'] ?? '';
    // }

    public function getTranslatedNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->name[$locale] ?? $this->name['en'] ?? '';
    }

    public function getFullNameAttribute(): string
    {
        return $this->name . ', ' . $this->region->name;
    }

    // Helper Methods
    public function getTotalHalls(): int
    {
        return $this->halls()->count();
    }

    public function getAvailableHalls(string $date, string $timeSlot): int
    {
        return $this->halls()
            ->where('is_active', true)
            ->whereDoesntHave('bookings', function ($query) use ($date, $timeSlot) {
                $query->where('booking_date', $date)
                    ->where('time_slot', $timeSlot)
                    ->whereIn('status', ['pending', 'confirmed']);
            })
            ->count();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
