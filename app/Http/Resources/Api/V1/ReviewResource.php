<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Public review. Only the reviewer's first name is exposed.
 */
class ReviewResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'cleanliness_rating' => $this->cleanliness_rating,
            'service_rating' => $this->service_rating,
            'value_rating' => $this->value_rating,
            'location_rating' => $this->location_rating,
            'comment' => $this->comment,
            'owner_response' => $this->owner_response,
            'reviewer_name' => $this->relationLoaded('user') && $this->user
                ? Str::before(trim((string) $this->user->name), ' ')
                : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
