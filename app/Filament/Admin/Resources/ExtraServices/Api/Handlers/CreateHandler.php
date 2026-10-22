<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ExtraServices\Api\Handlers;

use App\Filament\Admin\Resources\ExtraServiceResource;
use App\Filament\Admin\Resources\ExtraServices\Api\Requests\CreateExtraServiceRequest;
use App\Filament\Admin\Resources\ExtraServices\Api\Transformers\ExtraServiceTransformer;
use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * Create a Extra Service record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = ExtraServiceResource::class;

    protected static string $permission = 'Create:ExtraService';

    protected static string $transformer = ExtraServiceTransformer::class;

    protected static array $fileFields = ['image' => 'services'];

    /**
     * Create Extra Service
     */
    public function handler(CreateExtraServiceRequest $request)
    {
        return $this->store($request);
    }

    protected function beforeCreate(array $data): array
    {
        return static::validateService($data);
    }

    public static function validateService(array $data, ?Model $record = null): array
    {
        $min = $data['minimum_quantity'] ?? $record?->minimum_quantity ?? 1;
        $max = array_key_exists('maximum_quantity', $data) ? $data['maximum_quantity'] : $record?->maximum_quantity;

        if ($max !== null && $max < $min) {
            static::fail('maximum_quantity', __('extra-service.notifications.invalid_quantity_range_body'));
        }

        // A required service is always active (same as the panel)
        if (($data['is_required'] ?? $record?->is_required) && ! ($data['is_active'] ?? $record?->is_active ?? true)) {
            $data['is_active'] = true;
        }

        return $data;
    }

    protected function afterCreate(Model $record, array $data): void
    {
        activity()
            ->performedOn($record)
            ->causedBy(auth()->user())
            ->withProperties(['source' => 'api', 'changes' => $data])
            ->log(class_basename($record) . ' Created via API');
    }
}
