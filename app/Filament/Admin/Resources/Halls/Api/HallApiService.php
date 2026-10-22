<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Halls\Api;

use App\Filament\Admin\Resources\HallResource;
use App\Filament\Api\ApiService;

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
