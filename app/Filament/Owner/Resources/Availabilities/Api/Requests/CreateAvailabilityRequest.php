<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Availabilities\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Bulk availability: one `date`, or a `start_date`–`end_date` range (max 366 days),
 * for each of `time_slots` (default: all four slots).
 */
class CreateAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hall_id' => ['required', 'integer', Rule::exists('halls', 'id')->where('owner_id', $this->user()->id)->whereNull('deleted_at')],
            'date' => ['required_without:start_date', 'nullable', 'date'],
            'start_date' => ['required_without:date', 'nullable', 'date'],
            'end_date' => ['required_with:start_date', 'nullable', 'date', 'after_or_equal:start_date', 'before_or_equal:' . now()->addDays(366)->toDateString()],
            'time_slots' => ['sometimes', 'array', 'min:1'],
            'time_slots.*' => ['string', 'in:morning,afternoon,evening,full_day'],
            'is_available' => ['sometimes', 'boolean'],
            'reason' => ['nullable', 'string', 'max:255'],
            'custom_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
