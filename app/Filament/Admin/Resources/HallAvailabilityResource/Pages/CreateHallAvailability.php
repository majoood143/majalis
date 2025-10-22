<?php

namespace App\Filament\Admin\Resources\HallAvailabilityResource\Pages;

use App\Filament\Admin\Resources\HallAvailabilityResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CreateHallAvailability extends CreateRecord
{
    protected static string $resource = HallAvailabilityResource::class;

    protected static bool $canCreateAnother = true;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        $status = $this->record->is_available ? 'Available' : 'Blocked';

        return Notification::make()
            ->success()
            ->title('Availability Created')
            ->body("Hall availability has been set to {$status}.")
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values
        $data['is_available'] = $data['is_available'] ?? true;

        // Validate date is not in the past
        if (isset($data['date']) && \Carbon\Carbon::parse($data['date'])->isPast()) {
            Notification::make()
                ->danger()
                ->title('Invalid Date')
                ->body('Cannot create availability for past dates.')
                ->persistent()
                ->send();

            $this->halt();
        }

        // Check for duplicate slot
        $exists = \App\Models\HallAvailability::where('hall_id', $data['hall_id'])
            ->where('date', $data['date'])
            ->where('time_slot', $data['time_slot'])
            ->exists();

        if ($exists) {
            Notification::make()
                ->danger()
                ->title('Duplicate Slot')
                ->body('This time slot already exists for the selected hall and date.')
                ->persistent()
                ->send();

            $this->halt();
        }

        // Validate custom price
        if (isset($data['custom_price']) && $data['custom_price'] < 0) {
            Notification::make()
                ->danger()
                ->title('Invalid Price')
                ->body('Custom price cannot be negative.')
                ->persistent()
                ->send();

            $this->halt();
        }

        // Clear reason and notes if available
        if ($data['is_available']) {
            $data['reason'] = null;
            $data['notes'] = null;
        }

        // Validate reason is provided if not available
        if (!$data['is_available'] && empty($data['reason'])) {
            Notification::make()
                ->warning()
                ->title('Missing Reason')
                ->body('Please provide a reason for blocking this slot.')
                ->send();
        }

        // Check for existing bookings on this slot
        $this->checkExistingBookings($data);

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
                'date' => $data['date'],
                'time_slot' => $data['time_slot'],
                'is_available' => $data['is_available'],
            ])
            ->log('Hall availability created');

        return $record;
    }

    protected function afterCreate(): void
    {
        $availability = $this->record;

        // Log the creation
        Log::info('Hall availability created', [
            'availability_id' => $availability->id,
            'hall_id' => $availability->hall_id,
            'date' => $availability->date,
            'time_slot' => $availability->time_slot,
            'is_available' => $availability->is_available,
            'created_by' => Auth::id(),
        ]);

        // Clear cache
        Cache::tags(['availability', 'hall_' . $availability->hall_id])->flush();

        // Notify hall owner if blocked
        if (!$availability->is_available && $availability->hall && $availability->hall->owner) {
            $this->notifyHallOwner();
        }

        // Cancel pending bookings if slot is blocked
        if (!$availability->is_available) {
            $this->handlePendingBookings();
        }
    }

    protected function checkExistingBookings(array $data): void
    {
        // Check if there are any bookings for this slot
        // Adjust based on your actual booking structure

        // Example:
        // $bookingsCount = \App\Models\Booking::where('hall_id', $data['hall_id'])
        //     ->whereDate('booking_date', $data['date'])
        //     ->where('time_slot', $data['time_slot'])
        //     ->whereIn('status', ['pending', 'confirmed'])
        //     ->count();
        // 
        // if ($bookingsCount > 0 && !$data['is_available']) {
        //     Notification::make()
        //         ->warning()
        //         ->title('Existing Bookings Found')
        //         ->body("There are {$bookingsCount} existing booking(s) for this slot. They may need to be cancelled.")
        //         ->persistent()
        //         ->send();
        // }
    }

    protected function notifyHallOwner(): void
    {
        // Send notification to hall owner about blocked slot
        // $this->record->hall->owner->notify(new SlotBlocked($this->record));
    }

    protected function handlePendingBookings(): void
    {
        // Handle any pending bookings for this slot
        // You might want to automatically cancel them or notify the admin

        // Example:
        // $pendingBookings = \App\Models\Booking::where('hall_id', $this->record->hall_id)
        //     ->whereDate('booking_date', $this->record->date)
        //     ->where('time_slot', $this->record->time_slot)
        //     ->where('status', 'pending')
        //     ->get();
        // 
        // if ($pendingBookings->isNotEmpty()) {
        //     // Notify about pending bookings that need attention
        //     Notification::make()
        //         ->warning()
        //         ->title('Pending Bookings')
        //         ->body("There are {$pendingBookings->count()} pending booking(s) for this blocked slot.")
        //         ->persistent()
        //         ->send();
        // }
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
        return 'Create Hall Availability';
    }

    public function getSubheading(): ?string
    {
        return 'Set availability or block specific time slots for halls';
    }

    public function mount(): void
    {
        parent::mount();

        // Pre-fill data from URL parameters
        $formData = [];

        if (request()->has('hall_id')) {
            $formData['hall_id'] = request()->get('hall_id');
        }

        if (request()->has('date')) {
            $formData['date'] = request()->get('date');
        }

        if (request()->has('time_slot')) {
            $formData['time_slot'] = request()->get('time_slot');
        }

        if (!empty($formData)) {
            $this->form->fill($formData);
        }
    }
}
