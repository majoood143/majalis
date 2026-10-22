<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceFeeSettings\Api\Handlers;

use App\Filament\Admin\Resources\ServiceFeeSettingResource;
use App\Filament\Admin\Resources\ServiceFeeSettings\Api\Requests\CreateServiceFeeSettingRequest;
use App\Filament\Admin\Resources\ServiceFeeSettings\Api\Transformers\ServiceFeeSettingTransformer;
use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Create a Service Fee Setting record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = ServiceFeeSettingResource::class;

    protected static string $permission = 'Create:ServiceFeeSetting';

    protected static string $transformer = ServiceFeeSettingTransformer::class;

    /**
     * Create Service Fee Setting
     */
    public function handler(CreateServiceFeeSettingRequest $request)
    {
        return $this->store($request);
    }

    protected function beforeCreate(array $data): array
    {
        return static::normalizeScope($data, null);
    }

    public static function normalizeScope(array $data, ?Model $record): array
    {
        // Both hall and owner selected: the hall-specific setting wins (same as the panel)
        if (! empty($data['hall_id']) && ! empty($data['owner_id'])) {
            unset($data['owner_id']);
        }

        $type = $data['fee_type'] ?? $record?->fee_type;
        $type = $type instanceof \BackedEnum ? $type->value : $type;
        $value = $data['fee_value'] ?? $record?->fee_value;

        if ($type === 'percentage' && $value > 100) {
            static::fail('fee_value', __('Percentage cannot exceed 100%.'));
        }

        $from = $data['effective_from'] ?? $record?->effective_from;
        $to = $data['effective_to'] ?? $record?->effective_to;

        if ($from && $to && Carbon::parse($from)->gt(Carbon::parse($to))) {
            static::fail('effective_to', __('Effective from date must be before effective to date.'));
        }

        return $data;
    }

    protected function afterCreate(Model $record, array $data): void
    {
        Cache::forget('service_fees');
    }
}
