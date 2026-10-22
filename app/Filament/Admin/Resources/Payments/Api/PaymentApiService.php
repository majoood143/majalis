<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Payments\Api;

use App\Filament\Admin\Resources\PaymentResource;
use App\Filament\Api\ApiService;

class PaymentApiService extends ApiService
{
    protected static ?string $resource = PaymentResource::class;

    public static function handlers(): array
    {
        return [
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
        ];
    }
}
