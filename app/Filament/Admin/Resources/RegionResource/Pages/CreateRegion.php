<?php

namespace App\Filament\Admin\Resources\RegionResource\Pages;

use App\Filament\Admin\Resources\RegionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CreateRegion extends CreateRecord
{
    protected static string $resource = RegionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? true;

        return $data;
    }

    protected function afterCreate(): void
    {
        activity()
            ->performedOn($this->record)
            ->causedBy(Auth::user())
            ->log('Region created');

        Cache::tags(['regions'])->flush();
    }
}
