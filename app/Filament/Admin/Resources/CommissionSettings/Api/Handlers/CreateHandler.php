<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CommissionSettings\Api\Handlers;

use App\Filament\Admin\Resources\CommissionSettingResource;
use App\Filament\Admin\Resources\CommissionSettings\Api\Requests\CreateCommissionSettingRequest;
use App\Filament\Admin\Resources\CommissionSettings\Api\Transformers\CommissionSettingTransformer;
use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Create a Commission Setting record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = CommissionSettingResource::class;

    protected static string $permission = 'Create:CommissionSetting';

    protected static string $transformer = CommissionSettingTransformer::class;

    /**
     * Create Commission Setting
     */
    public function handler(CreateCommissionSettingRequest $request)
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

        $type = $data['commission_type'] ?? $record?->commission_type;
        $type = $type instanceof \BackedEnum ? $type->value : $type;
        $value = $data['commission_value'] ?? $record?->commission_value;

        if ($type === 'percentage' && $value > 100) {
            static::fail('commission_value', __('Percentage cannot exceed 100%.'));
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
        activity()
            ->performedOn($record)
            ->causedBy(auth()->user())
            ->withProperties(['source' => 'api', 'changes' => $data])
            ->log(class_basename($record) . ' Created via API');
    }
}
