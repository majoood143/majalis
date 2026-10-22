<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallTypes\Api\Handlers;

use App\Filament\Admin\Resources\HallTypeResource;
use App\Filament\Admin\Resources\HallTypes\Api\Requests\UpdateHallTypeRequest;
use App\Filament\Admin\Resources\HallTypes\Api\Transformers\HallTypeTransformer;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a Hall Type record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = HallTypeResource::class;

    protected static string $permission = 'Update:HallType';

    protected static string $transformer = HallTypeTransformer::class;

    /**
     * Update Hall Type
     */
    public function handler(UpdateHallTypeRequest $request)
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
