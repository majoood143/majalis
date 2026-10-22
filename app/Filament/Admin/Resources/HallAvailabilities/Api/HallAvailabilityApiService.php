<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallAvailabilities\Api;

use App\Filament\Admin\Resources\HallAvailabilityResource;
use App\Filament\Api\ApiService;

class HallAvailabilityApiService extends ApiService
{
    protected static ?string $resource = HallAvailabilityResource::class;

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
