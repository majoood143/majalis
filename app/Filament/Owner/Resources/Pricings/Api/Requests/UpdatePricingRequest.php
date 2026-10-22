<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Pricings\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePricingRequest extends FormRequest
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
            'hall_id' => ['sometimes', 'required', 'integer', Rule::exists('halls', 'id')->where('owner_id', $this->user()->id)->whereNull('deleted_at')],
            'name' => ['sometimes', 'required', 'array'],
            'name.en' => ['nullable', 'string'],
            'name.ar' => ['nullable', 'string'],
            'type' => ['sometimes', 'required', 'string', 'max:255', 'in:seasonal,holiday,weekend,special_event,early_bird,last_minute'],
            'priority' => ['sometimes', 'required', 'integer'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['sometimes', 'required', 'date'],
            'is_recurring' => ['sometimes', 'required', 'boolean'],
            'recurrence_type' => ['sometimes', 'nullable', 'string', 'max:255', 'in:weekly,yearly'],
            'days_of_week' => ['sometimes', 'nullable', 'array'],
            'days_of_week.*' => ['integer', 'between:0,6'],
            'adjustment_type' => ['sometimes', 'required', 'string', 'max:255', 'in:percentage,fixed_increase,fixed_price'],
            'adjustment_value' => ['sometimes', 'required', 'numeric'],
            'min_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'max_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'apply_to_slots' => ['sometimes', 'nullable', 'array'],
            'apply_to_slots.*' => ['string', 'in:morning,afternoon,evening,full_day'],
            'is_active' => ['sometimes', 'required', 'boolean'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
