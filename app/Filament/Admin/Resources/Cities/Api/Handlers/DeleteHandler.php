<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Cities\Api\Handlers;

use App\Filament\Admin\Resources\CityResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a City record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = CityResource::class;

    protected static string $permission = 'Delete:City';
}
