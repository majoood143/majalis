<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Regions\Api\Handlers;

use App\Filament\Admin\Resources\RegionResource;
use App\Filament\Admin\Resources\Regions\Api\Transformers\RegionTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Region record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = RegionResource::class;

    protected static string $permission = 'View:Region';

    protected static string $transformer = RegionTransformer::class;

    protected static array $allowedIncludes = ['cities', 'halls'];
}
