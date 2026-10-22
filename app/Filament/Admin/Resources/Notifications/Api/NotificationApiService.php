<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Notifications\Api;

use App\Filament\Admin\Resources\NotificationResource;
use App\Filament\Api\ApiService;

class NotificationApiService extends ApiService
{
    protected static ?string $resource = NotificationResource::class;

    public static function handlers(): array
    {
        return [
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class,
            Handlers\DeleteHandler::class,
        ];
    }
}
