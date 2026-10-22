<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Accepts either an uploaded file or an existing storage path (string).
 *
 * Lets API clients upload a new image (multipart) or keep the current one
 * by sending its path back unchanged.
 */
class FileOrPath implements ValidationRule
{
    public function __construct(
        protected bool $image = true,
        protected int $maxKilobytes = 10240,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value)) {
            if (mb_strlen($value) > 255) {
                $fail(__('validation.max.string', ['attribute' => $attribute, 'max' => 255]));
            }

            return;
        }

        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            $fail(__('validation.uploaded', ['attribute' => $attribute]));

            return;
        }

        if ($this->image && ! str_starts_with((string) $value->getMimeType(), 'image/')) {
            $fail(__('validation.image', ['attribute' => $attribute]));
        }

        if ($value->getSize() > $this->maxKilobytes * 1024) {
            $fail(__('validation.max.file', ['attribute' => $attribute, 'max' => $this->maxKilobytes]));
        }
    }
}
