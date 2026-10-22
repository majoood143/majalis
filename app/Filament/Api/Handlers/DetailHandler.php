<?php

declare(strict_types=1);

namespace App\Filament\Api\Handlers;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * GET /api/{panel}/{slug}/{id}?include=...
 */
abstract class DetailHandler extends Handler
{
    public static ?string $uri = '/{id}';

    public function handler(Request $request)
    {
        $record = QueryBuilder::for(static::getEloquentQuery(), $request)
            ->allowedIncludes(static::$allowedIncludes)
            ->whereKey($request->route('id'))
            ->first();

        if (! $record) {
            return static::sendNotFoundResponse();
        }

        return static::transform($record);
    }
}
