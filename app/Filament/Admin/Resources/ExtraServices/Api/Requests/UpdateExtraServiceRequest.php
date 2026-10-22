<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ExtraServices\Api\Requests;

use App\Rules\FileOrPath;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExtraServiceRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'array'],
            'name.en' => ['nullable', 'string'],
            'name.ar' => ['nullable', 'string'],
            'description' => ['sometimes', 'required', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'unit' => ['sometimes', 'nullable', 'string', 'max:255', 'in:per_person,per_item,per_hour,fixed'],
            'minimum_quantity' => ['sometimes', 'required', 'integer', 'min:1'],
            'maximum_quantity' => ['sometimes', 'nullable', 'integer'],
            'image' => ['sometimes', 'nullable', new FileOrPath],
            'order' => ['sometimes', 'required', 'integer'],
            'is_active' => ['sometimes', 'required', 'boolean'],
            'is_required' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
