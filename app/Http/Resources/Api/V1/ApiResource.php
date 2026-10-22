<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Base class for the customer mobile API (/api/v1).
 *
 * Unlike the panel API Transformer, v1 resources list their fields explicitly:
 * this is a public contract for the mobile app, so nothing internal (commission,
 * owner payout, admin notes…) may leak through a new column.
 * Translatable attributes are already resolved to the request locale by
 * spatie/laravel-translatable (see SetApiLocale).
 */
abstract class ApiResource extends JsonResource
{
    /** Absolute URL for a path on the public disk (or an already absolute URL). */
    protected static function fileUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : Storage::disk('public')->url($path);
    }

    protected static function money(mixed $amount): ?float
    {
        return $amount === null ? null : round((float) $amount, 3);
    }
}
