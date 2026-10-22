<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Bookings\Api;

use App\Filament\Api\ApiService;
use App\Filament\Owner\Resources\BookingResource;

class BookingApiService extends ApiService
{
    protected static ?string $resource = BookingResource::class;

    public static function handlers(): array
    {
        return [
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class,
        ];
    }
}
