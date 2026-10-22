<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Cities\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCityRequest extends FormRequest
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
            'region_id' => ['sometimes', 'required', 'integer', 'exists:regions,id'],
            'name' => ['sometimes', 'required', 'array'],
            'name.en' => ['nullable', 'string'],
            'name.ar' => ['nullable', 'string'],
            'code' => ['sometimes', 'nullable', 'string', 'max:10', Rule::unique('cities', 'code')->ignore($this->route('id'))],
            'description' => ['sometimes', 'nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'order' => ['sometimes', 'required', 'integer'],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
