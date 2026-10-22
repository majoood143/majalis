<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Tickets\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTicketRequest extends FormRequest
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
            'type' => ['sometimes', 'in:claim,complaint,inquiry,refund,cancellation,technical,feedback,other'],
            'priority' => ['sometimes', 'in:low,medium,high,urgent'],
            'status' => ['sometimes', 'in:open,pending,in_progress,on_hold,resolved,closed,cancelled,escalated'],
            'subject' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'booking_id' => ['nullable', 'integer', 'exists:bookings,id'],
            'due_date' => ['nullable', 'date'],
            'resolution' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
            'rating' => ['nullable', 'integer', 'min:0', 'between:1,5'],
            'feedback' => ['nullable', 'string'],
        ];
    }
}
