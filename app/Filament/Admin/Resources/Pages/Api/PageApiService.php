<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Pages\Api;

use App\Filament\Admin\Resources\PageResource;
use App\Filament\Api\ApiService;

class PageApiService extends ApiService
{
    protected static ?string $resource = PageResource::class;

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
