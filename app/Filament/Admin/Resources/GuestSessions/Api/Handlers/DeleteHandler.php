<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\GuestSessions\Api\Handlers;

use App\Filament\Admin\Resources\GuestSessionResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Guest Session record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = GuestSessionResource::class;

    protected static string $permission = 'Delete:GuestSession';
}
