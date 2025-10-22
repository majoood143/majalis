<?php

namespace App\Filament\Admin\Resources\CommissionSettingResource\Pages;

use App\Filament\Admin\Resources\CommissionSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EditCommissionSetting extends EditRecord
{
    protected static string $resource = CommissionSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('toggleActive')
                ->label(fn() => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn() => $this->record->is_active ? 'warning' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn() => $this->record->is_active ? 'Deactivate Commission' : 'Activate Commission')
                ->modalDescription(fn() => $this->record->is_active
                    ? 'This will deactivate the commission setting. Existing bookings will not be affected.'
                    : 'This will activate the commission setting and apply it to new bookings.')
                ->action(function () {
                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();

                    Notification::make()
                        ->title('Status Updated')
                        ->body('Commission setting status has been updated successfully.')
                        ->success()
                        ->send();

                    // Clear cache
                    Cache::tags(['commissions'])->flush();

                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('viewBookings')
                ->label('View Affected Bookings')
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->visible(fn() => $this->record->hall_id !== null)
                ->url(fn() => route('filament.admin.resources.bookings.index', [
                    'tableFilters' => [
                        'hall_id' => ['value' => $this->record->hall_id]
                    ]
                ])),

            Actions\Action::make('calculateImpact')
                ->label('Calculate Financial Impact')
                ->icon('heroicon-o-calculator')
                ->color('warning')
                ->action(function () {
                    $impact = $this->calculateCommissionImpact();

                    Notification::make()
                        ->title('Financial Impact Analysis')
                        ->body("Total commission earned: {$impact['total']} OMR\nBookings affected: {$impact['count']}")
                        ->info()
                        ->persistent()
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('viewReport')
                                ->label('View Full Report')
                                ->url('#'),
                        ])
                        ->send();
                }),

            Actions\Action::make('extendValidity')
                ->label('Extend Validity')
                ->icon('heroicon-o-calendar-days')
                ->color('success')
                ->visible(fn() => $this->record->effective_to !== null)
                ->form([
                    \Filament\Forms\Components\DatePicker::make('new_effective_to')
                        ->label('New End Date')
                        ->native(false)
                        ->minDate(fn() => $this->record->effective_to ?? now())
                        ->required(),

                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Reason for Extension')
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $oldDate = $this->record->effective_to;
                    $this->record->effective_to = $data['new_effective_to'];
                    $this->record->save();

                    // Log the extension
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(Auth::user())
                        ->withProperties([
                            'old_date' => $oldDate,
                            'new_date' => $data['new_effective_to'],
                            'reason' => $data['reason'] ?? 'No reason provided',
                        ])
                        ->log('Commission validity extended');

                    Notification::make()
                        ->success()
                        ->title('Validity Extended')
                        ->body('Commission setting validity has been extended.')
                        ->send();
                }),

            Actions\Action::make('duplicate')
                ->label('Duplicate')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Duplicate Commission Setting')
                ->modalDescription('This will create a copy of this commission setting.')
                ->action(function () {
                    $newCommission = $this->record->replicate();
                    $newCommission->is_active = false;

                    // Update name if exists
                    if ($newCommission->name) {
                        $name = $newCommission->getTranslations('name');
                        foreach ($name as $locale => $value) {
                            $name[$locale] = $value . ' (Copy)';
                        }
                        $newCommission->setTranslations('name', $name);
                    }

                    $newCommission->save();

                    Notification::make()
                        ->success()
                        ->title('Commission Setting Duplicated')
                        ->body('The commission setting has been duplicated successfully.')
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label('Edit Duplicate')
                                ->url(CommissionSettingResource::getUrl('edit', ['record' => $newCommission->id])),
                        ])
                        ->send();
                }),

            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
                    // Check if commission has been applied to any bookings
                    // This assumes you have a way to track which commission was applied
                    // You may need to adjust this based on your actual implementation

                    // if ($this->record->bookings()->exists()) {
                    //     Notification::make()
                    //         ->danger()
                    //         ->title('Cannot Delete Commission')
                    //         ->body('This commission setting has been applied to existing bookings.')
                    //         ->persistent()
                    //         ->send();
                    //     
                    //     $action->cancel();
                    // }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Commission Setting Deleted')
                        ->body('The commission setting has been deleted successfully.')
                )
                ->after(function () {
                    // Clear cache
                    Cache::tags(['commissions'])->flush();
                }),

            Actions\Action::make('viewHistory')
                ->label('View History')
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->modalContent(fn() => view('filament.pages.activity-log', [
                    'activities' => activity()
                        ->forSubject($this->record)
                        ->latest()
                        ->get()
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Commission Setting Updated')
            ->body('The commission setting has been updated successfully.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validate scope priority
        if (isset($data['hall_id']) && isset($data['owner_id'])) {
            Notification::make()
                ->warning()
                ->title('Scope Adjusted')
                ->body('Both hall and owner were selected. Hall-specific commission will be saved.')
                ->send();

            unset($data['owner_id']);
        }

        // Validate commission value
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

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $oldValues = $record->toArray();

        $record->update($data);

        $changes = array_diff_assoc($data, $oldValues);

        // Log the update
        activity()
            ->performedOn($record)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'changes' => $changes,
            ])
            ->log('Commission setting updated');

        return $record;
    }

    protected function afterSave(): void
    {
        // Clear cache
        Cache::tags(['commissions'])->flush();

        // Log the update
        Log::info('Commission setting updated', [
            'commission_id' => $this->record->id,
            'scope_type' => $this->getScopeType(),
            'updated_by' => Auth::id(),
        ]);

        // Notify affected parties if commission value changed significantly
        if ($this->hasSignificantChange()) {
            $this->notifyAffectedParties();
        }
    }

    protected function calculateCommissionImpact(): array
    {
        // This is a placeholder - implement based on your actual booking/payment structure
        // You'll need to query bookings that would be affected by this commission

        return [
            'total' => 0,
            'count' => 0,
        ];
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

    protected function hasSignificantChange(): bool
    {
        // Check if commission value changed by more than 10%
        $original = $this->record->getOriginal('commission_value');
        $current = $this->record->commission_value;

        if ($original && $current) {
            $changePercent = abs(($current - $original) / $original * 100);
            return $changePercent > 10;
        }

        return false;
    }

    protected function notifyAffectedParties(): void
    {
        // Send notifications to hall owners or owners about commission changes
        if ($this->record->owner_id && $this->record->owner) {
            // $this->record->owner->notify(new CommissionSettingUpdated($this->record));
        }

        if ($this->record->hall_id && $this->record->hall && $this->record->hall->owner) {
            // $this->record->hall->owner->notify(new CommissionSettingUpdated($this->record));
        }
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->submit(null)
                ->keyBindings(['mod+s']),

            $this->getCancelFormAction(),
        ];
    }

    public function getTitle(): string
    {
        $scopeType = $this->getScopeType();
        return "Edit {$scopeType} Commission";
    }

    public function getSubheading(): ?string
    {
        $type = ucfirst($this->record->commission_type->value ?? $this->record->commission_type);
        $value = $this->record->commission_type->value === 'percentage'
            ? $this->record->commission_value . '%'
            : number_format($this->record->commission_value, 3) . ' OMR';
        $status = $this->record->is_active ? 'Active' : 'Inactive';

        return "{$status} • {$type}: {$value}";
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
