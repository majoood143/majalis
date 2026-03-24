<?php

namespace App\Filament\Admin\Resources\HallCategoryResource\Pages;

use App\Filament\Admin\Resources\HallCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditHallCategory extends EditRecord
{
    protected static string $resource = HallCategoryResource::class;

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
            ->title(__('hall-category.updated'))
            ->duration(5000);
    }
}
