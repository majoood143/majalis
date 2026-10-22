<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Halls\Api\Handlers;

use App\Filament\Admin\Resources\HallResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Hall record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = HallResource::class;

    protected static string $permission = 'Delete:Hall';
}
