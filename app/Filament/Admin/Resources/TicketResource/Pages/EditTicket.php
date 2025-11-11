<?php

namespace App\Filament\Admin\Resources\TicketResource\Pages;

use App\Filament\Admin\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Edit Ticket Page
 * 
 * Handles editing of existing support tickets with validation and
 * automatic logging of changes.
 * 
 * @package App\Filament\Admin\Resources\TicketResource\Pages
 * @version 1.0.0
 */
class EditTicket extends EditRecord
{
    /**
     * The resource this page belongs to.
     *
     * @var string
     */
    protected static string $resource = TicketResource::class;

    /**
     * Get the header actions for the page.
     * 
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Get the redirect URL after saving the record.
     * Redirects to the view page after editing.
     * 
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    /**
     * Get the success notification message.
     * 
     * @return string
     */
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Ticket updated successfully';
    }

    /**
     * Mutate form data before filling the form.
     * Can be used to transform data before display.
     * 
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Add any data transformations here if needed
        return $data;
    }

    /**
     * Mutate form data before saving.
     * Can be used to add additional processing before save.
     * 
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Track status changes and update timestamps accordingly
        // This is already handled in the Ticket model's boot method
        return $data;
    }

    /**
     * Hook called after saving the record.
     * Use this for logging or triggering notifications.
     * 
     * @return void
     */
    protected function afterSave(): void
    {
        // Log activity using spatie/laravel-activitylog if needed
        activity()
            ->performedOn($this->record)
            ->causedBy(auth()->user())
            ->withProperties([
                'ticket_number' => $this->record->ticket_number,
                'status' => $this->record->status->value,
            ])
            ->log('Ticket updated');
    }
}
