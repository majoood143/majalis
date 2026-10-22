<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;

/**
 * Hall card for search results and lists.
 */
class HallListResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'city' => $this->whenLoaded('city', fn () => $this->city ? new LookupResource($this->city) : null),
            'region' => $this->whenLoaded('city', fn () => $this->city?->region ? new LookupResource($this->city->region) : null),
            'capacity_min' => $this->capacity_min,
            'capacity_max' => $this->capacity_max,
            'price_per_slot' => static::money($this->price_per_slot),
            'currency' => config('majalis.currency'),
            'average_rating' => $this->average_rating,
            'total_reviews' => (int) $this->total_reviews,
            'is_featured' => (bool) $this->is_featured,
            'featured_image_url' => static::fileUrl($this->featured_image),
            'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
            'hall_types' => LookupResource::collection($this->whenLoaded('hallTypes')),
            // Only present on date searches (AvailabilityService sets it)
            'is_available' => $this->whenHas('is_available'),
            // Only present when the request carries a customer token
            'is_favorite' => $this->whenHas('is_favorite'),
        ];
    }
}
