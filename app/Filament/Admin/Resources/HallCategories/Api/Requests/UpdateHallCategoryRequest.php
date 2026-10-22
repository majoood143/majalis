<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallCategories\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHallCategoryRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'array'],
            'name.en' => ['nullable', 'string'],
            'name.ar' => ['nullable', 'string'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('hall_categories', 'slug')->ignore($this->route('id'))],
            'sort_order' => ['sometimes', 'required', 'integer'],
            'description' => ['sometimes', 'nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
        ];
    }
}
