<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class HallType extends Model
{
    use HasTranslations;

    public $translatable = ['name', 'description'];

    protected $fillable = ['slug', 'name', 'description', 'icon', 'color', 'sort_order', 'is_active', 'category_id'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function halls(): BelongsToMany
    {
        return $this->belongsToMany(Hall::class, 'hall_hall_type')->withTimestamps();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(HallCategory::class);
    }
}
