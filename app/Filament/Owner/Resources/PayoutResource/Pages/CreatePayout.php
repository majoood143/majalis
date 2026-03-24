<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\PayoutResource\Pages;

use App\Enums\PayoutStatus;
use App\Filament\Owner\Resources\PayoutResource;
use App\Models\OwnerPayout;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePayout extends CreateRecord
{
    protected static string $resource = PayoutResource::class;

    public function getTitle(): string
    {
        return __('owner.payouts.create_title');
    }

    public function getHeading(): string
    {
        return __('owner.payouts.create_title');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['owner_id']    = Auth::id();
        $data['status']      = PayoutStatus::PENDING->value;
        $data['adjustments'] = 0;

        return $data;
    }

    protected function beforeCreate(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        $overlapping = OwnerPayout::where('owner_id', $user->id)
            ->whereNotIn('status', [PayoutStatus::CANCELLED->value, PayoutStatus::FAILED->value])
            ->where('period_start', '<=', $data['period_end'])
            ->where('period_end', '>=', $data['period_start'])
            ->exists();

        if ($overlapping) {
            Notification::make()
                ->danger()
                ->title(__('owner.payouts.duplicate_period'))
                ->send();

            $this->halt();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('owner.payouts.create_success'));
    }
}
