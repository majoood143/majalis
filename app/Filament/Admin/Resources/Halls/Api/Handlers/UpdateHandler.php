<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Halls\Api\Handlers;

use App\Filament\Admin\Resources\HallResource;
use App\Filament\Admin\Resources\Halls\Api\Requests\UpdateHallRequest;
use App\Filament\Admin\Resources\Halls\Api\Transformers\HallTransformer;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a Hall record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = HallResource::class;

    protected static string $permission = 'Update:Hall';

    protected static string $transformer = HallTransformer::class;

    protected static array $fileFields = ['featured_image' => 'halls', 'gallery' => 'halls/gallery'];

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

    protected function afterUpdate(Model $record, array $data): void
    {
        activity()
            ->performedOn($record)
            ->causedBy(auth()->user())
            ->withProperties(['source' => 'api', 'changes' => $data])
            ->log(class_basename($record) . ' Updated via API');
    }
}
