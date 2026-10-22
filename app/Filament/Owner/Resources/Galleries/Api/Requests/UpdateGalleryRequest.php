<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Galleries\Api\Requests;

use App\Rules\FileOrPath;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGalleryRequest extends FormRequest
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
            'hall_id' => ['sometimes', 'required', 'integer', Rule::exists('halls', 'id')->where('owner_id', $this->user()->id)->whereNull('deleted_at')],
            'image_path' => ['sometimes', 'required', new FileOrPath],
            'type' => ['sometimes', 'required', 'string', 'max:255', 'in:gallery,featured,floor_plan,360_view'],
            'is_featured' => ['sometimes', 'required', 'boolean'],
            'is_active' => ['sometimes', 'required', 'boolean'],
            'order' => ['sometimes', 'required', 'integer'],
            'title' => ['sometimes', 'nullable', 'array'],
            'title.en' => ['nullable', 'string'],
            'title.ar' => ['nullable', 'string'],
            'caption' => ['sometimes', 'nullable', 'array'],
            'caption.en' => ['nullable', 'string'],
            'caption.ar' => ['nullable', 'string'],
            'alt_text' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
