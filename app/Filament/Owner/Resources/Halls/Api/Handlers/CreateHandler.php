<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Halls\Api\Handlers;

use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;
use App\Filament\Owner\Resources\HallResource;
use App\Filament\Owner\Resources\Halls\Api\Requests\CreateHallRequest;
use App\Filament\Owner\Resources\Halls\Api\Transformers\HallTransformer;
use Illuminate\Database\Eloquent\Model;

/**
 * Create a Hall record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = HallResource::class;

    protected static string $permission = 'Create:Hall';

    protected static bool $checkPermission = false;

    protected static string $transformer = HallTransformer::class;

    protected static array $fileFields = ['featured_image' => 'halls/featured', 'gallery' => 'halls/gallery'];

    /**
     * Create Hall
     */
    public function handler(CreateHallRequest $request)
    {
        return $this->store($request);
    }

    protected function beforeCreate(array $data): array
    {
        // Owner and featured flag are never taken from the request (same as the panel)
        $data['owner_id'] = auth()->id();
        $data['is_featured'] = false;
        $data['features'] ??= [];

        if (empty($data['slug'])) {
            $data['slug'] = static::uniqueSlug($data['name']['en'] ?? $data['name']['ar'] ?? 'hall');
        }

        return static::prepareHall($data);
    }

    public static function prepareHall(array $data, ?Model $record = null): array
    {
        $min = $data['capacity_min'] ?? $record?->capacity_min;
        $max = $data['capacity_max'] ?? $record?->capacity_max;

        if ($min !== null && $max !== null && $min > $max) {
            static::fail('capacity_min', __('Minimum capacity cannot exceed maximum capacity.'));
        }

        foreach (['phone', 'whatsapp'] as $field) {
            if (! empty($data[$field])) {
                $data[$field] = preg_replace('/[^0-9+]/', '', $data[$field]);
            }
        }

        if (isset($data['features']) && is_array($data['features'])) {
            $data['features'] = array_values(array_filter($data['features']));
        }

        // Plain `address` column mirrors address_localized (the model hook only sees the current-locale string)
        if (empty($data['address']) && ! empty($data['address_localized'])) {
            $data['address'] = $data['address_localized']['en'] ?? $data['address_localized']['ar'] ?? '';
        }

        return $data;
    }
}
