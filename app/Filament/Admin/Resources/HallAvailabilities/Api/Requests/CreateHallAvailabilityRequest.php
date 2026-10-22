<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallAvailabilities\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateHallAvailabilityRequest extends FormRequest
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
            'hall_id' => ['required', 'integer', 'exists:halls,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time_slot' => ['required', 'string', 'max:255', 'in:morning,afternoon,evening,full_day'],
            'is_available' => ['sometimes', 'boolean'],
            'reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'custom_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
