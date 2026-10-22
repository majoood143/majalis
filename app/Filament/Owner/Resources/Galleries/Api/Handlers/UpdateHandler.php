<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Galleries\Api\Handlers;

use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use App\Filament\Owner\Resources\Galleries\Api\Requests\UpdateGalleryRequest;
use App\Filament\Owner\Resources\Galleries\Api\Transformers\GalleryTransformer;
use App\Filament\Owner\Resources\GalleryResource;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a Gallery record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = GalleryResource::class;

    protected static string $permission = 'Update:HallImage';

    protected static bool $checkPermission = false;

    protected static string $transformer = GalleryTransformer::class;

    protected static array $fileFields = ['image_path' => 'halls/images'];

    /**
     * Update Gallery
     */
    public function handler(UpdateGalleryRequest $request)
    {
        return $this->modify($request);
    }

    protected function beforeUpdate(Model $record, array $data): array
    {
        return CreateHandler::prepareImage($data, $record);
    }
}
