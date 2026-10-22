<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallFeatures\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHallFeatureRequest extends FormRequest
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
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('hall_features', 'slug')->ignore($this->route('id'))],
            'icon' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'order' => ['sometimes', 'required', 'integer'],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
