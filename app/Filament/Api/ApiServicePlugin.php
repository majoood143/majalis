<?php

declare(strict_types=1);

namespace App\Filament\Api;

use Filament\Panel;
use Rupadana\ApiService\ApiServicePlugin as BaseApiServicePlugin;

/**
 * Registers a panel's resource APIs without adding the package's Token
 * resource. Use this on panels other than Admin (tokens are managed in Admin).
 */
class ApiServicePlugin extends BaseApiServicePlugin
{
    public function register(Panel $panel): void {}
}
