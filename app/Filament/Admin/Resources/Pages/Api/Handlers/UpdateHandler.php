<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Pages\Api\Handlers;

use App\Filament\Admin\Resources\PageResource;
use App\Filament\Admin\Resources\Pages\Api\Requests\UpdatePageRequest;
use App\Filament\Admin\Resources\Pages\Api\Transformers\PageTransformer;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;

/**
 * Update a Page record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = PageResource::class;

    protected static string $permission = 'Update:Page';

    protected static string $transformer = PageTransformer::class;

    /**
     * Update Page
     */
    public function handler(UpdatePageRequest $request)
    {
        return $this->modify($request);
    }
}
