<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallTypes\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHallTypeRequest extends FormRequest
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
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('hall_types', 'slug')->ignore($this->route('id'))],
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:hall_categories,id'],
            'icon' => ['sometimes', 'nullable', 'string', 'max:255'],
            'color' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'sort_order' => ['sometimes', 'required', 'integer'],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
