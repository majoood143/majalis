<?php

namespace App\Filament\Admin\Resources\PaymentResource\Pages;

use App\Filament\Admin\Resources\PaymentResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate payment reference
        $data['payment_reference'] = 'PAY-' . strtoupper(uniqid());

        // Validate amount
        if ($data['amount'] <= 0) {
            Notification::make()
                ->danger()
                ->title('Invalid Amount')
                ->body('Amount must be greater than 0.')
                ->persistent()
                ->send();

            $this->halt();
        }

        // Set timestamps based on status
        if ($data['status'] === 'paid' && empty($data['paid_at'])) {
            $data['paid_at'] = now();
        }

        if ($data['status'] === 'failed' && empty($data['failed_at'])) {
            $data['failed_at'] = now();
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        activity()
            ->performedOn($this->record)
            ->causedBy(Auth::user())
            ->log('Payment record created manually');

        Cache::tags(['payments'])->flush();
    }

    public function getTitle(): string
    {
        return 'Create Payment Record';
    }
}
