<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Tickets\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'subject' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'booking_id' => ['nullable', 'integer', Rule::exists('bookings', 'id')->where(fn ($query) => $query->whereIn('hall_id', fn ($halls) => $halls->select('id')->from('halls')->where('owner_id', $this->user()->id)))],
        ];
    }
}
