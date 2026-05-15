<?php

namespace App\Filament\Admin\Resources\HallResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Actions\DeleteAction;
use App\Enums\UserRole;
use App\Models\Hall;
use App\Filament\Admin\Resources\HallResource;
use App\Models\User;
use App\Services\ImageOptimizationService;
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
            ViewAction::make()
                ->icon('heroicon-o-eye')
                ->color('info'),

            ActionGroup::make([
                Action::make('toggleActive')
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

                Action::make('toggleFeatured')
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
            ])
                ->label('Status')
                ->icon('heroicon-o-adjustments-horizontal')
                ->color('primary')
                ->button(),

            ActionGroup::make([
                Action::make('viewBookings')
                    ->label('View Bookings')
                    ->icon('heroicon-o-calendar-days')
                    ->color('info')
                    ->badge(fn() => $this->record->bookings()->count())
                    ->url(fn() => route('filament.admin.resources.bookings.index', [
                        'tableFilters' => ['hall_id' => ['value' => $this->record->id]]
                    ])),

                Action::make('manageAvailability')
                    ->label('Manage Availability')
                    ->icon('heroicon-o-calendar')
                    ->color('info')
                    ->url(fn() => route('filament.admin.resources.hall-availabilities.index', [
                        'tableFilters' => ['hall_id' => ['value' => $this->record->id]]
                    ])),

                Action::make('manageImages')
                    ->label('Manage Images')
                    ->icon('heroicon-o-photo')
                    ->color('info')
                    ->url(fn() => route('filament.admin.resources.hall-images.index', [
                        'tableFilters' => ['hall_id' => ['value' => $this->record->id]]
                    ])),

                Action::make('manageServices')
                    ->label('Extra Services')
                    ->icon('heroicon-o-gift')
                    ->color('success')
                    ->url(fn() => route('filament.admin.resources.extra-services.index', [
                        'tableFilters' => ['hall_id' => ['value' => $this->record->id]]
                    ])),
            ])
                ->label('Manage')
                ->icon('heroicon-o-squares-2x2')
                ->color('info')
                ->button(),

            ActionGroup::make([
                Action::make('updatePricing')
                    ->label('Update Pricing')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('warning')
                    ->schema([
                        TextInput::make('new_price')
                            ->label('New Base Price')
                            ->numeric()
                            ->required()
                            ->prefix('OMR')
                            ->step(0.001)
                            ->default(fn() => $this->record->price_per_slot),

                        Textarea::make('reason')
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

                Action::make('duplicate')
                    ->label('Duplicate Hall')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function () {
                        $newHall = $this->record->replicate();

                        $name = $newHall->getTranslations('name');
                        foreach ($name as $locale => $value) {
                            $name[$locale] = $value . ' (Copy)';
                        }
                        $newHall->setTranslations('name', $name);

                        $newHall->slug = $this->generateUniqueSlug($newHall->getTranslation('name', 'en'));
                        $newHall->is_active = false;
                        $newHall->is_featured = false;
                        $newHall->save();

                        $newHall->features = $this->record->features;
                        $newHall->save();

                        Notification::make()
                            ->success()
                            ->title('Hall Duplicated')
                            ->actions([
                                Action::make('view')
                                    ->label('View Duplicate')
                                    ->url(HallResource::getUrl('edit', ['record' => $newHall->id])),
                            ])
                            ->send();
                    }),

                Action::make('regenerateSlug')
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

                Action::make('viewHistory')
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
            ])
                ->label('More')
                ->icon('heroicon-o-ellipsis-horizontal')
                ->color('gray')
                ->button(),

            DeleteAction::make()
                ->before(function (DeleteAction $action) {
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
                    if ($this->record->featured_image) {
                        Storage::disk('public')->delete($this->record->featured_image);
                    }

                    if ($this->record->gallery) {
                        foreach ($this->record->gallery as $image) {
                            Storage::disk('public')->delete($image);
                        }
                    }

                    Cache::tags(['halls', 'city_' . $this->record->city_id])->flush();

                    $this->notifyAdmins($this->record, 'deleted');
                }),
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
        // Features is already an array of IDs from the JSON column
        $data['features'] = $this->record->features ?? [];

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
        // Just update normally - features will be saved as JSON
        $record->update($data);

        // Log the update
        activity()
            ->performedOn($record)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $record->getOriginal(),
                'changes' => $data,
            ])
            ->log('Hall updated');

        return $record;
    }

    protected function afterSave(): void
    {
        $optimizer = app(ImageOptimizationService::class);

        // Compress featured_image only if it changed
        $oldFeatured = $this->record->getOriginal('featured_image');
        $newFeatured = $this->record->featured_image;
        if (!empty($newFeatured) && $newFeatured !== $oldFeatured) {
            $optimizer->compress($newFeatured);
        }

        // Compress any newly added gallery images
        $oldGallery = (array) ($this->record->getOriginal('gallery') ?? []);
        $newGallery = (array) ($this->record->gallery ?? []);
        $added      = array_diff($newGallery, $oldGallery);
        if (!empty($added)) {
            $optimizer->compressMany(array_values($added));
        }

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

        // Notify all admins via database
        $this->notifyAdmins($this->record, 'updated');
    }

    protected function notifyAdmins($hall, string $event): void
    {
        $hallName = $hall->getTranslation('name', 'en', false) ?: $hall->name;
        $actor    = Auth::user()->name;

        $titles = [
            'updated' => "Hall Updated: {$hallName}",
            'deleted' => "Hall Deleted: {$hallName}",
        ];

        $bodies = [
            'updated' => "Updated by {$actor}",
            'deleted' => "Deleted by {$actor}",
        ];

        $admins = User::where('role', UserRole::ADMIN)->get();

        $notification = Notification::make()
            ->title($titles[$event])
            ->body($bodies[$event]);

        match ($event) {
            'deleted' => $notification->danger(),
            'updated' => $notification->warning(),
            default   => $notification->success(),
        };

        $notification->sendToDatabase($admins);
    }

    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $baseSlug = $slug;
        $counter = 1;

        $query = Hall::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;

            $query = Hall::where('slug', $slug);
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
