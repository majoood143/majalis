<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Bookings\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
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
            'status' => ['sometimes', 'required', 'string', 'max:255', 'in:pending,confirmed,cancelled,completed,refunded'],
            'payment_status' => ['sometimes', 'required', 'string', 'max:255', 'in:pending,paid,failed,refunded,partially_refunded'],
            'customer_name' => ['sometimes', 'required', 'string', 'max:255'],
            'customer_email' => ['sometimes', 'required', 'string', 'max:255', 'email'],
            'customer_phone' => ['sometimes', 'required', 'string', 'max:20'],
            'customer_notes' => ['sometimes', 'nullable', 'string'],
            'number_of_guests' => ['sometimes', 'required', 'integer'],
            'admin_notes' => ['sometimes', 'nullable', 'string'],
            'cancellation_reason' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
