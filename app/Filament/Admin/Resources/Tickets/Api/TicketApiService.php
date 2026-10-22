<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Tickets\Api;

use App\Filament\Admin\Resources\TicketResource;
use App\Filament\Api\ApiService;

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
            Handlers\DeleteHandler::class,
        ];
    }
}
