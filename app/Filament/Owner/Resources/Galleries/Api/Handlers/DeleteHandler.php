<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Galleries\Api\Handlers;

use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;
use App\Filament\Owner\Resources\GalleryResource;

/**
 * Delete a Gallery record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = GalleryResource::class;

    protected static string $permission = 'Delete:HallImage';

    protected static bool $checkPermission = false;
}
