<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallAvailabilities\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHallAvailabilityRequest extends FormRequest
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
            'hall_id' => ['sometimes', 'required', 'integer', 'exists:halls,id'],
            'date' => ['sometimes', 'required', 'date', 'after_or_equal:today'],
            'time_slot' => ['sometimes', 'required', 'string', 'max:255', 'in:morning,afternoon,evening,full_day'],
            'is_available' => ['sometimes', 'required', 'boolean'],
            'reason' => ['sometimes', 'nullable', 'string', 'max:255'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'custom_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ];
    }
}
