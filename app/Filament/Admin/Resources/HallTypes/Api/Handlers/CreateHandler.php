<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallTypes\Api\Handlers;

use App\Filament\Admin\Resources\HallTypeResource;
use App\Filament\Admin\Resources\HallTypes\Api\Requests\CreateHallTypeRequest;
use App\Filament\Admin\Resources\HallTypes\Api\Transformers\HallTypeTransformer;
use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;

/**
 * Create a Hall Type record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = HallTypeResource::class;

    protected static string $permission = 'Create:HallType';

    protected static string $transformer = HallTypeTransformer::class;

    /**
     * Create Hall Type
     */
    public function handler(CreateHallTypeRequest $request)
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
