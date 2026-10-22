<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Regions\Api\Handlers;

use App\Filament\Admin\Resources\RegionResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Region record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = RegionResource::class;

    protected static string $permission = 'Delete:Region';
}
