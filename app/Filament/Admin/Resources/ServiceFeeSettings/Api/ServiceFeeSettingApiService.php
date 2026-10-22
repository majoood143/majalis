<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceFeeSettings\Api;

use App\Filament\Admin\Resources\ServiceFeeSettingResource;
use App\Filament\Api\ApiService;

class ServiceFeeSettingApiService extends ApiService
{
    protected static ?string $resource = ServiceFeeSettingResource::class;

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
