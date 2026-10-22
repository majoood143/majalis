<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Features\Api\Handlers;

use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;
use App\Filament\Owner\Resources\FeatureResource;
use App\Filament\Owner\Resources\Features\Api\Transformers\FeatureTransformer;

/**
 * Show one Feature record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = FeatureResource::class;

    protected static string $permission = 'View:HallFeature';

    protected static bool $checkPermission = false;

    protected static string $transformer = FeatureTransformer::class;
}
