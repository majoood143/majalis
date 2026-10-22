<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallImages\Api\Requests;

use App\Rules\FileOrPath;
use Illuminate\Foundation\Http\FormRequest;

class CreateHallImageRequest extends FormRequest
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
            'hall_id' => ['required', 'integer', 'exists:halls,id'],
            'type' => ['sometimes', 'string', 'max:255', 'in:gallery,featured,floor_plan,360_view'],
            'image_path' => ['required', new FileOrPath],
            'thumbnail_path' => ['nullable', new FileOrPath],
            'title' => ['nullable', 'array'],
            'title.en' => ['nullable', 'string'],
            'title.ar' => ['nullable', 'string'],
            'caption' => ['nullable', 'array'],
            'caption.en' => ['nullable', 'string'],
            'caption.ar' => ['nullable', 'string'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'order' => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
        ];
    }
}
