<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Pages\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePageRequest extends FormRequest
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
            'slug' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('pages', 'slug')->ignore($this->route('id'))],
            'title_en' => ['sometimes', 'required', 'string', 'max:255'],
            'title_ar' => ['sometimes', 'required', 'string', 'max:255'],
            'content_en' => ['sometimes', 'required', 'string'],
            'content_ar' => ['sometimes', 'required', 'string'],
            'meta_title_en' => ['sometimes', 'nullable', 'string', 'max:255'],
            'meta_title_ar' => ['sometimes', 'nullable', 'string', 'max:255'],
            'meta_description_en' => ['sometimes', 'nullable', 'string'],
            'meta_description_ar' => ['sometimes', 'nullable', 'string'],
            'is_active' => ['sometimes', 'required', 'boolean'],
            'order' => ['sometimes', 'required', 'integer'],
            'show_in_footer' => ['sometimes', 'required', 'boolean'],
            'show_in_header' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
