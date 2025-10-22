<?php

namespace App\Filament\Admin\Resources\PaymentResource\Pages;

use App\Filament\Admin\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('processRefund')
                ->label('Refund')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn() => $this->record->canBeRefunded())
                ->form([
                    \Filament\Forms\Components\TextInput::make('amount')
                        ->numeric()
                        ->required()
                        ->prefix('OMR')
                        ->default(fn() => $this->record->amount),

                    \Filament\Forms\Components\Textarea::make('reason')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $this->record->refund($data['amount'], $data['reason']);
                    Notification::make()->success()->title('Refund Processed')->send();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('downloadReceipt')
                ->label('Download Receipt')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->visible(fn() => $this->record->status === 'paid')
                ->action(fn() => $this->downloadReceipt()),

            Actions\DeleteAction::make()
                ->successRedirectUrl(route('filament.admin.resources.payments.index')),
        ];
    }

    protected function downloadReceipt(): void
    {
        // Implement PDF receipt generation
        Notification::make()
            ->success()
            ->title('Receipt Generated')
            ->send();
    }

    public function getTitle(): string
    {
        return 'Payment: ' . $this->record->payment_reference;
    }

    public function getSubheading(): ?string
    {
        $status = ucfirst($this->record->status);
        $amount = number_format($this->record->amount, 3) . ' OMR';

        return "{$status} • {$amount}";
    }
}
