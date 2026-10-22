<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Pages\Api\Handlers;

use App\Filament\Admin\Resources\PageResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Page record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = PageResource::class;

    protected static string $permission = 'Delete:Page';
}
