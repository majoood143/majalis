<?php

namespace App\Filament\Admin\Pages;

use GeoSot\FilamentEnvEditor\Pages\ViewEnv;

class EnvEditor extends ViewEnv
{
    public static function getSlug(): string
    {
        return 'env-editor';
    }
}
