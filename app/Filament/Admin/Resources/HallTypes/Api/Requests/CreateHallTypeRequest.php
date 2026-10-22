<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallTypes\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateHallTypeRequest extends FormRequest
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
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('hall_types', 'slug')],
            'category_id' => ['nullable', 'integer', 'exists:hall_categories,id'],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'sort_order' => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
