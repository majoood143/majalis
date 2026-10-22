<?php

declare(strict_types=1);

namespace App\Filament\Api\Handlers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;

/**
 * DELETE /api/{panel}/{slug}/{id}
 *
 * Soft-deletes when the model uses SoftDeletes.
 */
abstract class DeleteHandler extends Handler
{
    public static ?string $uri = '/{id}';

    public static function getMethod()
    {
        return Handlers::DELETE;
    }

    public function handler(Request $request)
    {
        $record = static::findRecord($request->route('id'));

        if (! $record) {
            return static::sendNotFoundResponse();
        }

        $this->beforeDelete($record);
        $record->delete();

        return response()->json(['message' => __('Deleted successfully.')]);
    }

    protected function beforeDelete(Model $record): void {}
}
