<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Halls\Api\Handlers;

use App\Filament\Admin\Resources\HallResource;
use App\Filament\Admin\Resources\Halls\Api\Transformers\HallTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Hall record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = HallResource::class;

    protected static string $permission = 'View:Hall';

    protected static string $transformer = HallTransformer::class;

    protected static array $allowedIncludes = ['city', 'region', 'owner', 'images', 'extraServices', 'reviews', 'hallTypes', 'eventTypes', 'availabilities'];
}
