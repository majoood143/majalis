<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallTypes\Api\Handlers;

use App\Filament\Admin\Resources\HallTypeResource;
use App\Filament\Admin\Resources\HallTypes\Api\Transformers\HallTypeTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Hall Type record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = HallTypeResource::class;

    protected static string $permission = 'View:HallType';

    protected static string $transformer = HallTypeTransformer::class;

    protected static array $allowedIncludes = ['category', 'halls'];
}
