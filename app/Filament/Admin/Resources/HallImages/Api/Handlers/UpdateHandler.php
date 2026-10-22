<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallImages\Api\Handlers;

use App\Filament\Admin\Resources\HallImageResource;
use App\Filament\Admin\Resources\HallImages\Api\Requests\UpdateHallImageRequest;
use App\Filament\Admin\Resources\HallImages\Api\Transformers\HallImageTransformer;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Update a Hall Image record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = HallImageResource::class;

    protected static string $permission = 'Update:HallImage';

    protected static string $transformer = HallImageTransformer::class;

    protected static array $fileFields = ['image_path' => 'halls/images', 'thumbnail_path' => 'halls/thumbnails'];

    /**
     * Update Hall Image
     */
    public function handler(UpdateHallImageRequest $request)
    {
        return $this->modify($request);
    }

    protected function beforeUpdate(Model $record, array $data): array
    {
        // Replacing the image removes the old file and its thumbnail (same as the panel)
        if (! empty($data['image_path']) && $data['image_path'] !== $record->image_path) {
            Storage::disk('public')->delete(array_filter([$record->image_path, $record->thumbnail_path]));

            if (empty($data['thumbnail_path'])) {
                $data['thumbnail_path'] = null;
            }
        }

        return CreateHandler::prepareImage($data, $record);
    }
}
