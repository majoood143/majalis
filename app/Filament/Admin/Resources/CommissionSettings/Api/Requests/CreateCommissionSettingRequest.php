<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CommissionSettings\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCommissionSettingRequest extends FormRequest
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
            'hall_id' => ['nullable', 'integer', 'exists:halls,id'],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'name' => ['nullable', 'array'],
            'name.en' => ['nullable', 'string'],
            'name.ar' => ['nullable', 'string'],
            'commission_type' => ['sometimes', 'string', 'max:255', 'in:percentage,fixed'],
            'commission_value' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'effective_from' => ['nullable', 'date'],
            'effective_to' => ['nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
