<?php

namespace App\Filament\Admin\Resources\TicketResource\Pages;

use App\Filament\Admin\Resources\TicketResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

/**
 * Create Ticket Page
 * 
 * Handles the creation of new support tickets with automatic field population
 * and validation.
 * 
 * @package App\Filament\Admin\Resources\TicketResource\Pages
 * @version 1.0.0
 */
class CreateTicket extends CreateRecord
{
    /**
     * The resource this page belongs to.
     *
     * @var string
     */
    protected static string $resource = TicketResource::class;

    /**
     * Mutate the form data before creating the ticket.
     * Automatically populates certain fields.
     * 
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // If no assigned_to is set, don't auto-assign
        // Let staff manually assign or use the auto-assignment logic

        return $data;
    }

    /**
     * Get the redirect URL after creating the ticket.
     * Redirects to the view page of the created ticket.
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
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Ticket created successfully';
    }
}
