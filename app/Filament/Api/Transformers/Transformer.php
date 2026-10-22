<?php

declare(strict_types=1);

namespace App\Filament\Api\Transformers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Default JSON shape for API records.
 *
 * - Translatable attributes: `name` is the value in the request locale
 *   (?lang= / Accept-Language), `name_translations` holds {"ar": …, "en": …}.
 * - File attributes listed in $files get an absolute `{attribute}_url`.
 * - Relations loaded through ?include= are serialized the same way (snake_case keys).
 * - The model's own $hidden attributes are never returned.
 *
 * @property Model $resource
 */
class Transformer extends JsonResource
{
    /** Attributes holding public-disk paths (string or array of strings). */
    protected array $files = [];

    /** Attributes to leave out of the response, on top of the model's $hidden. */
    protected array $except = [];

    public function toArray($request): array
    {
        return static::serialize($this->resource, $this->files, $this->except);
    }

    public static function serialize(Model $model, array $files = [], array $except = []): array
    {
        $data = $model->attributesToArray();

        if (method_exists($model, 'getTranslatableAttributes')) {
            foreach ($model->getTranslatableAttributes() as $attribute) {
                if (! array_key_exists($attribute, $data)) {
                    continue;
                }

                $data[$attribute] = $model->getTranslation($attribute, app()->getLocale());
                $data[$attribute . '_translations'] = $model->getTranslations($attribute);
            }
        }

        foreach ($files as $attribute) {
            if (array_key_exists($attribute, $data)) {
                $data[$attribute . '_url'] = static::url($data[$attribute]);
            }
        }

        foreach ($model->getRelations() as $name => $related) {
            $data[Str::snake($name)] = match (true) {
                $related instanceof Model => static::serialize($related),
                $related instanceof Collection => $related->map(fn (Model $item) => static::serialize($item))->values()->all(),
                default => null,
            };
        }

        return Arr::except($data, $except);
    }

    protected static function url(mixed $path): mixed
    {
        if (is_array($path)) {
            return array_map(fn ($item) => static::url($item), $path);
        }

        if (blank($path) || ! is_string($path)) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : Storage::disk('public')->url($path);
    }
}
