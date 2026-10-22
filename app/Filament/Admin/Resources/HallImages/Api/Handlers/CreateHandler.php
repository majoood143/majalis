<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallImages\Api\Handlers;

use App\Filament\Admin\Resources\HallImageResource;
use App\Filament\Admin\Resources\HallImages\Api\Requests\CreateHallImageRequest;
use App\Filament\Admin\Resources\HallImages\Api\Transformers\HallImageTransformer;
use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * Create a Hall Image record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = HallImageResource::class;

    protected static string $permission = 'Create:HallImage';

    protected static string $transformer = HallImageTransformer::class;

    protected static array $fileFields = ['image_path' => 'halls/images', 'thumbnail_path' => 'halls/thumbnails'];

    /**
     * Create Hall Image
     */
    public function handler(CreateHallImageRequest $request)
    {
        return $this->store($request);
    }

    protected function beforeCreate(array $data): array
    {
        return static::prepareImage($data);
    }

    public static function prepareImage(array $data, ?Model $record = null): array
    {
        $type = $data['type'] ?? $record?->type;

        if ($type === 'featured') {
            $data['is_featured'] = true;
        }

        if (empty($data['alt_text']) && ! empty($data['title']['en']) && empty($record?->alt_text)) {
            $data['alt_text'] = $data['title']['en'];
        }

        if (! empty($data['image_path']) && $data['image_path'] !== $record?->image_path) {
            $data = array_merge($data, static::imageMetadata($data['image_path']));
        }

        return $data;
    }
}
