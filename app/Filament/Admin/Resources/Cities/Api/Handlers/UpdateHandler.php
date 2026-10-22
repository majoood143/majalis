<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Cities\Api\Handlers;

use App\Filament\Admin\Resources\Cities\Api\Requests\UpdateCityRequest;
use App\Filament\Admin\Resources\Cities\Api\Transformers\CityTransformer;
use App\Filament\Admin\Resources\CityResource;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;

/**
 * Update a City record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = CityResource::class;

    protected static string $permission = 'Update:City';

    protected static string $transformer = CityTransformer::class;

    /**
     * Update City
     */
    public function handler(UpdateCityRequest $request)
    {
        return $this->modify($request);
    }
}
