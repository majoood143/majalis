<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Halls\Api\Requests;

use App\Rules\FileOrPath;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateHallRequest extends FormRequest
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
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string'],
            'name.ar' => ['required', 'string'],
            'description' => ['required', 'array'],
            'description.en' => ['required', 'string'],
            'description.ar' => ['required', 'string'],
            'region_id' => ['required', 'integer', 'min:0', 'exists:regions,id'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('halls', 'slug')],
            'address' => ['nullable', 'string', 'required_without:address_localized'],
            'address_localized' => ['nullable', 'array'],
            'address_localized.en' => ['nullable', 'string'],
            'address_localized.ar' => ['nullable', 'string'],
            'google_maps_url' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'capacity_min' => ['sometimes', 'integer', 'min:0'],
            'capacity_max' => ['required', 'integer', 'min:1'],
            'area' => ['required', 'integer'],
            'price_per_slot' => ['required', 'numeric', 'min:0'],
            'pricing_override' => ['nullable', 'array'],
            'allows_advance_payment' => ['sometimes', 'boolean'],
            'advance_payment_type' => ['sometimes', 'in:fixed,percentage'],
            'advance_payment_amount' => ['nullable', 'numeric'],
            'advance_payment_percentage' => ['nullable', 'numeric', 'between:0,100'],
            'minimum_advance_payment' => ['nullable', 'numeric'],
            'features' => ['nullable', 'array'],
            'features.*' => ['integer', 'exists:hall_features,id'],
            'featured_image' => ['nullable', new FileOrPath],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => [new FileOrPath],
            'video_url' => ['nullable', 'string', 'max:255'],
            'virtual_tour_url' => ['nullable', 'string'],
            'phone' => ['required', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'max:255', 'email'],
            'function_hours' => ['nullable', 'array'],
            'is_24_hours' => ['sometimes', 'boolean'],
            'special_hours_note' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'requires_approval' => ['sometimes', 'boolean'],
            'cancellation_hours' => ['sometimes', 'integer'],
            'cancellation_fee_percentage' => ['sometimes', 'numeric', 'between:0,100'],
            'meta_title' => ['nullable', 'array'],
            'meta_title.en' => ['nullable', 'string'],
            'meta_title.ar' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'array'],
            'meta_description.en' => ['nullable', 'string'],
            'meta_description.ar' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'array'],
        ];
    }
}
