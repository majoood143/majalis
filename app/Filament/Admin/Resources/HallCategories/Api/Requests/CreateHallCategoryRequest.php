<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallCategories\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateHallCategoryRequest extends FormRequest
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
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string'],
            'name.ar' => ['required', 'string'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('hall_categories', 'slug')],
            'sort_order' => ['sometimes', 'integer'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
        ];
    }
}
