<?php

namespace App\Filament\Admin\Resources\PaymentResource\Pages;

use App\Filament\Admin\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),

            Actions\Action::make('markAsPaid')
                ->label('Mark as Paid')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->status === 'pending')
                ->action(function () {
                    $this->record->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);

                    Notification::make()->success()->title('Marked as Paid')->send();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('processRefund')
                ->label('Process Refund')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->form([
                    \Filament\Forms\Components\TextInput::make('amount')
                        ->label('Refund Amount')
                        ->numeric()
                        ->required()
                        ->prefix('OMR')
                        ->default(fn() => $this->record->amount),

                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Refund Reason')
                        ->required()
                        ->rows(3),
                ])
                ->visible(fn() => $this->record->canBeRefunded())
                ->action(function (array $data) {
                    $this->record->refund($data['amount'], $data['reason']);

                    Notification::make()->success()->title('Refund Processed')->send();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalDescription('This will permanently delete the payment record.'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function afterSave(): void
    {
        activity()
            ->performedOn($this->record)
            ->causedBy(Auth::user())
            ->log('Payment updated');

        Cache::tags(['payments'])->flush();
    }
}
