<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Pages\Api\Handlers;

use App\Filament\Admin\Resources\PageResource;
use App\Filament\Admin\Resources\Pages\Api\Requests\CreatePageRequest;
use App\Filament\Admin\Resources\Pages\Api\Transformers\PageTransformer;
use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;

/**
 * Create a Page record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = PageResource::class;

    protected static string $permission = 'Create:Page';

    protected static string $transformer = PageTransformer::class;

    /**
     * Create Page
     */
    public function handler(CreatePageRequest $request)
    {
        return $this->store($request);
    }
}
