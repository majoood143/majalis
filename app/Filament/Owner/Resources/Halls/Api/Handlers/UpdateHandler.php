<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Halls\Api\Handlers;

use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use App\Filament\Owner\Resources\HallResource;
use App\Filament\Owner\Resources\Halls\Api\Requests\UpdateHallRequest;
use App\Filament\Owner\Resources\Halls\Api\Transformers\HallTransformer;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a Hall record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = HallResource::class;

    protected static string $permission = 'Update:Hall';

    protected static bool $checkPermission = false;

    protected static string $transformer = HallTransformer::class;

    protected static array $fileFields = ['featured_image' => 'halls/featured', 'gallery' => 'halls/gallery'];

    /**
     * Update Hall
     */
    public function handler(UpdateHallRequest $request)
    {
        return $this->modify($request);
    }

    protected function beforeUpdate(Model $record, array $data): array
    {
        if (array_key_exists('slug', $data) && empty($data['slug'])) {
            $name = $data['name'] ?? $record->getTranslations('name');
            $data['slug'] = static::uniqueSlug($name['en'] ?? $name['ar'] ?? 'hall', $record->getKey());
        }

        return CreateHandler::prepareHall($data, $record);
    }
}
