<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

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
                Infolists\Components\Section::make('User Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('name'),
                        Infolists\Components\TextEntry::make('email')->copyable(),
                        Infolists\Components\TextEntry::make('role')->badge(),
                        Infolists\Components\TextEntry::make('phone')->copyable(),
                        Infolists\Components\IconEntry::make('is_active')->boolean(),
                        Infolists\Components\IconEntry::make('email_verified_at')
                            ->label('Email Verified')
                            ->boolean(),
                    ])->columns(3),
            ]);
    }
}
