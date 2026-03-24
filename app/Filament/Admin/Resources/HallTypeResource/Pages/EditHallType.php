<?php

namespace App\Filament\Admin\Resources\HallTypeResource\Pages;

use App\Filament\Admin\Resources\HallTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditHallType extends EditRecord
{
    protected static string $resource = HallTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('hall-type.updated'))
            ->duration(5000);
    }
}
