<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Payments\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentRequest extends FormRequest
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
            'status' => ['sometimes', 'required', 'string', 'max:255', 'in:pending,paid,failed,refunded,partially_refunded'],
            'transaction_id' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('payments', 'transaction_id')->ignore($this->route('id'))],
            'payment_method' => ['sometimes', 'nullable', 'string', 'max:255'],
            'refund_amount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'refund_reason' => ['sometimes', 'nullable', 'string'],
            'failure_reason' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
