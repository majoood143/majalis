<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallOwners\Api\Requests;

use App\Rules\FileOrPath;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateHallOwnerRequest extends FormRequest
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
            'user_id' => ['required', 'integer', 'exists:users,id', Rule::unique('hall_owners', 'user_id')->withoutTrashed()],
            'business_name' => ['required', 'string', 'max:255'],
            'business_name_ar' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', new FileOrPath],
            'commercial_registration' => ['required', 'string', 'max:255', Rule::unique('hall_owners', 'commercial_registration')],
            'tax_number' => ['nullable', 'string', 'max:255'],
            'business_phone' => ['required', 'string', 'max:20'],
            'business_email' => ['nullable', 'string', 'max:255', 'email'],
            'business_address' => ['required', 'string'],
            'business_address_ar' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account_name' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:255'],
            'iban' => ['nullable', 'string', 'max:255'],
            'commercial_registration_document' => ['nullable', new FileOrPath(image: false)],
            'tax_certificate' => ['nullable', new FileOrPath(image: false)],
            'identity_document' => ['nullable', new FileOrPath(image: false)],
            'is_verified' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'verification_notes' => ['nullable', 'string'],
            'commission_type' => ['nullable', 'string', 'max:255', 'in:percentage,fixed'],
            'commission_value' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
