<?php

namespace App\Filament\Admin\Resources\HallResource\Pages;

use App\Filament\Admin\Resources\HallResource;
use App\Models\User;
use App\Services\ImageOptimizationService;
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

        // Strip virtual availability fields (not stored on halls table)
        unset($data['availability_from_date'], $data['availability_to_date']);

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
        // $features = $data['features'] ?? [];
        // unset($data['features']);

        $record = static::getModel()::create($data);

        // Attach features
        // if (!empty($features)) {
        //     $record->features()->sync($features);
        // }

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
        $hall      = $this->record;
        $optimizer = app(ImageOptimizationService::class);

        // Compress featured image
        if (!empty($hall->featured_image)) {
            $optimizer->compress($hall->featured_image);
        }

        // Compress gallery images
        if (!empty($hall->gallery)) {
            $optimizer->compressMany($hall->gallery);
        }

        // Log the creation
        Log::info('Hall created', [
            'hall_id' => $hall->id,
            'name' => $hall->name,
            'owner_id' => $hall->owner_id,
            'created_by' => Auth::id(),
        ]);

        // if ($this->data['features'] ?? null) {
        //     $this->record->features()->sync($this->data['features']);
        // }

        // Clear cache
        //Cache::tags(['halls', 'city_' . $hall->city_id])->flush();

        // Generate availability for the chosen date range
        $fromDate = $this->data['availability_from_date'] ?? now()->toDateString();
        $toDate   = $this->data['availability_to_date']   ?? now()->addMonths(3)->toDateString();
        $this->generateInitialAvailability($hall, $fromDate, $toDate);

        // Notify owner
        $this->notifyOwner($hall);

        // Notify all admins via database
        $this->notifyAdmins($hall, 'created');
    }

    protected function notifyAdmins($hall, string $event): void
    {
        $hallName = $hall->getTranslation('name', 'en', false) ?: $hall->name;
        $actor    = Auth::user()->name;

        $titles = [
            'created' => "Hall Created: {$hallName}",
            'updated' => "Hall Updated: {$hallName}",
            'deleted' => "Hall Deleted: {$hallName}",
        ];

        $bodies = [
            'created' => "Created by {$actor}",
            'updated' => "Updated by {$actor}",
            'deleted' => "Deleted by {$actor}",
        ];

        $admins = User::where('role', \App\Enums\UserRole::ADMIN)->get();

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

    protected function generateInitialAvailability($hall, ?string $fromDate = null, ?string $toDate = null): void
    {
        $startDate = $fromDate ? \Carbon\Carbon::parse($fromDate) : now();
        $endDate   = $toDate   ? \Carbon\Carbon::parse($toDate)   : now()->addMonths(3);
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
                //->submit(null)
                ->keyBindings(['mod+s']),

            $this->getCreateAnotherFormAction()
                ->keyBindings(['mod+shift+s']),

            $this->getCancelFormAction(),
        ];
    }

    public function getTitle(): string
    {
        return __('admin.actions.create_hall');
    }

    public function getSubheading(): ?string
    {
        return __('admin.actions.create_hall_description');
    }
}
