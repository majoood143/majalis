<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallOwners\Api\Handlers;

use App\Filament\Admin\Resources\HallOwnerResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Hall Owner record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = HallOwnerResource::class;

    protected static string $permission = 'Delete:HallOwner';
}
