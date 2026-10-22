<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallFeatures\Api\Handlers;

use App\Filament\Admin\Resources\HallFeatureResource;
use App\Filament\Admin\Resources\HallFeatures\Api\Requests\CreateHallFeatureRequest;
use App\Filament\Admin\Resources\HallFeatures\Api\Transformers\HallFeatureTransformer;
use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;

/**
 * Create a Hall Feature record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = HallFeatureResource::class;

    protected static string $permission = 'Create:HallFeature';

    protected static string $transformer = HallFeatureTransformer::class;

    /**
     * Create Hall Feature
     */
    public function handler(CreateHallFeatureRequest $request)
    {
        return $this->store($request);
    }

    protected function beforeCreate(array $data): array
    {
        if (empty($data['slug'])) {
            $data['slug'] = static::uniqueSlug($data['name']['en'] ?? $data['name']['ar'] ?? '');
        }

        return $data;
    }
}
