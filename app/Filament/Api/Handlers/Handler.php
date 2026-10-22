<?php

declare(strict_types=1);

namespace App\Filament\Api\Handlers;

use App\Filament\Api\Transformers\Transformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Rupadana\ApiService\Http\Handlers;

/**
 * Shared behaviour for all resource API handlers.
 *
 * - Records are always read through the Filament resource's getEloquentQuery(),
 *   so the API sees exactly what the panel sees (e.g. owners only get their own halls).
 * - Admin handlers check the Shield permission (`permission:Create:Hall`).
 *   Owner handlers set $checkPermission = false: the Owner panel authorizes by
 *   panel access + query scoping, not by Shield permissions.
 */
abstract class Handler extends Handlers
{
    protected static string $permission = '';

    protected static bool $checkPermission = true;

    /** @var class-string<Transformer> */
    protected static string $transformer = Transformer::class;

    /** Relations allowed in `?include=` (camelCase relation method names). */
    protected static array $allowedIncludes = [];

    /** Upload fields: attribute => directory on the public disk. */
    protected static array $fileFields = [];

    public static function getRouteMiddleware(): array
    {
        if (static::isPublic()) {
            return [];
        }

        return static::$checkPermission
            ? ['auth:sanctum', 'permission:' . static::$permission]
            : ['auth:sanctum'];
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }

    protected static function getEloquentQuery(): Builder
    {
        $query = static::$resource::getEloquentQuery();

        return static::scopeQuery(static::applyTrashedFilter($query));
    }

    /**
     * Extra scoping for resources that only scope inside table() in Filament.
     */
    protected static function scopeQuery(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Soft-deleted rows are hidden unless `?filter[trashed]=with|only` is sent.
     */
    protected static function applyTrashedFilter(Builder $query): Builder
    {
        $model = $query->getModel();

        if (! in_array(SoftDeletes::class, class_uses_recursive($model), true)) {
            return $query;
        }

        $query->withoutGlobalScope(SoftDeletingScope::class);
        $column = $model->getQualifiedDeletedAtColumn();

        return match (request()->input('filter.trashed')) {
            'with' => $query,
            'only' => $query->whereNotNull($column),
            default => $query->whereNull($column),
        };
    }

    protected static function findRecord(mixed $id): ?Model
    {
        return static::getEloquentQuery()->whereKey($id)->first();
    }

    protected static function transform(Model $record): Transformer
    {
        return new (static::$transformer)($record);
    }

    /**
     * Validated input with uploaded files stored on the public disk.
     */
    protected static function collectData(FormRequest $request): array
    {
        $data = $request->validated();

        foreach (static::$fileFields as $field => $directory) {
            if (array_key_exists($field, $data)) {
                $data[$field] = static::storeUploads($data[$field], $directory);
            }
        }

        return $data;
    }

    protected static function storeUploads(mixed $value, string $directory): mixed
    {
        if ($value instanceof UploadedFile) {
            return $value->store($directory, 'public');
        }

        if (is_array($value)) {
            return array_values(array_map(fn ($item) => static::storeUploads($item, $directory), $value));
        }

        return $value;
    }

    /**
     * Slug from text, suffixed (-1, -2…) until unique for the model (trashed rows included).
     */
    protected static function uniqueSlug(string $text, ?int $ignoreId = null, string $column = 'slug'): string
    {
        $base = Str::slug($text) ?: Str::lower(Str::random(8));
        $slug = $base;
        $counter = 1;

        $query = fn () => static::getModel()::query()
            ->when(
                in_array(SoftDeletes::class, class_uses_recursive(static::getModel()), true),
                fn (Builder $query) => $query->withTrashed(),
            )
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId));

        while ($query()->where($column, $slug)->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * file_size / mime_type / width / height for an image on the public disk.
     */
    protected static function imageMetadata(?string $path): array
    {
        $disk = Storage::disk('public');

        if (blank($path) || ! $disk->exists($path)) {
            return [];
        }

        $size = @getimagesize($disk->path($path));

        return array_filter([
            'file_size' => $disk->size($path),
            'mime_type' => $disk->mimeType($path),
            'width' => $size[0] ?? null,
            'height' => $size[1] ?? null,
        ], fn ($value) => $value !== null);
    }

    /**
     * Abort with a 422 validation error (API equivalent of Filament's halt()).
     */
    protected static function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
