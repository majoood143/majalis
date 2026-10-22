<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Notifications\Api\Handlers;

use App\Filament\Admin\Resources\NotificationResource;
use App\Filament\Admin\Resources\Notifications\Api\Transformers\NotificationTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Notification record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = NotificationResource::class;

    protected static string $permission = 'View:DatabaseNotification';

    protected static string $transformer = NotificationTransformer::class;
}
