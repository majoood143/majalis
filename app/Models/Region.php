<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Region extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
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
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function activeCities(): HasMany
    {
        return $this->cities()->where('is_active', true)->orderBy('order');
    }

    public function halls(): HasManyThrough
    {
        return $this->hasManyThrough(Hall::class, City::class);
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

    // Accessors
    public function getNameAttribute($value)
    {
        $decoded = json_decode($value, true);
        $locale = app()->getLocale();
        return $decoded[$locale] ?? $decoded['en'] ?? '';
    }

    // Helper Methods
    public function getTotalHalls(): int
    {
        return $this->halls()->count();
    }

    public function getActiveHalls(): int
    {
        return $this->halls()->where('is_active', true)->count();
    }
}
