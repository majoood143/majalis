<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallCategories\Api\Handlers;

use App\Filament\Admin\Resources\HallCategories\Api\Requests\CreateHallCategoryRequest;
use App\Filament\Admin\Resources\HallCategories\Api\Transformers\HallCategoryTransformer;
use App\Filament\Admin\Resources\HallCategoryResource;
use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;

/**
 * Create a Hall Category record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = HallCategoryResource::class;

    protected static string $permission = 'Create:HallCategory';

    protected static string $transformer = HallCategoryTransformer::class;

    /**
     * Create Hall Category
     */
    public function handler(CreateHallCategoryRequest $request)
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
