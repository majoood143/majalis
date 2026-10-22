<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Halls\Api\Requests;

use App\Rules\FileOrPath;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHallRequest extends FormRequest
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
            'description' => ['sometimes', 'required', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'region_id' => ['sometimes', 'required', 'integer', 'min:0', 'exists:regions,id'],
            'city_id' => ['sometimes', 'required', 'integer', 'exists:cities,id'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('halls', 'slug')->ignore($this->route('id'))],
            'address' => ['sometimes', 'nullable', 'string'],
            'address_localized' => ['sometimes', 'nullable', 'array'],
            'address_localized.en' => ['nullable', 'string'],
            'address_localized.ar' => ['nullable', 'string'],
            'google_maps_url' => ['sometimes', 'nullable', 'string', 'max:255'],
            'latitude' => ['sometimes', 'required', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'required', 'numeric', 'between:-180,180'],
            'capacity_min' => ['sometimes', 'required', 'integer', 'min:0'],
            'capacity_max' => ['sometimes', 'required', 'integer', 'min:1'],
            'area' => ['sometimes', 'required', 'integer'],
            'price_per_slot' => ['sometimes', 'required', 'numeric', 'min:0'],
            'pricing_override' => ['sometimes', 'nullable', 'array'],
            'allows_advance_payment' => ['sometimes', 'required', 'boolean'],
            'advance_payment_type' => ['sometimes', 'required', 'in:fixed,percentage'],
            'advance_payment_amount' => ['sometimes', 'nullable', 'numeric'],
            'advance_payment_percentage' => ['sometimes', 'nullable', 'numeric', 'between:0,100'],
            'minimum_advance_payment' => ['sometimes', 'nullable', 'numeric'],
            'features' => ['sometimes', 'nullable', 'array'],
            'features.*' => ['integer', 'exists:hall_features,id'],
            'featured_image' => ['sometimes', 'nullable', new FileOrPath],
            'gallery' => ['sometimes', 'nullable', 'array'],
            'gallery.*' => [new FileOrPath],
            'video_url' => ['sometimes', 'nullable', 'string', 'max:255'],
            'virtual_tour_url' => ['sometimes', 'nullable', 'string'],
            'phone' => ['sometimes', 'required', 'string', 'max:20'],
            'whatsapp' => ['sometimes', 'nullable', 'string', 'max:20'],
            'email' => ['sometimes', 'nullable', 'string', 'max:255', 'email'],
            'function_hours' => ['sometimes', 'nullable', 'array'],
            'is_24_hours' => ['sometimes', 'required', 'boolean'],
            'special_hours_note' => ['sometimes', 'nullable', 'string'],
            'is_active' => ['sometimes', 'required', 'boolean'],
            'requires_approval' => ['sometimes', 'required', 'boolean'],
            'cancellation_hours' => ['sometimes', 'required', 'integer'],
            'cancellation_fee_percentage' => ['sometimes', 'required', 'numeric', 'between:0,100'],
            'meta_title' => ['sometimes', 'nullable', 'array'],
            'meta_title.en' => ['nullable', 'string'],
            'meta_title.ar' => ['nullable', 'string'],
            'meta_description' => ['sometimes', 'nullable', 'array'],
            'meta_description.en' => ['nullable', 'string'],
            'meta_description.ar' => ['nullable', 'string'],
            'meta_keywords' => ['sometimes', 'nullable', 'array'],
        ];
    }
}
