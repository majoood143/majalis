<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\EventTypes\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventTypeRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'array'],
            'name.en' => ['nullable', 'string'],
            'name.ar' => ['nullable', 'string'],
            'sort_order' => ['sometimes', 'required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
