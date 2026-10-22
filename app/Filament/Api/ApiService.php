<?php

declare(strict_types=1);

namespace App\Filament\Api;

use Filament\Panel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Rupadana\ApiService\ApiService as BaseApiService;

/**
 * Base API service for every Filament resource API in this app.
 *
 * Why not use the package's `{panel}` route prefix: the Admin and Owner panels
 * both have resources with the same slug (halls, bookings, tickets…), and
 * `api/{panel}/halls` would make one panel's routes overwrite the other's.
 * We therefore set `api-service.route.panel_prefix = false` and add a literal
 * panel prefix here: /api/admin/halls, /api/owner/halls.
 *
 * Every route requires a Sanctum token and panel access (see EnsureApiPanelAccess).
 */
abstract class ApiService extends BaseApiService
{
    public static function registerRoutes(Panel $panel)
    {
        $panelId = $panel->getId();
        $slug = static::getResource()::getSlug($panel);

        Route::name($panelId . '.' . str($slug)->replace('/', '.') . '.')
            ->prefix($panelId . '/' . $slug)
            ->middleware([
                'throttle:120,1',
                'api.locale',
                'auth:sanctum',
                'api.panel:' . $panelId,
            ])
            ->group(function (Router $router) {
                foreach (static::handlers() as $handler) {
                    $handler::route($router);
                }
            });
    }
}
