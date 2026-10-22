<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallFeatures\Api\Handlers;

use App\Filament\Admin\Resources\HallFeatureResource;
use App\Filament\Admin\Resources\HallFeatures\Api\Transformers\HallFeatureTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Hall Feature record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = HallFeatureResource::class;

    protected static string $permission = 'View:HallFeature';

    protected static string $transformer = HallFeatureTransformer::class;
}
