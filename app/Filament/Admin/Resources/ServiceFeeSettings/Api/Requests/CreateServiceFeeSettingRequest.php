<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceFeeSettings\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateServiceFeeSettingRequest extends FormRequest
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
            'fee_type' => ['sometimes', 'string', 'max:255', 'in:percentage,fixed'],
            'fee_value' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'effective_from' => ['nullable', 'date'],
            'effective_to' => ['nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
