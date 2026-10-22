<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Notifications\Api\Handlers;

use App\Filament\Admin\Resources\NotificationResource;
use App\Filament\Admin\Resources\Notifications\Api\Transformers\NotificationTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Notification records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = NotificationResource::class;

    protected static string $permission = 'ViewAny:DatabaseNotification';

    protected static string $transformer = NotificationTransformer::class;

    protected static array $allowedFilters = ['type', 'notifiable_type', 'notifiable_id'];

    protected static array $allowedSorts = ['created_at', 'read_at'];

    protected static array $dateFilters = ['created_at'];

    protected static string $defaultSort = '-created_at';
}
