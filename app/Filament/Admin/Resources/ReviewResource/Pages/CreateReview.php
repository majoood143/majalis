<?php

namespace App\Filament\Admin\Resources\ReviewResource\Pages;

use App\Filament\Admin\Resources\ReviewResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CreateReview extends CreateRecord
{
    protected static string $resource = ReviewResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validate rating
        if ($data['rating'] < 1 || $data['rating'] > 5) {
            Notification::make()
                ->danger()
                ->title('Invalid Rating')
                ->body('Rating must be between 1 and 5.')
                ->persistent()
                ->send();

            $this->halt();
        }

        $data['is_approved'] = $data['is_approved'] ?? false;
        $data['is_featured'] = $data['is_featured'] ?? false;

        return $data;
    }

    protected function afterCreate(): void
    {
        activity()
            ->performedOn($this->record)
            ->causedBy(Auth::user())
            ->log('Review created manually');

        Cache::tags(['reviews', 'hall_' . $this->record->hall_id])->flush();
    }
}
