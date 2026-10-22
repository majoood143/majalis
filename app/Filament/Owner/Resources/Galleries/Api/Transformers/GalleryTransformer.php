<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Galleries\Api\Transformers;

use App\Filament\Api\Transformers\Transformer;
use App\Models\HallImage;

/**
 * @property HallImage $resource
 */
class GalleryTransformer extends Transformer
{
    protected array $files = ['image_path'];
}
