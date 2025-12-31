<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\PricingResource\Pages;

use App\Filament\Owner\Resources\PricingResource;
use App\Models\Hall;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

/**
 * CreatePricing Page for Owner Panel
 *
 * Create new seasonal/special pricing rules.
 */
class CreatePricing extends CreateRecord
{
    /**
     * The resource this page belongs to.
     */
    protected static string $resource = PricingResource::class;

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return __('owner.pricing.create.title');
    }

    /**
     * Mutate form data before creation.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        // Verify hall ownership
        $hall = Hall::find($data['hall_id']);
        if (!$hall || $hall->owner_id !== $user->id) {
            Notification::make()
                ->danger()
                ->title(__('owner.errors.unauthorized'))
                ->send();

            $this->halt();
        }

        // Clean up apply_to_slots - if empty array, set to null (all slots)
        if (isset($data['apply_to_slots']) && empty($data['apply_to_slots'])) {
            $data['apply_to_slots'] = null;
        }

        // Clean up days_of_week - convert to integers
        if (!empty($data['days_of_week'])) {
            $data['days_of_week'] = array_map('intval', $data['days_of_week']);
        }

        return $data;
    }

    /**
     * Get the created notification.
     */
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('owner.pricing.notifications.created'))
            ->body(__('owner.pricing.notifications.created_body'));
    }

    /**
     * Get the redirect URL after creation.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
