<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\EventTypes\Api;

use App\Filament\Admin\Resources\EventTypeResource;
use App\Filament\Api\ApiService;

class EventTypeApiService extends ApiService
{
    protected static ?string $resource = EventTypeResource::class;

    public static function handlers(): array
    {
        return [
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class,
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
        ];
    }
}
