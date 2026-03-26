<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Guava\FilamentKnowledgeBase\Filament\Panels\KnowledgeBasePanel;

class KnowledgeBasePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return KnowledgeBasePanel::make()
            ->colors([
                'primary' => Color::Amber,
            ]);
    }
}
