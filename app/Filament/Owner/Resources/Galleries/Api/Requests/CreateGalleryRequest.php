<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Galleries\Api\Requests;

use App\Rules\FileOrPath;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateGalleryRequest extends FormRequest
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
            'hall_id' => ['required', 'integer', Rule::exists('halls', 'id')->where('owner_id', $this->user()->id)->whereNull('deleted_at')],
            'image_path' => ['required', new FileOrPath],
            'type' => ['sometimes', 'string', 'max:255', 'in:gallery,featured,floor_plan,360_view'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'order' => ['sometimes', 'integer'],
            'title' => ['nullable', 'array'],
            'title.en' => ['nullable', 'string'],
            'title.ar' => ['nullable', 'string'],
            'caption' => ['nullable', 'array'],
            'caption.en' => ['nullable', 'string'],
            'caption.ar' => ['nullable', 'string'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ];
    }
}
