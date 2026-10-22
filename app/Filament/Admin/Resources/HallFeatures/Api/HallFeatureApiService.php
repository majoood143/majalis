<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallFeatures\Api;

use App\Filament\Admin\Resources\HallFeatureResource;
use App\Filament\Api\ApiService;

class HallFeatureApiService extends ApiService
{
    protected static ?string $resource = HallFeatureResource::class;

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
