<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallCategories\Api;

use App\Filament\Admin\Resources\HallCategoryResource;
use App\Filament\Api\ApiService;

class HallCategoryApiService extends ApiService
{
    protected static ?string $resource = HallCategoryResource::class;

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
