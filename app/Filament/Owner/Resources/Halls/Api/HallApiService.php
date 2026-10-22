<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Halls\Api;

use App\Filament\Api\ApiService;
use App\Filament\Owner\Resources\HallResource;

class HallApiService extends ApiService
{
    protected static ?string $resource = HallResource::class;

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
