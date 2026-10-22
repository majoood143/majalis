<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallOwners\Api\Requests;

use App\Rules\FileOrPath;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHallOwnerRequest extends FormRequest
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
            'user_id' => ['sometimes', 'required', 'integer', 'exists:users,id', Rule::unique('hall_owners', 'user_id')->withoutTrashed()->ignore($this->route('id'))],
            'business_name' => ['sometimes', 'required', 'string', 'max:255'],
            'business_name_ar' => ['sometimes', 'nullable', 'string', 'max:255'],
            'logo' => ['sometimes', 'nullable', new FileOrPath],
            'commercial_registration' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('hall_owners', 'commercial_registration')->ignore($this->route('id'))],
            'tax_number' => ['sometimes', 'nullable', 'string', 'max:255'],
            'business_phone' => ['sometimes', 'required', 'string', 'max:20'],
            'business_email' => ['sometimes', 'nullable', 'string', 'max:255', 'email'],
            'business_address' => ['sometimes', 'required', 'string'],
            'business_address_ar' => ['sometimes', 'nullable', 'string'],
            'bank_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'bank_account_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'bank_account_number' => ['sometimes', 'nullable', 'string', 'max:255'],
            'iban' => ['sometimes', 'nullable', 'string', 'max:255'],
            'commercial_registration_document' => ['sometimes', 'nullable', new FileOrPath(image: false)],
            'tax_certificate' => ['sometimes', 'nullable', new FileOrPath(image: false)],
            'identity_document' => ['sometimes', 'nullable', new FileOrPath(image: false)],
            'is_verified' => ['sometimes', 'required', 'boolean'],
            'is_active' => ['sometimes', 'required', 'boolean'],
            'verification_notes' => ['sometimes', 'nullable', 'string'],
            'commission_type' => ['sometimes', 'nullable', 'string', 'max:255', 'in:percentage,fixed'],
            'commission_value' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ];
    }
}
