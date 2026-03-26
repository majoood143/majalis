<?php

namespace App\Filament\Admin\Resources\GuestSessionResource\Pages;

use App\Filament\Admin\Resources\GuestSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGuestSession extends ViewRecord
{
    protected static string $resource = GuestSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label(__('guest-session.hard_delete'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('guest-session.hard_delete_heading'))
                ->modalDescription(__('guest-session.hard_delete_description'))
                ->modalSubmitActionLabel(__('guest-session.hard_delete_confirm')),
        ];
    }
}
