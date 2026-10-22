<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Filament\Facades\Filament;
use Illuminate\Http\Request;

/**
 * The signed-in user (GET /api/v1/me, login and register responses).
 */
class UserResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified' => $this->email_verified_at !== null,
            'phone' => $this->phone,
            'phone_country_code' => $this->phone_country_code,
            'role' => $this->role?->value,
            'language_preference' => $this->language_preference,
            // The app shows owner screens (phase 6) only when the Owner panel would let them in.
            'can_use_owner_mode' => $this->canAccessPanel(Filament::getPanel('owner')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
