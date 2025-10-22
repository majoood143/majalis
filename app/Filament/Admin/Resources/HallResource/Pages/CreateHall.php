<?php

namespace App\Filament\Admin\Resources\HallResource\Pages;

use App\Filament\Admin\Resources\HallResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CreateHall extends CreateRecord
{
    protected static string $resource = HallResource::class;

    protected static bool $canCreateAnother = true;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Hall Created')
            ->body('The hall has been created successfully.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate slug if not provided
        if (empty($data['slug']) && isset($data['name']['en'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name']['en']);
        }

        // Set default values
        $data['is_active'] = $data['is_active'] ?? true;
        $data['is_featured'] = $data['is_featured'] ?? false;
        $data['requires_approval'] = $data['requires_approval'] ?? false;
        $data['cancellation_hours'] = $data['cancellation_hours'] ?? 24;
        $data['cancellation_fee_percentage'] = $data['cancellation_fee_percentage'] ?? 0;

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

        // Validate coordinates
        if (isset($data['latitude']) && ($data['latitude'] < -90 || $data['latitude'] > 90)) {
            Notification::make()
                ->danger()
                ->title('Invalid Latitude')
                ->body('Latitude must be between -90 and 90.')
                ->persistent()
                ->send();

            $this->halt();
        }

        if (isset($data['longitude']) && ($data['longitude'] < -180 || $data['longitude'] > 180)) {
            Notification::make()
                ->danger()
                ->title('Invalid Longitude')
                ->body('Longitude must be between -180 and 180.')
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

        // Validate pricing override format
        if (!empty($data['pricing_override'])) {
            $validSlots = ['morning', 'afternoon', 'evening', 'full_day'];
            foreach (array_keys($data['pricing_override']) as $slot) {
                if (!in_array($slot, $validSlots)) {
                    Notification::make()
                        ->warning()
                        ->title('Invalid Time Slot')
                        ->body("'{$slot}' is not a valid time slot. Use: morning, afternoon, evening, or full_day.")
                        ->send();
                }
            }
        }

        // Check for incomplete profile
        $incomplete = [];
        if (empty($data['featured_image'])) $incomplete[] = 'Featured Image';
        if (empty($data['latitude']) || empty($data['longitude'])) $incomplete[] = 'Location Coordinates';
        if (empty($data['features'])) $incomplete[] = 'Features';

        if (!empty($incomplete)) {
            Notification::make()
                ->warning()
                ->title('Incomplete Profile')
                ->body('Missing: ' . implode(', ', $incomplete))
                ->persistent()
                ->send();
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Separate features for pivot table
        $features = $data['features'] ?? [];
        unset($data['features']);

        $record = static::getModel()::create($data);

        // Attach features
        if (!empty($features)) {
            $record->features()->sync($features);
        }

        // Log the creation
        activity()
            ->performedOn($record)
            ->causedBy(Auth::user())
            ->withProperties([
                'name' => $data['name'],
                'city_id' => $data['city_id'],
                'owner_id' => $data['owner_id'],
            ])
            ->log('Hall created');

        return $record;
    }

    protected function afterCreate(): void
    {
        $hall = $this->record;

        // Log the creation
        Log::info('Hall created', [
            'hall_id' => $hall->id,
            'name' => $hall->name,
            'owner_id' => $hall->owner_id,
            'created_by' => Auth::id(),
        ]);

        // Clear cache
        Cache::tags(['halls', 'city_' . $hall->city_id])->flush();

        // Generate availability for next 3 months
        $this->generateInitialAvailability($hall);

        // Notify owner
        $this->notifyOwner($hall);
    }

    protected function generateInitialAvailability($hall): void
    {
        $startDate = now();
        $endDate = now()->addMonths(3);
        $createdCount = 0;

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            foreach (['morning', 'afternoon', 'evening', 'full_day'] as $timeSlot) {
                \App\Models\HallAvailability::create([
                    'hall_id' => $hall->id,
                    'date' => $currentDate->toDateString(),
                    'time_slot' => $timeSlot,
                    'is_available' => true,
                ]);

                $createdCount++;
            }

            $currentDate->addDay();
        }

        Log::info("Generated {$createdCount} availability slots for hall {$hall->id}");
    }

    protected function notifyOwner($hall): void
    {
        // Send notification to owner
        // Example: $hall->owner->notify(new HallCreated($hall));
    }

    protected function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $baseSlug = $slug;
        $counter = 1;

        while (\App\Models\Hall::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
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
        return 'Create Hall';
    }

    public function getSubheading(): ?string
    {
        return 'Add a new hall to the system';
    }
}
