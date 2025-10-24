<?php

namespace App\Filament\Admin\Resources\HallResource\Pages;

use App\Filament\Admin\Resources\HallResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EditHall extends EditRecord
{
    protected static string $resource = HallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->icon('heroicon-o-eye')
                ->color('info'),

            Actions\Action::make('toggleActive')
                ->label(fn() => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn() => $this->record->is_active ? 'danger' : 'success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();

                    Notification::make()
                        ->success()
                        ->title('Status Updated')
                        ->send();

                    Cache::tags(['halls', 'city_' . $this->record->city_id])->flush();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('toggleFeatured')
                ->label(fn() => $this->record->is_featured ? 'Unmark Featured' : 'Mark Featured')
                ->icon('heroicon-o-star')
                ->color(fn() => $this->record->is_featured ? 'gray' : 'warning')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->is_featured = !$this->record->is_featured;
                    $this->record->save();

                    Notification::make()
                        ->success()
                        ->title('Featured Status Updated')
                        ->send();

                    Cache::tags(['halls'])->flush();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('viewBookings')
                ->label('View Bookings')
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->badge(fn() => $this->record->bookings()->count())
                ->url(fn() => route('filament.admin.resources.bookings.index', [
                    'tableFilters' => ['hall_id' => ['value' => $this->record->id]]
                ])),

            Actions\Action::make('manageAvailability')
                ->label('Manage Availability')
                ->icon('heroicon-o-calendar')
                ->color('purple')
                ->url(fn() => route('filament.admin.resources.hall-availabilities.index', [
                    'tableFilters' => ['hall_id' => ['value' => $this->record->id]]
                ])),

            Actions\Action::make('manageImages')
                ->label('Manage Images')
                ->icon('heroicon-o-photo')
                ->color('info')
                ->url(fn() => route('filament.admin.resources.hall-images.index', [
                    'tableFilters' => ['hall_id' => ['value' => $this->record->id]]
                ])),

            Actions\Action::make('manageServices')
                ->label('Extra Services')
                ->icon('heroicon-o-gift')
                ->color('success')
                ->url(fn() => route('filament.admin.resources.extra-services.index', [
                    'tableFilters' => ['hall_id' => ['value' => $this->record->id]]
                ])),

            Actions\Action::make('updatePricing')
                ->label('Update Pricing')
                ->icon('heroicon-o-currency-dollar')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\TextInput::make('new_price')
                        ->label('New Base Price')
                        ->numeric()
                        ->required()
                        ->prefix('OMR')
                        ->step(0.001)
                        ->default(fn() => $this->record->price_per_slot),

                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Reason for Change')
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $oldPrice = $this->record->price_per_slot;
                    $this->record->price_per_slot = $data['new_price'];
                    $this->record->save();

                    activity()
                        ->performedOn($this->record)
                        ->causedBy(Auth::user())
                        ->withProperties([
                            'old_price' => $oldPrice,
                            'new_price' => $data['new_price'],
                            'reason' => $data['reason'] ?? 'No reason provided',
                        ])
                        ->log('Hall price updated');

                    Notification::make()
                        ->success()
                        ->title('Price Updated')
                        ->body("Price changed from {$oldPrice} to {$data['new_price']} OMR")
                        ->send();
                }),

            Actions\Action::make('regenerateSlug')
                ->label('Regenerate Slug')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function () {
                    $oldSlug = $this->record->slug;
                    $newSlug = $this->generateUniqueSlug($this->record->getTranslation('name', 'en'));

                    $this->record->slug = $newSlug;
                    $this->record->save();

                    Notification::make()
                        ->success()
                        ->title('Slug Regenerated')
                        ->body("Updated from '{$oldSlug}' to '{$newSlug}'")
                        ->send();
                }),

            Actions\Action::make('duplicate')
                ->label('Duplicate Hall')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function () {
                    $newHall = $this->record->replicate();

                    // Update name
                    $name = $newHall->getTranslations('name');
                    foreach ($name as $locale => $value) {
                        $name[$locale] = $value . ' (Copy)';
                    }
                    $newHall->setTranslations('name', $name);

                    $newHall->slug = $this->generateUniqueSlug($newHall->getTranslation('name', 'en'));
                    $newHall->is_active = false;
                    $newHall->is_featured = false;
                    $newHall->save();

                    // Copy features
                    $newHall->features()->sync($this->record->features->pluck('id'));

                    Notification::make()
                        ->success()
                        ->title('Hall Duplicated')
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label('View Duplicate')
                                ->url(HallResource::getUrl('edit', ['record' => $newHall->id])),
                        ])
                        ->send();
                }),

            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
                    // Check for active bookings
                    $activeBookings = $this->record->bookings()
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->count();

                    if ($activeBookings > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot Delete Hall')
                            ->body("This hall has {$activeBookings} active booking(s).")
                            ->persistent()
                            ->send();

                        $action->cancel();
                    }
                })
                ->after(function () {
                    // Delete images
                    if ($this->record->featured_image) {
                        Storage::disk('public')->delete($this->record->featured_image);
                    }

                    if ($this->record->gallery) {
                        foreach ($this->record->gallery as $image) {
                            Storage::disk('public')->delete($image);
                        }
                    }

                    Cache::tags(['halls', 'city_' . $this->record->city_id])->flush();
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
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Hall Updated')
            ->body('The hall has been updated successfully.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load features relationship
        //$data['features'] = $this->record->features->pluck('id')->toArray();

        $data['features'] = collect($this->record->features)->pluck('id')->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Auto-generate slug if empty
        if (empty($data['slug']) && isset($data['name']['en'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name']['en'], $this->record->id);
        }

        // Validate capacity range
        if ($data['capacity_min'] > $data['capacity_max']) {
            Notification::make()
                ->danger()
                ->title('Invalid Capacity Range')
                ->body('Minimum capacity cannot exceed maximum capacity.')
                ->persistent()
                ->send();

            $this->halt();
        }

        // Validate pricing
        if ($data['price_per_slot'] < 0) {
            Notification::make()
                ->danger()
                ->title('Invalid Price')
                ->body('Price cannot be negative.')
                ->persistent()
                ->send();

            $this->halt();
        }

        // Clean phone numbers
        if (isset($data['phone'])) {
            $data['phone'] = preg_replace('/[^0-9+]/', '', $data['phone']);
        }

        if (isset($data['whatsapp'])) {
            $data['whatsapp'] = preg_replace('/[^0-9+]/', '', $data['whatsapp']);
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $oldValues = $record->toArray();

        // Separate features for pivot table
        $features = $data['features'] ?? [];
        unset($data['features']);

        $record->update($data);

        // Sync features
        //$record->features()->sync($features);

        //$changes = array_diff_assoc($data, $oldValues);

        // Log the update
        // activity()
        //     ->performedOn($record)
        //     ->causedBy(Auth::user())
        //     ->withProperties([
        //         'old' => $oldValues,
        //         'changes' => $changes,
        //     ])
        //     ->log('Hall updated');

        return $record;
    }

    protected function afterSave(): void
    {
        // Clear cache for both old and new city
        $oldCityId = $this->record->getOriginal('city_id');
        $newCityId = $this->record->city_id;

        //Cache::tags(['halls'])->flush();

        // if ($oldCityId) {
        //     Cache::tags(['city_' . $oldCityId])->flush();
        // }

        // if ($newCityId && $newCityId !== $oldCityId) {
        //     Cache::tags(['city_' . $newCityId])->flush();
        // }

        // Log the update
            Log::info('Hall updated', [
            'hall_id' => $this->record->id,
            'name' => $this->record->name,
            'updated_by' => Auth::id(),
        ]);
    }

    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $baseSlug = $slug;
        $counter = 1;

        $query = \App\Models\Hall::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;

            $query = \App\Models\Hall::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }

        return $slug;
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
        return 'Edit Hall: ' . $this->record->name;
    }

    public function getSubheading(): ?string
    {
        $city = $this->record->city->name ?? 'Unknown City';
        $status = $this->record->is_active ? 'Active' : 'Inactive';
        $featured = $this->record->is_featured ? '• Featured' : '';
        $capacity = $this->record->capacity_max . ' guests';

        return "{$city} • {$status} {$featured} • {$capacity}";
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
