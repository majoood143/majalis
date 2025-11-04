<?php

namespace App\Filament\Admin\Resources\ExtraServiceResource\Pages;

use App\Filament\Admin\Resources\ExtraServiceResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CreateExtraService extends CreateRecord
{
    protected static string $resource = ExtraServiceResource::class;

    protected static bool $canCreateAnother = true;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Extra Service Created')
            ->body('The extra service has been created successfully.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? true;
        $data['is_required'] = $data['is_required'] ?? false;
        $data['minimum_quantity'] = $data['minimum_quantity'] ?? 1;
        $data['unit'] = $data['unit'] ?? 'fixed';

        // Validate price
        if ($data['price'] < 0) {
            Notification::make()
                ->danger()
                ->title('Invalid Price')
                ->body('Price cannot be negative.')
                ->persistent()
                ->send();

            $this->halt();
        }

        // Validate quantity range
        if (isset($data['maximum_quantity']) && $data['maximum_quantity'] < $data['minimum_quantity']) {
            Notification::make()
                ->danger()
                ->title('Invalid Quantity Range')
                ->body('Maximum quantity must be greater than or equal to minimum quantity.')
                ->persistent()
                ->send();

            $this->halt();
        }

        // Check for duplicate service names in the same hall
        $exists = \App\Models\ExtraService::where('hall_id', $data['hall_id'])
            ->where(function ($query) use ($data) {
                $query->where('name->en', $data['name']['en'])
                    ->orWhere('name->ar', $data['name']['ar']);
            })
            ->exists();

        if ($exists) {
            Notification::make()
                ->warning()
                ->title('Similar Service Found')
                ->body('A service with a similar name already exists for this hall.')
                ->persistent()
                ->send();
        }

        // Validate required service logic
        if ($data['is_required'] && !$data['is_active']) {
            Notification::make()
                ->warning()
                ->title('Auto-Activation')
                ->body('Required services must be active. Service has been activated automatically.')
                ->send();

            $data['is_active'] = true;
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
                'hall_id' => $data['hall_id'],
                'price' => $data['price'],
                'unit' => $data['unit'],
                'is_required' => $data['is_required'],
            ])
            ->log('Extra service created');

        return $record;
    }

    protected function afterCreate(): void
    {
        $service = $this->record;

        // Log the creation
        Log::info('Extra service created', [
            'service_id' => $service->id,
            'hall_id' => $service->hall_id,
            'name' => $service->name,
            'price' => $service->price,
            'is_required' => $service->is_required,
            'created_by' => Auth::id(),
        ]);

        // Clear cache
        //Cache::tags(['services', 'hall_' . $service->hall_id])->flush();

        // Notify hall owner about new service
        if ($service->hall && $service->hall->owner) {
            $this->notifyHallOwner();
        }

        // Auto-add to existing pending bookings if required
        if ($service->is_required) {
            $this->addToExistingBookings();
        }
    }

    protected function notifyHallOwner(): void
    {
        // Implement notification to hall owner
        // Example: $this->record->hall->owner->notify(new ExtraServiceCreated($this->record));
    }

    protected function addToExistingBookings(): void
    {
        // If service is required, add it to pending bookings for this hall
        // This is optional based on your business logic

        // Example implementation:
        // $pendingBookings = \App\Models\Booking::where('hall_id', $this->record->hall_id)
        //     ->where('status', 'pending')
        //     ->get();
        // 
        // foreach ($pendingBookings as $booking) {
        //     $booking->extraServices()->attach($this->record->id, [
        //         'quantity' => $this->record->minimum_quantity,
        //         'price' => $this->record->price,
        //     ]);
        // }
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
        return 'Create Extra Service';
    }

    public function getSubheading(): ?string
    {
        return 'Add a new extra service to a hall';
    }

    public function mount(): void
    {
        parent::mount();

        // Pre-fill hall_id if coming from hall view
        if (request()->has('hall_id')) {
            $this->form->fill([
                'hall_id' => request()->get('hall_id'),
            ]);
        }
    }
}
