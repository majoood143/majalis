<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceFeeSettingResource\Pages;

use App\Filament\Admin\Resources\ServiceFeeSettingResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Create page for Service Fee Settings.
 *
 * Includes validation for:
 *   - Percentage max 100%
 *   - Positive values only
 *   - Date range validity
 *   - Overlapping scope conflicts (warning only)
 */
class CreateServiceFeeSetting extends CreateRecord
{
    protected static string $resource = ServiceFeeSettingResource::class;

    protected static bool $canCreateAnother = true;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Success notification with scope info.
     */
    protected function getCreatedNotification(): ?Notification
    {
        $scopeType = $this->getScopeType($this->record);

        return Notification::make()
            ->success()
            ->title(__('service-fee.created'))
            ->body(__('service-fee.created_body', ['scope' => $scopeType]))
            ->duration(5000);
    }

    /**
     * Validate and clean form data before creating the record.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Default to active
        $data['is_active'] = $data['is_active'] ?? true;

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
     * Post-creation: log + cache clear + overlap warning.
     */
    protected function afterCreate(): void
    {
        // Clear any cached fee lookups
        Cache::forget('service_fees');

        // Log creation
        Log::info('Service fee setting created', [
            'fee_id'    => $this->record->id,
            'scope'     => $this->getScopeType($this->record),
            'fee_type'  => $this->record->fee_type->value ?? $this->record->fee_type,
            'fee_value' => $this->record->fee_value,
            'admin'     => Auth::id(),
        ]);

        // Warn about overlapping fees
        $this->checkForOverlaps($this->record);
    }

    /**
     * Check if there are conflicting fee settings with overlapping scope/dates.
     */
    protected function checkForOverlaps(Model $record): void
    {
        $query = \App\Models\ServiceFeeSetting::where('is_active', true)
            ->where('id', '!=', $record->id);

        // Match scope
        if ($record->hall_id) {
            $query->where('hall_id', $record->hall_id);
        } elseif ($record->owner_id) {
            $query->where('owner_id', $record->owner_id)->whereNull('hall_id');
        } else {
            $query->whereNull('hall_id')->whereNull('owner_id');
        }

        if ($query->exists()) {
            Notification::make()
                ->warning()
                ->title(__('service-fee.overlap_warning'))
                ->body(__('service-fee.overlap_warning_body', ['count' => $query->count()]))
                ->persistent()
                ->send();
        }
    }

    /**
     * Determine scope type label for logging/notifications.
     */
    protected function getScopeType(Model $record): string
    {
        if ($record->hall_id) {
            return 'Hall-Specific';
        } elseif ($record->owner_id) {
            return 'Owner-Specific';
        }
        return 'Global';
    }

    public function getTitle(): string
    {
        return __('service-fee.create');
    }

    public function getSubheading(): ?string
    {
        return __('service-fee.subheading');
    }
}
