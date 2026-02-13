<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceFeeSettingResource\Pages;

use App\Filament\Admin\Resources\ServiceFeeSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Edit page for Service Fee Settings.
 *
 * Includes same validations as Create, plus change logging.
 */
class EditServiceFeeSetting extends EditRecord
{
    protected static string $resource = ServiceFeeSettingResource::class;

    /**
     * Header actions: Delete.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Validate form data before update.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If both hall and owner selected, prioritize hall
        if (isset($data['hall_id']) && isset($data['owner_id'])) {
            Notification::make()
                ->warning()
                ->title(__('service-fee.scope_adjusted'))
                ->body(__('service-fee.scope_adjusted_body'))
                ->send();

            unset($data['owner_id']);
        }

        // Percentage cannot exceed 100%
        if (($data['fee_type'] ?? '') === 'percentage' && ($data['fee_value'] ?? 0) > 100) {
            Notification::make()
                ->danger()
                ->title(__('service-fee.invalid_value'))
                ->body(__('service-fee.percentage_max'))
                ->persistent()
                ->send();

            $this->halt();
        }

        // Value must be positive
        if (($data['fee_value'] ?? 0) < 0) {
            Notification::make()
                ->danger()
                ->title(__('service-fee.invalid_value'))
                ->body(__('service-fee.value_positive'))
                ->persistent()
                ->send();

            $this->halt();
        }

        // Date range must be valid
        if (isset($data['effective_from'], $data['effective_to'])) {
            if ($data['effective_from'] > $data['effective_to']) {
                Notification::make()
                    ->danger()
                    ->title(__('service-fee.invalid_dates'))
                    ->body(__('service-fee.date_range_error'))
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

        return $data;
    }

    /**
     * Log update with old/new values for audit trail.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $oldValues = $record->only(['fee_type', 'fee_value', 'is_active', 'hall_id', 'owner_id']);

        $record->update($data);

        // Activity log
        activity()
            ->performedOn($record)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'new' => $data,
            ])
            ->log('Service fee setting updated');

        return $record;
    }

    /**
     * Post-save: cache clear + logging.
     */
    protected function afterSave(): void
    {
        Cache::forget('service_fees');

        Log::info('Service fee setting updated', [
            'fee_id'    => $this->record->id,
            'scope'     => $this->getScopeType(),
            'admin'     => Auth::id(),
        ]);
    }

    protected function getScopeType(): string
    {
        if ($this->record->hall_id) {
            return 'Hall-Specific';
        } elseif ($this->record->owner_id) {
            return 'Owner-Specific';
        }
        return 'Global';
    }
}
