<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallImages\Api;

use App\Filament\Admin\Resources\HallImageResource;
use App\Filament\Api\ApiService;

class HallImageApiService extends ApiService
{
    protected static ?string $resource = HallImageResource::class;

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
