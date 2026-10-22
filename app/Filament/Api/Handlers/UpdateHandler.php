<?php

declare(strict_types=1);

namespace App\Filament\Api\Handlers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\DB;
use Rupadana\ApiService\Http\Handlers;

/**
 * PUT|PATCH /api/{panel}/{slug}/{id}
 *
 * Partial updates: only the fields sent are validated and saved.
 * For file uploads use multipart POST with `_method=PUT`
 * (PHP does not parse multipart bodies on PUT).
 */
abstract class UpdateHandler extends Handler
{
    public static ?string $uri = '/{id}';

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function route(Router $router)
    {
        $router
            ->match(['put', 'patch'], static::$uri, [static::class, 'handler'])
            ->name(static::getKebabClassName())
            ->middleware(static::getRouteMiddleware());
    }

    protected function modify(FormRequest $request): JsonResponse
    {
        $record = static::findRecord($request->route('id'));

        if (! $record) {
            return static::sendNotFoundResponse();
        }

        $data = static::collectData($request);

        DB::transaction(function () use ($record, $data): void {
            $data = $this->beforeUpdate($record, $data);
            $this->updateRecord($record, $data);
            $this->afterUpdate($record, $data);
        });

        return static::transform($record->refresh())
            ->additional(['message' => __('Updated successfully.')])
            ->response();
    }

    protected function beforeUpdate(Model $record, array $data): array
    {
        return $data;
    }

    protected function updateRecord(Model $record, array $data): void
    {
        $record->update($data);
    }

    protected function afterUpdate(Model $record, array $data): void {}
}
