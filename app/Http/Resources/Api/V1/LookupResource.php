<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;

/**
 * Small reference records used by filters and forms:
 * regions, cities, hall types, hall features, event types.
 */
class LookupResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->whenHas('slug'),
            'code' => $this->whenHas('code'),
            'icon' => $this->whenHas('icon'),
            'region_id' => $this->whenHas('region_id'),
        ];
    }
}
