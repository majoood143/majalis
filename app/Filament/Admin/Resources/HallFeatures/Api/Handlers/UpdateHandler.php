<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallFeatures\Api\Handlers;

use App\Filament\Admin\Resources\HallFeatureResource;
use App\Filament\Admin\Resources\HallFeatures\Api\Requests\UpdateHallFeatureRequest;
use App\Filament\Admin\Resources\HallFeatures\Api\Transformers\HallFeatureTransformer;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a Hall Feature record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = HallFeatureResource::class;

    protected static string $permission = 'Update:HallFeature';

    protected static string $transformer = HallFeatureTransformer::class;

    /**
     * Update Hall Feature
     */
    public function handler(UpdateHallFeatureRequest $request)
    {
        return $this->modify($request);
    }

    protected function beforeUpdate(Model $record, array $data): array
    {
        if (array_key_exists('slug', $data) && empty($data['slug'])) {
            $name = $data['name'] ?? $record->getTranslations('name');
            $data['slug'] = static::uniqueSlug($name['en'] ?? $name['ar'] ?? '', $record->getKey());
        }

        return $data;
    }
}
