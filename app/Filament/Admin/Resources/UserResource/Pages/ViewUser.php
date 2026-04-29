<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Filament\Admin\Resources\UserResource\Widgets\UserRecentBookings;
use App\Filament\Admin\Resources\UserResource\Widgets\UserStatsOverview;
use App\Filament\Admin\Resources\UserResource\Widgets\UserTickets;
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

    protected function getHeaderWidgets(): array
    {
        return [
            UserStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            UserRecentBookings::class,
            UserTickets::class,
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
