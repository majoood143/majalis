<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Features\Api;

use App\Filament\Api\ApiService;
use App\Filament\Owner\Resources\FeatureResource;

class FeatureApiService extends ApiService
{
    protected static ?string $resource = FeatureResource::class;

    public static function handlers(): array
    {
        return [
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class,
        ];
    }
}
