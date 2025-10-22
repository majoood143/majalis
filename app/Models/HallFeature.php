<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class HallFeature extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'is_active',
        'order',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public $translatable = ['name', 'description'];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name->en');
    }

    // Mutators
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($feature) {
            if (empty($feature->slug)) {
                $name = is_array($feature->name) ? $feature->name : json_decode($feature->name, true);
                $feature->slug = Str::slug($name['en'] ?? $name['ar'] ?? 'feature');
            }
        });
    }

    // Accessors
    public function getNameAttribute($value)
    {
        $decoded = json_decode($value, true);
        $locale = app()->getLocale();
        return $decoded[$locale] ?? $decoded['en'] ?? '';
    }

    // Helper Methods
    public function getHallsCount(): int
    {
        return Hall::whereJsonContains('features', $this->id)
            ->where('is_active', true)
            ->count();
    }
}
