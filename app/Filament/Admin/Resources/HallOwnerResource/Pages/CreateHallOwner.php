<?php

namespace App\Filament\Admin\Resources\HallOwnerResource\Pages;

use App\Filament\Admin\Resources\HallOwnerResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CreateHallOwner extends CreateRecord
{
    protected static string $resource = HallOwnerResource::class;

    protected static bool $canCreateAnother = true;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Hall Owner Created')
            ->body('The hall owner profile has been created successfully.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values
        $data['is_verified'] = $data['is_verified'] ?? false;
        $data['is_active'] = $data['is_active'] ?? true;

        // Validate commercial registration uniqueness
        if (isset($data['commercial_registration'])) {
            $exists = \App\Models\HallOwner::where('commercial_registration', $data['commercial_registration'])
                ->exists();

            if ($exists) {
                Notification::make()
                    ->danger()
                    ->title('Duplicate Registration')
                    ->body('This commercial registration number already exists.')
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

        // Validate user is not already an owner
        if (isset($data['user_id'])) {
            $exists = \App\Models\HallOwner::where('user_id', $data['user_id'])->exists();

            if ($exists) {
                Notification::make()
                    ->danger()
                    ->title('User Already Owner')
                    ->body('This user is already registered as a hall owner.')
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

        // Validate commission settings
        if (isset($data['commission_type']) && isset($data['commission_value'])) {
            if ($data['commission_type'] === 'percentage' && $data['commission_value'] > 100) {
                Notification::make()
                    ->danger()
                    ->title('Invalid Commission')
                    ->body('Percentage commission cannot exceed 100%.')
                    ->persistent()
                    ->send();

                $this->halt();
            }

            if ($data['commission_value'] < 0) {
                Notification::make()
                    ->danger()
                    ->title('Invalid Commission')
                    ->body('Commission value cannot be negative.')
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

        // Validate phone number format
        if (isset($data['business_phone'])) {
            $data['business_phone'] = preg_replace('/[^0-9+]/', '', $data['business_phone']);
        }

        // Validate email format
        if (isset($data['business_email']) && !filter_var($data['business_email'], FILTER_VALIDATE_EMAIL)) {
            Notification::make()
                ->warning()
                ->title('Invalid Email')
                ->body('Please provide a valid business email address.')
                ->send();
        }

        // Check for incomplete required documents
        if (empty($data['commercial_registration_document'])) {
            Notification::make()
                ->warning()
                ->title('Missing Document')
                ->body('Commercial registration document is recommended.')
                ->send();
        }

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
                'user_id' => $data['user_id'],
                'business_name' => $data['business_name'],
                'commercial_registration' => $data['commercial_registration'],
            ])
            ->log('Hall owner created');

        return $record;
    }

    protected function afterCreate(): void
    {
        $owner = $this->record;

        // Log the creation
        Log::info('Hall owner created', [
            'owner_id' => $owner->id,
            'user_id' => $owner->user_id,
            'business_name' => $owner->business_name,
            'created_by' => Auth::id(),
        ]);

        // Clear cache
        //Cache::tags(['hall_owners'])->flush();

        // Send welcome notification to owner
        $this->sendWelcomeNotification($owner);

        // Notify admin team
        $this->notifyAdminTeam($owner);
    }

    protected function sendWelcomeNotification($owner): void
    {
        // Send welcome email/notification to the owner
        // Example: $owner->user->notify(new WelcomeHallOwner($owner));
    }

    protected function notifyAdminTeam($owner): void
    {
        // Notify admin team about new owner registration
        Notification::make()
            ->title('New Hall Owner Registered')
            ->body("{$owner->business_name} has been registered and requires verification.")
            ->info()
            ->sendToDatabase(Auth::user());
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
        return 'Create Hall Owner';
    }

    public function getSubheading(): ?string
    {
        return 'Register a new hall owner with business details';
    }
}
