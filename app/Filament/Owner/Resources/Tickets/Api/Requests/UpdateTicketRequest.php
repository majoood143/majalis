<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Tickets\Api\Requests;

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
            'status' => ['sometimes', 'required', 'in:open,pending,in_progress,on_hold,resolved,closed,cancelled,escalated'],
            'priority' => ['sometimes', 'required', 'in:low,medium,high,urgent'],
            'resolution' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
