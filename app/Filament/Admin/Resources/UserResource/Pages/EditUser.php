<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('toggleActive')
                ->label(fn() => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn() => $this->record->is_active ? 'danger' : 'success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();

                    Notification::make()->success()->title('Status Updated')->send();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('verifyEmail')
                ->label('Verify Email')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn() => !$this->record->email_verified_at)
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->email_verified_at = now();
                    $this->record->save();

                    Notification::make()->success()->title('Email Verified')->send();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        Cache::tags(['users'])->flush();
    }
}
