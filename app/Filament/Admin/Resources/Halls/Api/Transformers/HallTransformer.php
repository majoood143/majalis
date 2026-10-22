<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Halls\Api\Transformers;

use App\Filament\Api\Transformers\Transformer;
use App\Models\Hall;

/**
 * @property Hall $resource
 */
class HallTransformer extends Transformer
{
    protected array $files = ['featured_image', 'gallery'];
}
