<?php

namespace App\Filament\Admin\Resources\CommissionSettingResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Actions\DeleteAction;
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
            Action::make('toggleActive')
                ->label(fn() => $this->record->is_active ? __('Deactivate') : __('Activate'))
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn() => $this->record->is_active ? 'warning' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn() => $this->record->is_active ? __('Deactivate Commission') : __('Activate Commission'))
                ->modalDescription(fn() => $this->record->is_active
                    ? __('This will deactivate the commission setting. Existing bookings will not be affected.')
                    : __('This will activate the commission setting and apply it to new bookings.'))
                ->action(function () {
                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();

                    Notification::make()
                        ->title(__('Status Updated'))
                        ->body(__('Commission setting status has been updated successfully.'))
                        ->success()
                        ->send();

                    // Clear cache
                    //Cache::tags(['commissions'])->flush();

                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Action::make('viewBookings')
                ->label(__('View Affected Bookings'))
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->visible(fn() => $this->record->hall_id !== null)
                ->url(fn() => route('filament.admin.resources.bookings.index', [
                    'tableFilters' => [
                        'hall_id' => ['value' => $this->record->hall_id]
                    ]
                ])),

            Action::make('calculateImpact')
                ->label(__('Calculate Financial Impact'))
                ->icon('heroicon-o-calculator')
                ->color('warning')
                ->action(function () {
                    $impact = $this->calculateCommissionImpact();

                    Notification::make()
                        ->title(__('Financial Impact Analysis'))
                        ->body(__('Total commission earned: :total OMR\nBookings affected: :count', ['total' => $impact['total'], 'count' => $impact['count']]))
                        ->info()
                        ->persistent()
                        ->actions([
                            Action::make('viewReport')
                                ->label(__('View Full Report'))
                                ->url('#'),
                        ])
                        ->send();
                }),

            Action::make('extendValidity')
                ->label(__('Extend Validity'))
                ->icon('heroicon-o-calendar-days')
                ->color('success')
                ->visible(fn() => $this->record->effective_to !== null)
                ->schema([
                    DatePicker::make('new_effective_to')
                        ->label(__('New End Date'))
                        ->native(false)
                        ->minDate(fn() => $this->record->effective_to ?? now())
                        ->required(),

                    Textarea::make('reason')
                        ->label(__('Reason for Extension'))
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
                            'reason' => $data['reason'] ?? __('No reason provided'),
                        ])
                        ->log('Commission validity extended');

                    Notification::make()
                        ->success()
                        ->title(__('Validity Extended'))
                        ->body(__('Commission setting validity has been extended.'))
                        ->send();
                }),

            Action::make('duplicate')
                ->label(__('Duplicate'))
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading(__('Duplicate Commission Setting'))
                ->modalDescription(__('This will create a copy of this commission setting.'))
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
                        ->title(__('Commission Setting Duplicated'))
                        ->body(__('The commission setting has been duplicated successfully.'))
                        ->actions([
                            Action::make('view')
                                ->label(__('Edit Duplicate'))
                                ->url(CommissionSettingResource::getUrl('edit', ['record' => $newCommission->id])),
                        ])
                        ->send();
                }),

            DeleteAction::make()
                ->before(function (DeleteAction $action) {
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
                        ->title(__('Commission Setting Deleted'))
                        ->body(__('The commission setting has been deleted successfully.'))
                )
                ->after(function () {
                    // Clear cache
                    //Cache::tags(['commissions'])->flush();
                }),

            Action::make('viewHistory')
                ->label(__('View History'))
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->modalContent(fn() => view('filament.pages.activity-log', [
                    'activities' => \Spatie\Activitylog\Models\Activity::forSubject($this->record)
                        ->latest()
                        ->get()
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel(__('Close')),
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
            ->title(__('Commission Setting Updated'))
            ->body(__('The commission setting has been updated successfully.'))
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
                ->title(__('Scope Adjusted'))
                ->body(__('Both hall and owner were selected. Hall-specific commission will be saved.'))
                ->send();

            unset($data['owner_id']);
        }

        // Validate commission value
        if ($data['commission_type'] === 'percentage' && $data['commission_value'] > 100) {
            Notification::make()
                ->danger()
                ->title(__('Invalid Commission Value'))
                ->body(__('Percentage commission cannot exceed 100%.'))
                ->persistent()
                ->send();

            $this->halt();
        }

        if ($data['commission_value'] < 0) {
            Notification::make()
                ->danger()
                ->title(__('Invalid Commission Value'))
                ->body(__('Commission value cannot be negative.'))
                ->persistent()
                ->send();

            $this->halt();
        }

        // Validate date range
        if (isset($data['effective_from']) && isset($data['effective_to'])) {
            if ($data['effective_from'] > $data['effective_to']) {
                Notification::make()
                    ->danger()
                    ->title(__('Invalid Date Range'))
                    ->body(__('Effective from date must be before effective to date.'))
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

        $normalize = fn(array $arr) => array_map(
            fn($v) => is_array($v) ? json_encode($v) : $v,
            $arr
        );
        $changes = array_diff_assoc($normalize($data), $normalize($oldValues));

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
        //Cache::tags(['commissions'])->flush();

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
            return __('Hall-Specific');
        } elseif ($this->record->owner_id) {
            return __('Owner-Specific');
        }

        return __('Global');
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
                //->submit(null)
                ->keyBindings(['mod+s']),

            $this->getCancelFormAction(),
        ];
    }

    public function getTitle(): string
    {
        $scopeType = $this->getScopeType();
        return __('Edit :scopeType Commission', ['scopeType' => $scopeType]);
    }

    public function getSubheading(): ?string
    {
        $type = ucfirst($this->record->commission_type->value ?? $this->record->commission_type);
        $value = $this->record->commission_type->value === 'percentage'
            ? $this->record->commission_value . '%'
            : number_format($this->record->commission_value, 3) . ' OMR';
        $status = $this->record->is_active ? __('Active') : __('Inactive');

        return "{$status} • {$type}: {$value}";
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
