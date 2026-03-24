<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class HallCategory extends Model
{
    use HasTranslations;

    public $translatable = ['name', 'description'];

    protected $fillable = ['slug', 'name', 'description', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function hallTypes(): HasMany
    {
        return $this->hasMany(HallType::class, 'category_id');
    }
}
