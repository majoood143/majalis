<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ExtraServices\Api\Requests;

use App\Rules\FileOrPath;
use Illuminate\Foundation\Http\FormRequest;

class CreateExtraServiceRequest extends FormRequest
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
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string'],
            'name.ar' => ['required', 'string'],
            'description' => ['required', 'array'],
            'description.en' => ['required', 'string'],
            'description.ar' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:255', 'in:per_person,per_item,per_hour,fixed'],
            'minimum_quantity' => ['sometimes', 'integer', 'min:1'],
            'maximum_quantity' => ['nullable', 'integer'],
            'image' => ['nullable', new FileOrPath],
            'order' => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
            'is_required' => ['sometimes', 'boolean'],
        ];
    }
}
