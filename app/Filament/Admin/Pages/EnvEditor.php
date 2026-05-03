<?php

namespace App\Filament\Admin\Pages;

use Filament\Panel;
use GeoSot\FilamentEnvEditor\Pages\ViewEnv;

class EnvEditor extends ViewEnv
{
    public static function getSlug(?Panel $panel = null): string
    {
        return 'env-editor';
    }
}
