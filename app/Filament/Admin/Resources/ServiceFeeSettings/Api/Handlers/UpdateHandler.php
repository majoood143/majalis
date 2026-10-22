<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceFeeSettings\Api\Handlers;

use App\Filament\Admin\Resources\ServiceFeeSettingResource;
use App\Filament\Admin\Resources\ServiceFeeSettings\Api\Requests\UpdateServiceFeeSettingRequest;
use App\Filament\Admin\Resources\ServiceFeeSettings\Api\Transformers\ServiceFeeSettingTransformer;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Update a Service Fee Setting record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = ServiceFeeSettingResource::class;

    protected static string $permission = 'Update:ServiceFeeSetting';

    protected static string $transformer = ServiceFeeSettingTransformer::class;

    /**
     * Update Service Fee Setting
     */
    public function handler(UpdateServiceFeeSettingRequest $request)
    {
        return $this->modify($request);
    }

    protected function beforeUpdate(Model $record, array $data): array
    {
        return CreateHandler::normalizeScope($data, $record);
    }

    protected function afterUpdate(Model $record, array $data): void
    {
        Cache::forget('service_fees');
    }
}
