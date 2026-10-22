<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;

class ExtraServiceResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => static::money($this->price),
            'unit' => $this->unit,
            'minimum_quantity' => $this->minimum_quantity,
            'maximum_quantity' => $this->maximum_quantity,
            'is_required' => (bool) $this->is_required,
            'image_url' => static::fileUrl($this->image),
        ];
    }
}
