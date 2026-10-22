<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Tickets\Api;

use App\Filament\Api\ApiService;
use App\Filament\Owner\Resources\TicketResource;

class TicketApiService extends ApiService
{
    protected static ?string $resource = TicketResource::class;

    public static function handlers(): array
    {
        return [
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class,
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
        ];
    }
}
