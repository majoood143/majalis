<?php

namespace App\Filament\Admin\Resources\CityResource\Pages;

use App\Filament\Admin\Resources\CityResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CreateCity extends CreateRecord
{
    protected static string $resource = CityResource::class;

    protected static bool $canCreateAnother = true;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('City Created')
            ->body('The city has been created successfully.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values if not provided
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? true;

        // Ensure name and description are properly formatted as JSON
        if (isset($data['name']) && is_array($data['name'])) {
            $data['name'] = $data['name'];
        }

        if (isset($data['description']) && is_array($data['description'])) {
            $data['description'] = $data['description'];
        }

        // Generate code from English name if not provided
        if (empty($data['code']) && isset($data['name']['en'])) {
            $data['code'] = $this->generateCodeFromName($data['name']['en']);
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
            ->log('City created');

        return $record;
    }
    

    protected function afterCreate(): void
    {
        // You can perform additional actions after creation
        // For example: send notifications, trigger events, etc.

        $city = $this->record;

        // Example: Log additional information
        Log::info('New city created', [
            'city_id' => $city->id,
            'name' => $city->name,
            'region_id' => $city->region_id,
            'created_by' => Auth::id(),
        ]);
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

    private function generateCodeFromName(string $name): string
    {
        // Generate a code from the name (uppercase, no spaces, max 10 chars)
        $code = strtoupper(str_replace(' ', '_', $name));

        // Limit to 10 characters
        if (strlen($code) > 10) {
            $code = substr($code, 0, 10);
        }

        // Ensure uniqueness
        $baseCode = $code;
        $counter = 1;

        while (\App\Models\City::where('code', $code)->exists()) {
            $code = $baseCode . $counter;
            $counter++;

            // Re-limit to 10 characters
            if (strlen($code) > 10) {
                $code = substr($baseCode, 0, 8) . $counter;
            }
        }

        return $code;
    }

    // protected function getTitle(): string
    // {
    //     return 'Create City';
    // }

    // protected function getSubheading(): ?string
    // {
    //     return 'Add a new city to the system';
    // }
}
