<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallImages\Api\Transformers;

use App\Filament\Api\Transformers\Transformer;
use App\Models\HallImage;

/**
 * @property HallImage $resource
 */
class HallImageTransformer extends Transformer
{
    protected array $files = ['image_path', 'thumbnail_path'];
}
