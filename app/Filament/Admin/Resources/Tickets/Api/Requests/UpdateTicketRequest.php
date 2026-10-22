<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Tickets\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
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
            'type' => ['sometimes', 'required', 'in:claim,complaint,inquiry,refund,cancellation,technical,feedback,other'],
            'priority' => ['sometimes', 'required', 'in:low,medium,high,urgent'],
            'status' => ['sometimes', 'required', 'in:open,pending,in_progress,on_hold,resolved,closed,cancelled,escalated'],
            'subject' => ['sometimes', 'required', 'string', 'max:200'],
            'description' => ['sometimes', 'required', 'string'],
            'user_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'assigned_to' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'booking_id' => ['sometimes', 'nullable', 'integer', 'exists:bookings,id'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'resolution' => ['sometimes', 'nullable', 'string'],
            'internal_notes' => ['sometimes', 'nullable', 'string'],
            'rating' => ['sometimes', 'nullable', 'integer', 'min:0', 'between:1,5'],
            'feedback' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
