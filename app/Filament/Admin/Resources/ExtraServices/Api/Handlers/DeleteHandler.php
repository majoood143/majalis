<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ExtraServices\Api\Handlers;

use App\Filament\Admin\Resources\ExtraServiceResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Extra Service record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = ExtraServiceResource::class;

    protected static string $permission = 'Delete:ExtraService';
}
