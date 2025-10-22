<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_active'] = $data['is_active'] ?? true;
        $data['phone_country_code'] = $data['phone_country_code'] ?? '+968';

        return $data;
    }

    protected function afterCreate(): void
    {
        activity()
            ->performedOn($this->record)
            ->causedBy(Auth::user())
            ->log('User created');

        Cache::tags(['users'])->flush();
    }
}
