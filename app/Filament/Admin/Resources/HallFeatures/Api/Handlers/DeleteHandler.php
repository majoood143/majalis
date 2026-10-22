<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallFeatures\Api\Handlers;

use App\Filament\Admin\Resources\HallFeatureResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Hall Feature record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = HallFeatureResource::class;

    protected static string $permission = 'Delete:HallFeature';
}
