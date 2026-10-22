<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallImages\Api\Requests;

use App\Rules\FileOrPath;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHallImageRequest extends FormRequest
{
    /**
     * Access is checked by the route middleware (token, panel access, permission).
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hall_id' => ['sometimes', 'required', 'integer', 'exists:halls,id'],
            'type' => ['sometimes', 'required', 'string', 'max:255', 'in:gallery,featured,floor_plan,360_view'],
            'image_path' => ['sometimes', 'required', new FileOrPath],
            'thumbnail_path' => ['sometimes', 'nullable', new FileOrPath],
            'title' => ['sometimes', 'nullable', 'array'],
            'title.en' => ['nullable', 'string'],
            'title.ar' => ['nullable', 'string'],
            'caption' => ['sometimes', 'nullable', 'array'],
            'caption.en' => ['nullable', 'string'],
            'caption.ar' => ['nullable', 'string'],
            'alt_text' => ['sometimes', 'nullable', 'string', 'max:255'],
            'order' => ['sometimes', 'required', 'integer'],
            'is_active' => ['sometimes', 'required', 'boolean'],
            'is_featured' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
