<?php

declare(strict_types=1);

namespace App\Filament\Api\Handlers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Rupadana\ApiService\Http\Handlers;

/**
 * POST /api/{panel}/{slug}
 *
 * Subclasses declare `handler(CreateXRequest $request)` (so Scramble documents
 * the body) and call store(). Logic from the Filament Create page
 * (mutateFormDataBeforeCreate / handleRecordCreation) lives in beforeCreate(),
 * createRecord() and afterCreate().
 */
abstract class CreateHandler extends Handler
{
    public static ?string $uri = '/';

    public static function getMethod()
    {
        return Handlers::POST;
    }

    protected function store(FormRequest $request): JsonResponse
    {
        $data = static::collectData($request);

        $record = DB::transaction(function () use ($data) {
            $data = $this->beforeCreate($data);
            $record = $this->createRecord($data);
            $this->afterCreate($record, $data);

            return $record;
        });

        return static::transform($record->refresh())
            ->additional(['message' => __('Created successfully.')])
            ->response()
            ->setStatusCode(201);
    }

    protected function beforeCreate(array $data): array
    {
        return $data;
    }

    protected function createRecord(array $data): Model
    {
        return static::getModel()::create($data);
    }

    protected function afterCreate(Model $record, array $data): void {}
}
