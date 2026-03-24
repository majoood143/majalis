<?php

namespace App\Filament\Admin\Resources\HallCategoryResource\Pages;

use App\Filament\Admin\Resources\HallCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class CreateHallCategory extends CreateRecord
{
    protected static string $resource = HallCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('hall-category.created'))
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['slug']) && isset($data['name']['en'])) {
            $slug = Str::slug($data['name']['en']);
            $base = $slug;
            $counter = 1;
            while (\App\Models\HallCategory::where('slug', $slug)->exists()) {
                $slug = $base . '-' . $counter++;
            }
            $data['slug'] = $slug;
        }

        return $data;
    }
}
