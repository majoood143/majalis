<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallOwners\Api\Transformers;

use App\Filament\Api\Transformers\Transformer;
use App\Models\HallOwner;

/**
 * @property HallOwner $resource
 */
class HallOwnerTransformer extends Transformer
{
    protected array $files = ['logo', 'commercial_registration_document', 'tax_certificate', 'identity_document'];
}
