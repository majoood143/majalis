<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Galleries\Api\Handlers;

use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;
use App\Filament\Owner\Resources\Galleries\Api\Requests\CreateGalleryRequest;
use App\Filament\Owner\Resources\Galleries\Api\Transformers\GalleryTransformer;
use App\Filament\Owner\Resources\GalleryResource;
use Illuminate\Database\Eloquent\Model;

/**
 * Create a Gallery record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = GalleryResource::class;

    protected static string $permission = 'Create:HallImage';

    protected static bool $checkPermission = false;

    protected static string $transformer = GalleryTransformer::class;

    protected static array $fileFields = ['image_path' => 'halls/images'];

    /**
     * Create Gallery
     */
    public function handler(CreateGalleryRequest $request)
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

        if (! empty($data['image_path']) && $data['image_path'] !== $record?->image_path) {
            $data = array_merge($data, static::imageMetadata($data['image_path']));
        }

        return $data;
    }
}
