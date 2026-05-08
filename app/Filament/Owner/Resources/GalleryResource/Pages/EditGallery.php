<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\GalleryResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use App\Filament\Owner\Resources\GalleryResource;
use App\Models\Hall;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * EditGallery Page for Owner Panel
 *
 * Edit image metadata and settings.
 */
class EditGallery extends EditRecord
{
    /**
     * The resource this page belongs to.
     */
    protected static string $resource = GalleryResource::class;

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return __('owner.gallery.edit.title') ?? 'Edit Image';
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
                ->title(__('owner.errors.unauthorized') ?? 'Unauthorized')
                ->send();

            $this->redirect(GalleryResource::getUrl('index'));
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
            // Toggle Featured
            Action::make('toggle_featured')
                ->label(fn (): string => $this->record->is_featured
                    ? __('owner.gallery.actions.unmark_featured') ?? 'Unmark Featured'
                    : __('owner.gallery.actions.mark_featured') ?? 'Mark Featured')
                ->icon('heroicon-o-star')
                ->color(fn (): string => $this->record->is_featured ? 'gray' : 'warning')
                ->action(function (): void {
                    $this->record->update(['is_featured' => !$this->record->is_featured]);

                    Notification::make()
                        ->success()
                        ->title($this->record->is_featured
                            ? __('owner.gallery.notifications.marked_featured') ?? 'Marked as Featured'
                            : __('owner.gallery.notifications.unmarked_featured') ?? 'Removed from Featured')
                        ->send();

                    $this->refreshFormData(['is_featured']);
                }),

            // Toggle Active
            Action::make('toggle_active')
                ->label(fn (): string => $this->record->is_active
                    ? __('owner.gallery.actions.deactivate') ?? 'Deactivate'
                    : __('owner.gallery.actions.activate') ?? 'Activate')
                ->icon(fn (): string => $this->record->is_active
                    ? 'heroicon-o-x-circle'
                    : 'heroicon-o-check-circle')
                ->color(fn (): string => $this->record->is_active ? 'warning' : 'success')
                ->action(function (): void {
                    $this->record->update(['is_active' => !$this->record->is_active]);

                    Notification::make()
                        ->success()
                        ->title($this->record->is_active
                            ? __('owner.gallery.notifications.activated') ?? 'Image Activated'
                            : __('owner.gallery.notifications.deactivated') ?? 'Image Deactivated')
                        ->send();

                    $this->refreshFormData(['is_active']);
                }),

            // Delete
            DeleteAction::make()
                ->before(function (): void {
                    // Delete files from storage
                    if ($this->record->image_path && Storage::disk('public')->exists($this->record->image_path)) {
                        Storage::disk('public')->delete($this->record->image_path);
                    }
                    if ($this->record->thumbnail_path && Storage::disk('public')->exists($this->record->thumbnail_path)) {
                        Storage::disk('public')->delete($this->record->thumbnail_path);
                    }
                }),
        ];
    }

    /**
     * Mutate form data before save.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Verify hall ownership if changed
        if (isset($data['hall_id']) && $data['hall_id'] !== $this->record->hall_id) {
            $user = Auth::user();
            $hall = Hall::find($data['hall_id']);

            if (!$hall || $hall->owner_id !== $user->id) {
                Notification::make()
                    ->danger()
                    ->title(__('owner.errors.unauthorized') ?? 'Unauthorized')
                    ->send();

                $this->halt();
            }
        }

        // If type changed to 'featured', also mark as featured
        if (($data['type'] ?? '') === 'featured') {
            $data['is_featured'] = true;
        }

        return $data;
    }

    /**
     * Get the saved notification.
     */
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('owner.gallery.notifications.updated') ?? 'Image Updated')
            ->body(__('owner.gallery.notifications.updated_body') ?? 'The image details have been updated.');
    }

    /**
     * Get the redirect URL after saving.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
