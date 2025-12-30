<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\AvailabilityResource\Pages;

use App\Filament\Owner\Resources\AvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

/**
 * EditAvailability Page for Owner Panel
 *
 * Allows owners to edit individual availability slots.
 */
class EditAvailability extends EditRecord
{
    /**
     * The resource this page belongs to.
     */
    protected static string $resource = AvailabilityResource::class;

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return __('owner.availability_resource.edit.title', [
            'date' => $this->record->date->format('d M Y'),
            'slot' => __("owner.slots.{$this->record->time_slot}"),
        ]);
    }

    /**
     * Mount the page and verify ownership.
     */
    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Verify ownership
        $user = Auth::user();
        if ($this->record->hall->owner_id !== $user->id) {
            Notification::make()
                ->danger()
                ->title(__('owner.errors.unauthorized'))
                ->send();

            $this->redirect(AvailabilityResource::getUrl('index'));
        }
    }

    /**
     * Get the header actions.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            // Quick Toggle
            Actions\Action::make('toggle')
                ->label(fn (): string => $this->record->is_available
                    ? __('owner.availability_resource.actions.block')
                    : __('owner.availability_resource.actions.unblock'))
                ->icon(fn (): string => $this->record->is_available
                    ? 'heroicon-o-x-circle'
                    : 'heroicon-o-check-circle')
                ->color(fn (): string => $this->record->is_available ? 'danger' : 'success')
                ->action(function (): void {
                    if ($this->record->is_available) {
                        $this->record->block();
                    } else {
                        $this->record->unblock();
                    }

                    Notification::make()
                        ->success()
                        ->title($this->record->is_available
                            ? __('owner.availability.notifications.unblocked')
                            : __('owner.availability.notifications.blocked'))
                        ->send();

                    $this->refreshFormData(['is_available', 'reason']);
                }),

            // View Hall
            Actions\Action::make('view_hall')
                ->label(__('owner.availability_resource.actions.view_hall'))
                ->icon('heroicon-o-building-office-2')
                ->color('gray')
                ->url(fn () => \App\Filament\Owner\Resources\HallResource::getUrl('edit', [
                    'record' => $this->record->hall_id,
                ])),

            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Get the saved notification.
     */
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('owner.availability_resource.notifications.updated'))
            ->body(__('owner.availability_resource.notifications.updated_body'));
    }

    /**
     * Get the redirect URL after saving.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
