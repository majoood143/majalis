<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'max:255', 'email', Rule::unique('users', 'email')->ignore($this->route('id'))],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'role' => ['sometimes', 'required', 'string', 'max:255', 'in:admin,hall_owner,customer'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'phone_country_code' => ['sometimes', 'required', 'string', 'max:5'],
            'language_preference' => ['sometimes', 'required', 'string', 'max:5', 'in:ar,en'],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
