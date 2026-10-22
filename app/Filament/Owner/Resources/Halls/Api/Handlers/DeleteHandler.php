<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Halls\Api\Handlers;

use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;
use App\Filament\Owner\Resources\HallResource;

/**
 * Delete a Hall record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = HallResource::class;

    protected static string $permission = 'Delete:Hall';

    protected static bool $checkPermission = false;
}
