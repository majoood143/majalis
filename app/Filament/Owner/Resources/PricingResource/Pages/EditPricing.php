<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\PricingResource\Pages;

use App\Filament\Owner\Resources\PricingResource;
use App\Models\Hall;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

/**
 * EditPricing Page for Owner Panel
 *
 * Edit existing pricing rules.
 */
class EditPricing extends EditRecord
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
        return __('owner.pricing.edit.title', [
            'name' => $this->record->getTranslation('name', app()->getLocale()),
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

            $this->redirect(PricingResource::getUrl('index'));
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
            // Toggle Active
            Actions\Action::make('toggle')
                ->label(fn (): string => $this->record->is_active
                    ? __('owner.pricing.actions.deactivate')
                    : __('owner.pricing.actions.activate'))
                ->icon(fn (): string => $this->record->is_active
                    ? 'heroicon-o-pause'
                    : 'heroicon-o-play')
                ->color(fn (): string => $this->record->is_active ? 'warning' : 'success')
                ->action(function (): void {
                    $this->record->update(['is_active' => !$this->record->is_active]);

                    Notification::make()
                        ->success()
                        ->title($this->record->is_active
                            ? __('owner.pricing.notifications.activated')
                            : __('owner.pricing.notifications.deactivated'))
                        ->send();

                    $this->refreshFormData(['is_active']);
                }),

            // Duplicate
            Actions\Action::make('duplicate')
                ->label(__('owner.pricing.actions.duplicate'))
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->action(function (): void {
                    $newRule = $this->record->replicate();
                    $newRule->name = [
                        'en' => $this->record->getTranslation('name', 'en') . ' (Copy)',
                        'ar' => $this->record->getTranslation('name', 'ar') . ' (نسخة)',
                    ];
                    $newRule->is_active = false;
                    $newRule->save();

                    Notification::make()
                        ->success()
                        ->title(__('owner.pricing.notifications.duplicated'))
                        ->send();

                    $this->redirect(PricingResource::getUrl('edit', ['record' => $newRule->id]));
                }),

            Actions\DeleteAction::make(),
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
                    ->title(__('owner.errors.unauthorized'))
                    ->send();

                $this->halt();
            }
        }

        // Clean up apply_to_slots
        if (isset($data['apply_to_slots']) && empty($data['apply_to_slots'])) {
            $data['apply_to_slots'] = null;
        }

        // Clean up days_of_week
        if (!empty($data['days_of_week'])) {
            $data['days_of_week'] = array_map('intval', $data['days_of_week']);
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
            ->title(__('owner.pricing.notifications.updated'))
            ->body(__('owner.pricing.notifications.updated_body'));
    }

    /**
     * Get the redirect URL after saving.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
