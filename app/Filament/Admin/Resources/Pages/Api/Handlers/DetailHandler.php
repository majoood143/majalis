<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Pages\Api\Handlers;

use App\Filament\Admin\Resources\PageResource;
use App\Filament\Admin\Resources\Pages\Api\Transformers\PageTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Page record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = PageResource::class;

    protected static string $permission = 'View:Page';

    protected static string $transformer = PageTransformer::class;
}
