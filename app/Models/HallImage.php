<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations;

class HallImage extends Model
{
    use HasFactory, HasTranslations,HasRoles;

    protected $fillable = [
        'hall_id',
        'image_path',
        'thumbnail_path',
        'title',
        'caption',
        'alt_text',
        'type',
        'file_size',
        'mime_type',
        'width',
        'height',
        'is_active',
        'is_featured',
        'order',
    ];

    protected $casts = [
        'title' => 'array',
        'caption' => 'array',
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'order' => 'integer',
    ];

    public $translatable = ['title', 'caption'];

    // Relationships
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeGallery($query)
    {
        return $query->where('type', 'gallery');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at');
    }

    // Accessors
    public function getUrlAttribute(): string
    {
        return Storage::url($this->image_path);
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail_path) {
            return Storage::url($this->thumbnail_path);
        }

        return $this->url;
    }

    public function getFormattedSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    public function getDimensionsAttribute(): string
    {
        if ($this->width && $this->height) {
            return "{$this->width} × {$this->height}";
        }

        return 'Unknown';
    }

    public function getTitleAttribute($value)
    {
        if (empty($value)) {
            return '';
        }

        $decoded = json_decode($value, true);
        $locale = app()->getLocale();
        return $decoded[$locale] ?? $decoded['en'] ?? '';
    }

    // Mutators
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            // Delete physical files
            if (Storage::exists($image->image_path)) {
                Storage::delete($image->image_path);
            }

            if ($image->thumbnail_path && Storage::exists($image->thumbnail_path)) {
                Storage::delete($image->thumbnail_path);
            }
        });
    }

    // Helper Methods
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'gallery' => __('Gallery Image'),
            'featured' => __('Featured Image'),
            'floor_plan' => __('Floor Plan'),
            '360_view' => __('360° View'),
            default => ucfirst($this->type),
        };
    }
}
