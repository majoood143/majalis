<?php

namespace App\Filament\Admin\Resources\CityResource\Pages;

use App\Filament\Admin\Resources\CityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EditCity extends EditRecord
{
    protected static string $resource = CityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewHalls')
                ->label('View Halls')
                ->icon('heroicon-o-building-storefront')
                ->color('info')
                ->url(fn() => route('filament.admin.resources.halls.index', [
                    'tableFilters' => [
                        'city_id' => ['value' => $this->record->id]
                    ]
                ]))
                ->visible(fn() => $this->record->halls()->count() > 0),

            Actions\Action::make('toggleActive')
                ->label(fn() => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn() => $this->record->is_active ? 'warning' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn() => $this->record->is_active ? 'Deactivate City' : 'Activate City')
                ->modalDescription(fn() => $this->record->is_active
                    ? 'Are you sure you want to deactivate this city? It will no longer be available for selection.'
                    : 'Are you sure you want to activate this city?')
                ->action(function () {
                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();

                    Notification::make()
                        ->title('Status Updated')
                        ->body('City status has been updated successfully.')
                        ->success()
                        ->send();
                }),

            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
                    // Check if city has halls
                    if ($this->record->halls()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot Delete City')
                            ->body('This city has ' . $this->record->halls()->count() . ' hall(s) associated with it. Please remove or reassign the halls first.')
                            ->persistent()
                            ->send();

                        $action->cancel();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('City Deleted')
                        ->body('The city has been deleted successfully.')
                ),

            Actions\Action::make('duplicate')
                ->label('Duplicate')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Duplicate City')
                ->modalDescription('This will create a copy of this city with a new code.')
                ->action(function () {
                    $newCity = $this->record->replicate();
                    $newCity->code = $this->record->code . '_COPY';
                    $newCity->is_active = false;
                    $newCity->save();

                    Notification::make()
                        ->success()
                        ->title('City Duplicated')
                        ->body('The city has been duplicated successfully.')
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label('View Duplicate')
                                ->url(CityResource::getUrl('edit', ['record' => $newCity->id])),
                        ])
                        ->send();
                }),

            Actions\Action::make('viewTimeline')
                ->label('Activity Log')
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
            ->title('City Updated')
            ->body('The city has been updated successfully.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // You can transform data before filling the form
        // For example: format dates, decode JSON, etc.

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure name and description are properly formatted
        if (isset($data['name']) && is_array($data['name'])) {
            $data['name'] = $data['name'];
        }

        if (isset($data['description']) && is_array($data['description'])) {
            $data['description'] = $data['description'];
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Store old values for comparison
        $oldValues = $record->toArray();

        // Update the record
        $record->update($data);

        // Log the update with changes
        $changes = array_diff_assoc($data, $oldValues);

        activity()
            ->performedOn($record)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'changes' => $changes,
            ])
            ->log('City updated');

        return $record;
    }

    protected function afterSave(): void
    {
        // Clear cache if you're using caching
        Cache::tags(['cities'])->flush();

        // Log the update
        Log::info('City updated', [
            'city_id' => $this->record->id,
            'name' => $this->record->name,
            'updated_by' => Auth::id(),
        ]);
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
        return 'Edit City: ' . $this->record->name;
    }

    public function getSubheading(): ?string
    {
        $hallsCount = $this->record->halls()->count();
        $status = $this->record->is_active ? 'Active' : 'Inactive';
    
        return "{$status} • {$hallsCount} Hall(s) • Code: {$this->record->code}";
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
