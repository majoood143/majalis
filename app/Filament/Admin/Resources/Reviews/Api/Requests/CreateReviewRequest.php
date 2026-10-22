<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Reviews\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateReviewRequest extends FormRequest
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
            'booking_id' => ['required', 'integer', 'exists:bookings,id', Rule::unique('reviews', 'booking_id')->where('user_id', $this->input('user_id'))],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'rating' => ['required', 'integer', 'min:0', 'between:1,5'],
            'comment' => ['nullable', 'string'],
            'cleanliness_rating' => ['nullable', 'integer', 'between:1,5'],
            'service_rating' => ['nullable', 'integer', 'between:1,5'],
            'value_rating' => ['nullable', 'integer', 'between:1,5'],
            'location_rating' => ['nullable', 'integer', 'between:1,5'],
            'is_approved' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_late_review' => ['sometimes', 'boolean'],
            'marketing_consent' => ['sometimes', 'boolean'],
            'admin_notes' => ['nullable', 'string'],
            'owner_response' => ['nullable', 'string'],
            'owner_response_at' => ['nullable', 'date'],
        ];
    }
}
