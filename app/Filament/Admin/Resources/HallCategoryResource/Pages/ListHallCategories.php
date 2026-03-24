<?php

namespace App\Filament\Admin\Resources\HallCategoryResource\Pages;

use App\Filament\Admin\Resources\HallCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHallCategories extends ListRecords
{
    protected static string $resource = HallCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
}
