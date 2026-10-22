<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Regions\Api\Handlers;

use App\Filament\Admin\Resources\RegionResource;
use App\Filament\Admin\Resources\Regions\Api\Requests\CreateRegionRequest;
use App\Filament\Admin\Resources\Regions\Api\Transformers\RegionTransformer;
use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;

/**
 * Create a Region record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = RegionResource::class;

    protected static string $permission = 'Create:Region';

    protected static string $transformer = RegionTransformer::class;

    /**
     * Create Region
     */
    public function handler(CreateRegionRequest $request)
    {
        return $this->store($request);
    }
}
