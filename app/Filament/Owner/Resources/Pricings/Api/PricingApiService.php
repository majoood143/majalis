<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Pricings\Api;

use App\Filament\Api\ApiService;
use App\Filament\Owner\Resources\PricingResource;

class PricingApiService extends ApiService
{
    protected static ?string $resource = PricingResource::class;

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
