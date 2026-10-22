<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\GuestSessions\Api;

use App\Filament\Admin\Resources\GuestSessionResource;
use App\Filament\Api\ApiService;

class GuestSessionApiService extends ApiService
{
    protected static ?string $resource = GuestSessionResource::class;

    public static function handlers(): array
    {
        return [
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class,
            Handlers\DeleteHandler::class,
        ];
    }
}
