<?php

namespace App\Filament\Admin\Resources\BookingResource\Pages;

use App\Filament\Admin\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),

            Actions\Action::make('confirm')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(fn() => $this->record->confirm())
                ->visible(fn() => $this->record->status->value === 'pending'),

            Actions\Action::make('cancel')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Cancellation Reason')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->cancel($data['reason']);
                })
                ->visible(fn() => in_array($this->record->status->value, ['pending', 'confirmed'])),

            Actions\Action::make('complete')
                ->icon('heroicon-o-check-badge')
                ->color('info')
                ->requiresConfirmation()
                ->action(fn() => $this->record->complete())
                ->visible(fn() => $this->record->status->value === 'confirmed' &&
                    $this->record->booking_date->isPast()),

            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Booking updated successfully';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Recalculate totals if pricing fields are changed
        if ($this->record->isDirty(['hall_price', 'services_price'])) {
            // Pricing will be recalculated
        }

        return $data;
    }
}
