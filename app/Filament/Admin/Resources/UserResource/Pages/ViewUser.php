<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use App\Filament\Admin\Resources\UserResource;
use App\Filament\Admin\Resources\UserResource\Widgets\UserRecentBookings;
use App\Filament\Admin\Resources\UserResource\Widgets\UserStatsOverview;
use App\Filament\Admin\Resources\UserResource\Widgets\UserTickets;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
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

    public function infolist(Schema $schema): Schema
    {
        return $infolist
            ->schema([
                Section::make('User Information')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')->copyable(),
                        TextEntry::make('role')->badge(),
                        TextEntry::make('phone')->copyable(),
                        IconEntry::make('is_active')->boolean(),
                        IconEntry::make('email_verified_at')
                            ->label('Email Verified')
                            ->boolean(),
                    ])->columns(3),
            ]);
    }
}
