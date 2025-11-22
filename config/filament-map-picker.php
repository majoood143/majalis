<?php

declare(strict_types=1);

/**
 * Filament Map Picker Configuration
 *
 * This configuration file customizes the map picker behavior
 * for the Majalis Hall Booking Platform.
 *
 * Default settings are optimized for Oman (Muscat region).
 *
 * @package Config
 * @version 1.0.0
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default Map Center
    |--------------------------------------------------------------------------
    |
    | The default center coordinates when no location is set.
    | These coordinates point to Muscat, Oman.
    |
    */
    'default_location' => [
        'lat' => 23.5880,
        'lng' => 58.3829,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Zoom Level
    |--------------------------------------------------------------------------
    |
    | The default zoom level for the map.
    |
    | Zoom levels:
    | - 1-5: Continent/Country level
    | - 6-10: Region/City level
    | - 11-14: City/District level
    | - 15-18: Street/Building level
    | - 19-22: Building/Room level
    |
    */
    'default_zoom' => 10,

    /*
    |--------------------------------------------------------------------------
    | Tile Provider
    |--------------------------------------------------------------------------
    |
    | The tile provider URL for the map.
    | OpenStreetMap is free and has good coverage for Oman.
    |
    | Available options:
    | - OpenStreetMap: https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png
    | - CartoDB Positron: https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png
    | - CartoDB Dark: https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png
    |
    */
    'tile_url' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',

    /*
    |--------------------------------------------------------------------------
    | Tile Attribution
    |--------------------------------------------------------------------------
    |
    | Attribution text for the tile provider.
    | Required by OpenStreetMap license.
    |
    */
    'tile_attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',

    /*
    |--------------------------------------------------------------------------
    | Map Height
    |--------------------------------------------------------------------------
    |
    | Default height for the map component.
    |
    */
    'map_height' => '400px',

    /*
    |--------------------------------------------------------------------------
    | Controls
    |--------------------------------------------------------------------------
    |
    | Configure which controls are shown on the map.
    |
    */
    'controls' => [
        'zoom' => true,
        'fullscreen' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Marker Options
    |--------------------------------------------------------------------------
    |
    | Configuration for the map marker.
    |
    */
    'marker' => [
        'draggable' => true,
        // Custom marker icon (optional)
        'icon' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Oman Boundaries (Optional)
    |--------------------------------------------------------------------------
    |
    | Geographic boundaries for Oman.
    | Can be used to restrict map panning/zoom.
    |
    */
    'bounds' => [
        'north' => 26.4,  // Northern border
        'south' => 16.4,  // Southern border (Dhofar)
        'east' => 60.0,   // Eastern border
        'west' => 51.8,   // Western border
    ],

    /*
    |--------------------------------------------------------------------------
    | Popular Oman Locations
    |--------------------------------------------------------------------------
    |
    | Quick-access coordinates for major Omani cities.
    | Can be used for dropdown selection or suggestions.
    |
    */
    'quick_locations' => [
        'muscat' => [
            'name' => 'Muscat',
            'name_ar' => 'مسقط',
            'lat' => 23.5880,
            'lng' => 58.3829,
        ],
        'salalah' => [
            'name' => 'Salalah',
            'name_ar' => 'صلالة',
            'lat' => 17.0150,
            'lng' => 54.0924,
        ],
        'sohar' => [
            'name' => 'Sohar',
            'name_ar' => 'صحار',
            'lat' => 24.3474,
            'lng' => 56.7333,
        ],
        'nizwa' => [
            'name' => 'Nizwa',
            'name_ar' => 'نزوى',
            'lat' => 22.9333,
            'lng' => 57.5333,
        ],
        'sur' => [
            'name' => 'Sur',
            'name_ar' => 'صور',
            'lat' => 22.5667,
            'lng' => 59.5289,
        ],
        'ibri' => [
            'name' => 'Ibri',
            'name_ar' => 'عبري',
            'lat' => 23.2167,
            'lng' => 56.5167,
        ],
        'barka' => [
            'name' => 'Barka',
            'name_ar' => 'بركاء',
            'lat' => 23.7000,
            'lng' => 57.8833,
        ],
        'rustaq' => [
            'name' => 'Rustaq',
            'name_ar' => 'الرستاق',
            'lat' => 23.3833,
            'lng' => 57.4333,
        ],
    ],
];
