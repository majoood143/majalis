<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallAvailabilities\Api\Handlers;

use App\Filament\Admin\Resources\HallAvailabilities\Api\Transformers\HallAvailabilityTransformer;
use App\Filament\Admin\Resources\HallAvailabilityResource;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Hall Availability record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = HallAvailabilityResource::class;

    protected static string $permission = 'View:HallAvailability';

    protected static string $transformer = HallAvailabilityTransformer::class;

    protected static array $allowedIncludes = ['hall'];
}
