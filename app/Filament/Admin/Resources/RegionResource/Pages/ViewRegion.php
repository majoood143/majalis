<?php

namespace App\Filament\Admin\Resources\RegionResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use App\Filament\Admin\Resources\RegionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;

class ViewRegion extends ViewRecord
{
    protected static string $resource = RegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $infolist
            ->schema([
                Section::make('Region Information')
                    ->schema([
                        TextEntry::make('name')
                            ->formatStateUsing(fn($record) => $record->name),
                        TextEntry::make('code')->badge(),
                        TextEntry::make('cities_count')
                            ->state(fn($record) => $record->cities()->count())
                            ->badge(),
                        IconEntry::make('is_active')->boolean(),
                    ])->columns(2),
            ]);
    }
}
