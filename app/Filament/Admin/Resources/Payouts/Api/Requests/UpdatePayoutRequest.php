<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Payouts\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePayoutRequest extends FormRequest
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
            'owner_id' => ['sometimes', 'required', 'integer', 'exists:users,id'],
            'period_start' => ['sometimes', 'required', 'date'],
            'period_end' => ['sometimes', 'required', 'date'],
            'status' => ['sometimes', 'required', 'string', 'max:20', 'in:pending,processing,completed,failed,cancelled,on_hold'],
            'gross_revenue' => ['sometimes', 'required', 'numeric'],
            'commission_amount' => ['sometimes', 'required', 'numeric'],
            'bookings_count' => ['sometimes', 'required', 'integer', 'min:0'],
            'adjustments' => ['sometimes', 'required', 'numeric'],
            'payment_method' => ['sometimes', 'nullable', 'string', 'max:50'],
            'transaction_reference' => ['sometimes', 'nullable', 'string', 'max:100'],
            'bank_details' => ['sometimes', 'nullable', 'array'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'failure_reason' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
