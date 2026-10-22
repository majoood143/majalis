<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Payouts\Api;

use App\Filament\Api\ApiService;
use App\Filament\Owner\Resources\PayoutResource;

class PayoutApiService extends ApiService
{
    protected static ?string $resource = PayoutResource::class;

    public static function handlers(): array
    {
        return [
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class,
            Handlers\CreateHandler::class,
        ];
    }
}
