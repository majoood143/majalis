<?php

namespace App\Filament\Admin\Resources\CommissionSettingResource\Pages;

use App\Filament\Admin\Resources\CommissionSettingResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CreateCommissionSetting extends CreateRecord
{
    protected static string $resource = CommissionSettingResource::class;

    protected static bool $canCreateAnother = true;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        $scopeType = $this->getScopeType($this->record);

        return Notification::make()
            ->success()
            ->title('Commission Setting Created')
            ->body("A new {$scopeType} commission setting has been created successfully.")
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values
        $data['is_active'] = $data['is_active'] ?? true;

        // Validate scope priority
        if (isset($data['hall_id']) && isset($data['owner_id'])) {
            // If both are set, prioritize hall and clear owner
            Notification::make()
                ->warning()
                ->title('Scope Adjusted')
                ->body('Both hall and owner were selected. Hall-specific commission will be created.')
                ->send();

            unset($data['owner_id']);
        }

        // Validate commission value based on type
        if ($data['commission_type'] === 'percentage' && $data['commission_value'] > 100) {
            Notification::make()
                ->danger()
                ->title('Invalid Commission Value')
                ->body('Percentage commission cannot exceed 100%.')
                ->persistent()
                ->send();

            $this->halt();
        }

        if ($data['commission_value'] < 0) {
            Notification::make()
                ->danger()
                ->title('Invalid Commission Value')
                ->body('Commission value cannot be negative.')
                ->persistent()
                ->send();

            $this->halt();
        }

        // Validate date range
        if (isset($data['effective_from']) && isset($data['effective_to'])) {
            if ($data['effective_from'] > $data['effective_to']) {
                Notification::make()
                    ->danger()
                    ->title('Invalid Date Range')
                    ->body('Effective from date must be before effective to date.')
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

        // Check for existing active commission with same scope
        $this->validateUniqueScope($data);

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);

        // Log the creation
        activity()
            ->performedOn($record)
            ->causedBy(Auth::user())
            ->withProperties([
                'scope_type' => $this->getScopeType($record),
                'commission_type' => $data['commission_type'],
                'commission_value' => $data['commission_value'],
            ])
            ->log('Commission setting created');

        return $record;
    }

    protected function afterCreate(): void
    {
        $scopeType = $this->getScopeType($this->record);

        // Log the creation
        Log::info('Commission setting created', [
            'commission_id' => $this->record->id,
            'scope_type' => $scopeType,
            'commission_type' => $this->record->commission_type,
            'commission_value' => $this->record->commission_value,
            'created_by' => Auth::id(),
        ]);

        // Clear commission cache
        Cache::tags(['commissions'])->flush();

        // Notify relevant parties if needed
        if ($this->record->owner_id) {
            $this->notifyOwner();
        }

        if ($this->record->hall_id) {
            $this->notifyHallOwner();
        }
    }

    protected function validateUniqueScope(array $data): void
    {
        $query = \App\Models\CommissionSetting::where('is_active', true);

        if (isset($data['hall_id'])) {
            $query->where('hall_id', $data['hall_id']);
        } elseif (isset($data['owner_id'])) {
            $query->where('owner_id', $data['owner_id'])
                ->whereNull('hall_id');
        } else {
            $query->whereNull('hall_id')->whereNull('owner_id');
        }

        // Check for overlapping date ranges
        if (isset($data['effective_from']) || isset($data['effective_to'])) {
            $query->where(function ($q) use ($data) {
                if (isset($data['effective_from'])) {
                    $q->where(function ($subQ) use ($data) {
                        $subQ->whereNull('effective_to')
                            ->orWhere('effective_to', '>=', $data['effective_from']);
                    });
                }

                if (isset($data['effective_to'])) {
                    $q->where(function ($subQ) use ($data) {
                        $subQ->whereNull('effective_from')
                            ->orWhere('effective_from', '<=', $data['effective_to']);
                    });
                }
            });
        }

        if ($query->exists()) {
            $existingCount = $query->count();

            Notification::make()
                ->warning()
                ->title('Existing Commission Found')
                ->body("There are {$existingCount} active commission setting(s) with overlapping scope and date range. This may cause conflicts.")
                ->persistent()
                ->send();
        }
    }

    protected function getScopeType(Model $record): string
    {
        if ($record->hall_id) {
            return 'Hall-Specific';
        } elseif ($record->owner_id) {
            return 'Owner-Specific';
        }

        return 'Global';
    }

    protected function notifyOwner(): void
    {
        if ($this->record->owner) {
            // Send notification to owner about new commission setting
            // This assumes you have a notification system set up
            // $this->record->owner->notify(new CommissionSettingCreated($this->record));
        }
    }

    protected function notifyHallOwner(): void
    {
        if ($this->record->hall && $this->record->hall->owner) {
            // Send notification to hall owner about new commission setting
            // $this->record->hall->owner->notify(new CommissionSettingCreated($this->record));
        }
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->submit(null)
                ->keyBindings(['mod+s']),

            $this->getCreateAnotherFormAction()
                ->keyBindings(['mod+shift+s']),

            $this->getCancelFormAction(),
        ];
    }

    public function getTitle(): string
    {
        return 'Create Commission Setting';
    }

    public function getSubheading(): ?string
    {
        return 'Configure commission rates for halls, owners, or globally';
    }
}
