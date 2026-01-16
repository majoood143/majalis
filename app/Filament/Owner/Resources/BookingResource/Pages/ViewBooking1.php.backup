<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\BookingResource\Pages;

use App\Filament\Owner\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBooking1 extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn() => in_array($this->record->status, ['pending', 'confirmed'])),

            Actions\Action::make('confirm')
                ->label('Confirm Booking')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn() => $this->record->status === 'pending')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->confirm();
                    $this->notify('success', 'Booking confirmed successfully!');
                }),

            Actions\Action::make('cancel')
                ->label('Cancel Booking')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->visible(fn() => $this->record->canBeCancelled())
                ->requiresConfirmation()
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Cancellation Reason')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->cancel($data['reason']);
                    $this->notify('success', 'Booking cancelled successfully!');
                }),
        ];
    }
}
