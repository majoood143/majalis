<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ExtraServices\Api\Transformers;

use App\Filament\Api\Transformers\Transformer;
use App\Models\ExtraService;

/**
 * @property ExtraService $resource
 */
class ExtraServiceTransformer extends Transformer
{
    protected array $files = ['image'];
}
