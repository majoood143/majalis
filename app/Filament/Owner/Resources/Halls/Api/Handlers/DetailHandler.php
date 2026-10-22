<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Halls\Api\Handlers;

use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;
use App\Filament\Owner\Resources\HallResource;
use App\Filament\Owner\Resources\Halls\Api\Transformers\HallTransformer;

/**
 * Show one Hall record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = HallResource::class;

    protected static string $permission = 'View:Hall';

    protected static bool $checkPermission = false;

    protected static string $transformer = HallTransformer::class;

    protected static array $allowedIncludes = ['city', 'region', 'images', 'extraServices', 'reviews', 'availabilities', 'hallTypes', 'eventTypes'];
}
