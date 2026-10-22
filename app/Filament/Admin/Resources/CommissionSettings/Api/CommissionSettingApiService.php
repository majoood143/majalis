<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CommissionSettings\Api;

use App\Filament\Admin\Resources\CommissionSettingResource;
use App\Filament\Api\ApiService;

class CommissionSettingApiService extends ApiService
{
    protected static ?string $resource = CommissionSettingResource::class;

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
