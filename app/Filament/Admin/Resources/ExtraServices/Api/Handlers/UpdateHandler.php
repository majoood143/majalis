<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ExtraServices\Api\Handlers;

use App\Filament\Admin\Resources\ExtraServiceResource;
use App\Filament\Admin\Resources\ExtraServices\Api\Requests\UpdateExtraServiceRequest;
use App\Filament\Admin\Resources\ExtraServices\Api\Transformers\ExtraServiceTransformer;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a Extra Service record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = ExtraServiceResource::class;

    protected static string $permission = 'Update:ExtraService';

    protected static string $transformer = ExtraServiceTransformer::class;

    protected static array $fileFields = ['image' => 'services'];

    /**
     * Update Extra Service
     */
    public function handler(UpdateExtraServiceRequest $request)
    {
        return $this->modify($request);
    }

    protected function beforeUpdate(Model $record, array $data): array
    {
        return CreateHandler::validateService($data, $record);
    }
}
