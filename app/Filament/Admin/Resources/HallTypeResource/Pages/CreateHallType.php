<?php

namespace App\Filament\Admin\Resources\HallTypeResource\Pages;

use App\Filament\Admin\Resources\HallTypeResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class CreateHallType extends CreateRecord
{
    protected static string $resource = HallTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('hall-type.created'))
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['slug']) && isset($data['name']['en'])) {
            $slug = Str::slug($data['name']['en']);
            $base = $slug;
            $counter = 1;
            while (\App\Models\HallType::where('slug', $slug)->exists()) {
                $slug = $base . '-' . $counter++;
            }
            $data['slug'] = $slug;
        }

        return $data;
    }
}
