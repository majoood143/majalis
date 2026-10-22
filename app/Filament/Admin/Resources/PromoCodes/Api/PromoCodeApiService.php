<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PromoCodes\Api;

use App\Filament\Admin\Resources\PromoCodeResource;
use App\Filament\Api\ApiService;

class PromoCodeApiService extends ApiService
{
    protected static ?string $resource = PromoCodeResource::class;

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
