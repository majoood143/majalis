<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Pages\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePageRequest extends FormRequest
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
            'slug' => ['required', 'string', 'max:255', Rule::unique('pages', 'slug')],
            'title_en' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'content_en' => ['required', 'string'],
            'content_ar' => ['required', 'string'],
            'meta_title_en' => ['nullable', 'string', 'max:255'],
            'meta_title_ar' => ['nullable', 'string', 'max:255'],
            'meta_description_en' => ['nullable', 'string'],
            'meta_description_ar' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'order' => ['sometimes', 'integer'],
            'show_in_footer' => ['sometimes', 'boolean'],
            'show_in_header' => ['sometimes', 'boolean'],
        ];
    }
}
