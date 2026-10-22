<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\PromoCodes\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePromoCodeRequest extends FormRequest
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
            'code' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('promo_codes', 'code')->ignore($this->route('id'))],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'discount_type' => ['sometimes', 'required', 'in:percentage,fixed'],
            'discount_value' => ['sometimes', 'required', 'numeric', 'min:0'],
            'valid_from' => ['sometimes', 'nullable', 'date'],
            'valid_until' => ['sometimes', 'nullable', 'date'],
            'max_uses' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'required', 'boolean'],
            'hall_id' => ['sometimes', 'required', 'integer', Rule::exists('halls', 'id')->where('owner_id', $this->user()->id)->whereNull('deleted_at')],
        ];
    }
}
