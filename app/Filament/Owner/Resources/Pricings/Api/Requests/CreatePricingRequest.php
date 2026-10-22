<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Pricings\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePricingRequest extends FormRequest
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
            'hall_id' => ['required', 'integer', Rule::exists('halls', 'id')->where('owner_id', $this->user()->id)->whereNull('deleted_at')],
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string'],
            'name.ar' => ['required', 'string'],
            'type' => ['sometimes', 'string', 'max:255', 'in:seasonal,holiday,weekend,special_event,early_bird,last_minute'],
            'priority' => ['sometimes', 'integer'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'is_recurring' => ['sometimes', 'boolean'],
            'recurrence_type' => ['nullable', 'string', 'max:255', 'in:weekly,yearly'],
            'days_of_week' => ['nullable', 'array'],
            'days_of_week.*' => ['integer', 'between:0,6'],
            'adjustment_type' => ['sometimes', 'string', 'max:255', 'in:percentage,fixed_increase,fixed_price'],
            'adjustment_value' => ['sometimes', 'numeric'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'apply_to_slots' => ['nullable', 'array'],
            'apply_to_slots.*' => ['string', 'in:morning,afternoon,evening,full_day'],
            'is_active' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
