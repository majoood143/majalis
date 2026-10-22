<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Reviews\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
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
            'booking_id' => ['sometimes', 'required', 'integer', 'exists:bookings,id'],
            'user_id' => ['sometimes', 'required', 'integer', 'exists:users,id'],
            'rating' => ['sometimes', 'required', 'integer', 'min:0', 'between:1,5'],
            'comment' => ['sometimes', 'nullable', 'string'],
            'cleanliness_rating' => ['sometimes', 'nullable', 'integer', 'between:1,5'],
            'service_rating' => ['sometimes', 'nullable', 'integer', 'between:1,5'],
            'value_rating' => ['sometimes', 'nullable', 'integer', 'between:1,5'],
            'location_rating' => ['sometimes', 'nullable', 'integer', 'between:1,5'],
            'is_approved' => ['sometimes', 'required', 'boolean'],
            'is_featured' => ['sometimes', 'required', 'boolean'],
            'is_late_review' => ['sometimes', 'required', 'boolean'],
            'marketing_consent' => ['sometimes', 'required', 'boolean'],
            'admin_notes' => ['sometimes', 'nullable', 'string'],
            'owner_response' => ['sometimes', 'nullable', 'string'],
            'owner_response_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
