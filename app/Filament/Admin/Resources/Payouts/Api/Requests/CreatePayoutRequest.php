<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Payouts\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePayoutRequest extends FormRequest
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
            'owner_id' => ['required', 'integer', 'exists:users,id'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'status' => ['sometimes', 'string', 'max:20', 'in:pending,processing,completed,failed,cancelled,on_hold'],
            'adjustments' => ['sometimes', 'numeric'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'bank_details' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
            'failure_reason' => ['nullable', 'string'],
        ];
    }
}
