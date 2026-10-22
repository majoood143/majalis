<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Bookings\Api;

use App\Filament\Admin\Resources\BookingResource;
use App\Filament\Api\ApiService;

class BookingApiService extends ApiService
{
    protected static ?string $resource = BookingResource::class;

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
