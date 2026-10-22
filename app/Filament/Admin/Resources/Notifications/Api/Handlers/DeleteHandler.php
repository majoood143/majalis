<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Notifications\Api\Handlers;

use App\Filament\Admin\Resources\NotificationResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Notification record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = NotificationResource::class;

    protected static string $permission = 'Delete:DatabaseNotification';
}
