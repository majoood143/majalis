<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Reviews\Api;

use App\Filament\Admin\Resources\ReviewResource;
use App\Filament\Api\ApiService;

class ReviewApiService extends ApiService
{
    protected static ?string $resource = ReviewResource::class;

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
