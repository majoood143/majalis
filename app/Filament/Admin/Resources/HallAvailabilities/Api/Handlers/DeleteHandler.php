<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallAvailabilities\Api\Handlers;

use App\Filament\Admin\Resources\HallAvailabilityResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Hall Availability record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = HallAvailabilityResource::class;

    protected static string $permission = 'Delete:HallAvailability';
}
