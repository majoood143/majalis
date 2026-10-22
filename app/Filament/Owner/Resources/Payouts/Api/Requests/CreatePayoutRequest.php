<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Payouts\Api\Requests;

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
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start', 'before_or_equal:today'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
