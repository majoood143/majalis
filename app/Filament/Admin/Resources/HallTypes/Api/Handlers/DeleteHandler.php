<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallTypes\Api\Handlers;

use App\Filament\Admin\Resources\HallTypeResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Hall Type record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = HallTypeResource::class;

    protected static string $permission = 'Delete:HallType';
}
