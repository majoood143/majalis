<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Galleries\Api\Handlers;

use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;
use App\Filament\Owner\Resources\Galleries\Api\Transformers\GalleryTransformer;
use App\Filament\Owner\Resources\GalleryResource;

/**
 * Show one Gallery record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = GalleryResource::class;

    protected static string $permission = 'View:HallImage';

    protected static bool $checkPermission = false;

    protected static string $transformer = GalleryTransformer::class;

    protected static array $allowedIncludes = ['hall'];
}
