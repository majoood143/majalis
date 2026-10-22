<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CommissionSettings\Api\Handlers;

use App\Filament\Admin\Resources\CommissionSettingResource;
use App\Filament\Admin\Resources\CommissionSettings\Api\Requests\UpdateCommissionSettingRequest;
use App\Filament\Admin\Resources\CommissionSettings\Api\Transformers\CommissionSettingTransformer;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a Commission Setting record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = CommissionSettingResource::class;

    protected static string $permission = 'Update:CommissionSetting';

    protected static string $transformer = CommissionSettingTransformer::class;

    /**
     * Update Commission Setting
     */
    public function handler(UpdateCommissionSettingRequest $request)
    {
        return $this->modify($request);
    }

    protected function beforeUpdate(Model $record, array $data): array
    {
        return CreateHandler::normalizeScope($data, $record);
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
