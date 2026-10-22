<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\EventTypes\Api\Handlers;

use App\Filament\Admin\Resources\EventTypeResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Event Type record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = EventTypeResource::class;

    protected static string $permission = 'Delete:EventType';
}
