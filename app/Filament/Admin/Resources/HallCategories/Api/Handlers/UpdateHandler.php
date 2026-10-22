<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallCategories\Api\Handlers;

use App\Filament\Admin\Resources\HallCategories\Api\Requests\UpdateHallCategoryRequest;
use App\Filament\Admin\Resources\HallCategories\Api\Transformers\HallCategoryTransformer;
use App\Filament\Admin\Resources\HallCategoryResource;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a Hall Category record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = HallCategoryResource::class;

    protected static string $permission = 'Update:HallCategory';

    protected static string $transformer = HallCategoryTransformer::class;

    /**
     * Update Hall Category
     */
    public function handler(UpdateHallCategoryRequest $request)
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
