<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Regions\Api\Handlers;

use App\Filament\Admin\Resources\RegionResource;
use App\Filament\Admin\Resources\Regions\Api\Requests\UpdateRegionRequest;
use App\Filament\Admin\Resources\Regions\Api\Transformers\RegionTransformer;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;

/**
 * Update a Region record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = RegionResource::class;

    protected static string $permission = 'Update:Region';

    protected static string $transformer = RegionTransformer::class;

    /**
     * Update Region
     */
    public function handler(UpdateRegionRequest $request)
    {
        return $this->modify($request);
    }
}
