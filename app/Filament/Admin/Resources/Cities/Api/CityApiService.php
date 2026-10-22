<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Cities\Api;

use App\Filament\Admin\Resources\CityResource;
use App\Filament\Api\ApiService;

class CityApiService extends ApiService
{
    protected static ?string $resource = CityResource::class;

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
