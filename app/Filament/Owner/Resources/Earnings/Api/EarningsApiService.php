<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Earnings\Api;

use App\Filament\Api\ApiService;
use App\Filament\Owner\Resources\EarningsResource;

class EarningsApiService extends ApiService
{
    protected static ?string $resource = EarningsResource::class;

    public static function handlers(): array
    {
        return [
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class,
        ];
    }
}
