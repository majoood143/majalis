<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\GuestSessions\Api\Transformers;

use App\Filament\Api\Transformers\Transformer;
use App\Models\GuestSession;

/**
 * @property GuestSession $resource
 */
class GuestSessionTransformer extends Transformer
{
    protected array $except = ['session_token'];
}
