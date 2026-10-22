<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Payouts\Api;

use App\Filament\Admin\Resources\PayoutResource;
use App\Filament\Api\ApiService;

class PayoutApiService extends ApiService
{
    protected static ?string $resource = PayoutResource::class;

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
