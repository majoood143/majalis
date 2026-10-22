<?php

return [
    'navigation' => [
        'token' => [
            'cluster' => null,
            'group' => 'User',
            'sort' => -1,
            'icon' => 'heroicon-o-key',
            'should_register_navigation' => false,
        ],
    ],
    'models' => [
        'token' => [
            'enable_policy' => true,
        ],
    ],
    'route' => [
        // false: App\Filament\Api\ApiService adds a literal panel prefix (/api/admin, /api/owner)
        // because both panels have resources with the same slugs.
        'panel_prefix' => false,
        'use_resource_middlewares' => false,
    ],
    'tenancy' => [
        'enabled' => false,
        'awareness' => false,
    ],
    'login-rules' => [
        'email' => 'required|email',
        'password' => 'required',
    ],
    'login-middleware' => [
        'throttle:6,1',
    ],
    'logout-middleware' => [
        'auth:sanctum',
        // Add any additional middleware you want to apply to the logout route
    ],
    'use-spatie-permission-middleware' => true,
];
