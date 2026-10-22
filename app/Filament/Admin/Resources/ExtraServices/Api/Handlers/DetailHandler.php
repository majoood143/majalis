<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ExtraServices\Api\Handlers;

use App\Filament\Admin\Resources\ExtraServiceResource;
use App\Filament\Admin\Resources\ExtraServices\Api\Transformers\ExtraServiceTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Extra Service record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = ExtraServiceResource::class;

    protected static string $permission = 'View:ExtraService';

    protected static string $transformer = ExtraServiceTransformer::class;

    protected static array $allowedIncludes = ['hall'];
}
