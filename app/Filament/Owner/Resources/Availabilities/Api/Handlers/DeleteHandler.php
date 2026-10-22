<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Availabilities\Api\Handlers;

use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;
use App\Filament\Owner\Resources\AvailabilityResource;

/**
 * Delete a Availability record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = AvailabilityResource::class;

    protected static string $permission = 'Delete:HallAvailability';

    protected static bool $checkPermission = false;
}
