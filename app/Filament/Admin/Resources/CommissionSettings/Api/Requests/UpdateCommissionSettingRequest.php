<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CommissionSettings\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommissionSettingRequest extends FormRequest
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
            'hall_id' => ['sometimes', 'nullable', 'integer', 'exists:halls,id'],
            'owner_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'name' => ['sometimes', 'nullable', 'array'],
            'name.en' => ['nullable', 'string'],
            'name.ar' => ['nullable', 'string'],
            'commission_type' => ['sometimes', 'required', 'string', 'max:255', 'in:percentage,fixed'],
            'commission_value' => ['sometimes', 'required', 'numeric', 'min:0'],
            'description' => ['sometimes', 'nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'effective_from' => ['sometimes', 'nullable', 'date'],
            'effective_to' => ['sometimes', 'nullable', 'date'],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
