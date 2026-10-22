<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallOwners\Api;

use App\Filament\Admin\Resources\HallOwnerResource;
use App\Filament\Api\ApiService;

class HallOwnerApiService extends ApiService
{
    protected static ?string $resource = HallOwnerResource::class;

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
