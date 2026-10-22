<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Availabilities\Api\Handlers;

use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;
use App\Filament\Owner\Resources\Availabilities\Api\Transformers\AvailabilityTransformer;
use App\Filament\Owner\Resources\AvailabilityResource;

/**
 * Show one Availability record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = AvailabilityResource::class;

    protected static string $permission = 'View:HallAvailability';

    protected static bool $checkPermission = false;

    protected static string $transformer = AvailabilityTransformer::class;

    protected static array $allowedIncludes = ['hall'];
}
