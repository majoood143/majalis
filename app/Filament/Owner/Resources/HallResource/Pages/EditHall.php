<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\HallResource\Pages;

use App\Filament\Owner\Resources\HallResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

/**
 * EditHall Page for Owner Panel
 *
 * Allows owners to edit their hall details.
 */
class EditHall extends EditRecord
{
    /**
     * The resource this page belongs to.
     *
     * @var string
     */
    protected static string $resource = HallResource::class;

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        $hallName = $this->record->getTranslation('name', app()->getLocale());
        return __('owner.halls.edit.title', ['name' => $hallName]);
    }

    /**
     * Get the header actions.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            // View Hall
            Actions\ViewAction::make()
                ->label(__('owner.halls.actions.view'))
                ->icon('heroicon-o-eye'),

            // Manage Availability
            Actions\Action::make('availability')
                ->label(__('owner.halls.actions.availability'))
                ->icon('heroicon-o-calendar')
                ->color('info')
                ->url(fn () => HallResource::getUrl('availability', ['record' => $this->record])),

            // Regenerate Availability
            Actions\Action::make('regenerate_availability')
                ->label(__('owner.halls.actions.regenerate'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('owner.halls.modals.regenerate_heading'))
                ->modalDescription(__('owner.halls.modals.regenerate_description'))
                ->action(function (): void {
                    $this->record->generateAvailability();

                    Notification::make()
                        ->success()
                        ->title(__('owner.halls.notifications.availability_regenerated'))
                        ->send();
                }),

            // Toggle Active Status
            Actions\Action::make('toggle_active')
                ->label(fn (): string => $this->record->is_active
                    ? __('owner.halls.actions.deactivate')
                    : __('owner.halls.actions.activate'))
                ->icon(fn (): string => $this->record->is_active
                    ? 'heroicon-o-pause'
                    : 'heroicon-o-play')
                ->color(fn (): string => $this->record->is_active ? 'warning' : 'success')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update(['is_active' => !$this->record->is_active]);

                    Notification::make()
                        ->success()
                        ->title($this->record->is_active
                            ? __('owner.halls.notifications.activated')
                            : __('owner.halls.notifications.deactivated'))
                        ->send();

                    $this->refreshFormData(['is_active']);
                }),

            // Delete Action
            Actions\DeleteAction::make()
                ->label(__('owner.halls.actions.delete'))
                ->requiresConfirmation()
                ->modalHeading(__('owner.halls.modals.delete_heading'))
                ->modalDescription(__('owner.halls.modals.delete_description'))
                ->before(function (): void {
                    // Check if hall has active bookings
                    $activeBookings = $this->record->bookings()
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->count();

                    if ($activeBookings > 0) {
                        Notification::make()
                            ->danger()
                            ->title(__('owner.halls.errors.cannot_delete'))
                            ->body(__('owner.halls.errors.has_active_bookings', ['count' => $activeBookings]))
                            ->persistent()
                            ->send();

                        $this->halt();
                    }
                }),
        ];
    }

    /**
     * Mutate form data before filling the form.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure features is properly formatted
        if (isset($data['features'])) {
            if (is_string($data['features'])) {
                $data['features'] = json_decode($data['features'], true) ?? [];
            }
            $data['features'] = array_map('intval', array_filter((array) $data['features']));
        }

        return $data;
    }

    /**
     * Mutate form data before saving.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure features is an array
        if (isset($data['features']) && is_array($data['features'])) {
            $data['features'] = array_values(array_filter($data['features']));
        }

        // Prevent owner from changing is_featured
        unset($data['is_featured']);

        return $data;
    }

    /**
     * Get the saved notification.
     */
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('owner.halls.notifications.updated'))
            ->body(__('owner.halls.notifications.updated_body'));
    }

    /**
     * Get the redirect URL after saving.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
