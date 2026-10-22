<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['sometimes', 'string', 'max:255', 'in:admin,hall_owner,customer'],
            'phone' => ['nullable', 'string', 'max:20'],
            'phone_country_code' => ['sometimes', 'string', 'max:5'],
            'language_preference' => ['sometimes', 'string', 'max:5', 'in:ar,en'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
