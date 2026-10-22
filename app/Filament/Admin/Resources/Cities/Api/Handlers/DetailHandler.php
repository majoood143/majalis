<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Cities\Api\Handlers;

use App\Filament\Admin\Resources\Cities\Api\Transformers\CityTransformer;
use App\Filament\Admin\Resources\CityResource;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one City record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = CityResource::class;

    protected static string $permission = 'View:City';

    protected static string $transformer = CityTransformer::class;

    protected static array $allowedIncludes = ['region', 'halls'];
}
