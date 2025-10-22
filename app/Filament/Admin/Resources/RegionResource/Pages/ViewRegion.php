<?php

namespace App\Filament\Admin\Resources\RegionResource\Pages;

use App\Filament\Admin\Resources\RegionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists;

class ViewRegion extends ViewRecord
{
    protected static string $resource = RegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Region Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->formatStateUsing(fn($record) => $record->name),
                        Infolists\Components\TextEntry::make('code')->badge(),
                        Infolists\Components\TextEntry::make('cities_count')
                            ->state(fn($record) => $record->cities()->count())
                            ->badge(),
                        Infolists\Components\IconEntry::make('is_active')->boolean(),
                    ])->columns(2),
            ]);
    }
}
