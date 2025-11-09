<?php

namespace App\Filament\Admin\Resources\HallAvailabilityResource\Pages;

use App\Filament\Admin\Resources\HallAvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EditHallAvailability extends EditRecord
{
    protected static string $resource = HallAvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('toggleAvailability')
                ->label(fn() => $this->record->is_available ? 'Block Slot' : 'Unblock Slot')
                ->icon(fn() => $this->record->is_available ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                ->color(fn() => $this->record->is_available ? 'danger' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn() => $this->record->is_available ? 'Block This Slot' : 'Unblock This Slot')
                ->modalDescription(fn() => $this->record->is_available
                    ? 'This will block the slot and prevent new bookings.'
                    : 'This will make the slot available for bookings again.')
                ->form(fn() => $this->record->is_available ? [
                    \Filament\Forms\Components\Select::make('reason')
                        ->label('Block Reason')
                        ->options([
                            'maintenance' => 'Under Maintenance',
                            'blocked' => 'Blocked by Owner',
                            'holiday' => 'Holiday',
                            'custom' => 'Custom Block',
                        ])
                        ->required(),

                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('Notes')
                        ->rows(3),
                ] : [])
                ->action(function (array $data) {
                    $wasAvailable = $this->record->is_available;
                    $this->record->is_available = !$this->record->is_available;

                    if (!$this->record->is_available) {
                        $this->record->reason = $data['reason'];
                        $this->record->notes = $data['notes'] ?? null;
                    } else {
                        $this->record->reason = null;
                        $this->record->notes = null;
                    }

                    $this->record->save();

                    // Log the change
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(Auth::user())
                        ->withProperties([
                            'was_available' => $wasAvailable,
                            'is_available' => $this->record->is_available,
                        ])
                        ->log('Availability status toggled');

                    // Handle bookings if blocked
                    if (!$this->record->is_available && $wasAvailable) {
                        $this->handleExistingBookingsWhenBlocked();
                    }

                    Notification::make()
                        ->success()
                        ->title('Availability Updated')
                        ->body('Slot availability has been updated.')
                        ->send();

                    // Clear cache
                    Cache::tags(['availability', 'hall_' . $this->record->hall_id])->flush();

                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('viewBookings')
                ->label('View Bookings')
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->url(fn() => route('filament.admin.resources.bookings.index', [
                    'tableFilters' => [
                        'hall_id' => ['value' => $this->record->hall_id],
                        'date' => ['value' => $this->record->date->format('Y-m-d')],
                    ]
                ])),

            Actions\Action::make('updatePrice')
                ->label('Update Price')
                ->icon('heroicon-o-currency-dollar')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\TextInput::make('custom_price')
                        ->label('Custom Price')
                        ->numeric()
                        ->prefix('OMR')
                        ->step(0.001)
                        ->minValue(0)
                        ->helperText('Leave empty to use default hall pricing')
                        ->default(fn() => $this->record->custom_price),

                    \Filament\Forms\Components\Placeholder::make('current_default')
                        ->label('Default Hall Price')
                        ->content(fn() => number_format($this->getDefaultPrice(), 3) . ' OMR'),

                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Reason for Price Change')
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $oldPrice = $this->record->custom_price;
                    $this->record->custom_price = $data['custom_price'] ?: null;
                    $this->record->save();

                    // Log price change
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(Auth::user())
                        ->withProperties([
                            'old_price' => $oldPrice,
                            'new_price' => $data['custom_price'],
                            'reason' => $data['reason'] ?? 'No reason provided',
                        ])
                        ->log('Custom price updated');

                    Notification::make()
                        ->success()
                        ->title('Price Updated')
                        ->body('Custom price has been updated successfully.')
                        ->send();

                    // Clear cache
                    Cache::tags(['availability', 'hall_' . $this->record->hall_id])->flush();
                }),

            Actions\Action::make('duplicate')
                ->label('Duplicate to Other Dates')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('start_date')
                        ->label('Start Date')
                        ->required()
                        ->native(false)
                        ->minDate(now())
                        ->default(now()->addDay()),

                    \Filament\Forms\Components\DatePicker::make('end_date')
                        ->label('End Date')
                        ->required()
                        ->native(false)
                        ->minDate(now())
                        ->afterOrEqual('start_date'),

                    \Filament\Forms\Components\Toggle::make('copy_same_time_slot')
                        ->label('Same Time Slot Only')
                        ->helperText('Copy only the same time slot, or all time slots')
                        ->default(true),

                    \Filament\Forms\Components\Toggle::make('skip_existing')
                        ->label('Skip Existing Records')
                        ->default(true),
                ])
                ->action(function (array $data) {
                    $this->duplicateToOtherDates($data);
                }),

            Actions\Action::make('extendBlock')
                ->label('Extend Block Period')
                ->icon('heroicon-o-calendar-days')
                ->color('danger')
                ->visible(fn() => !$this->record->is_available)
                ->form([
                    \Filament\Forms\Components\DatePicker::make('extend_until')
                        ->label('Extend Until')
                        ->required()
                        ->native(false)
                        ->minDate($this->record->date)
                        ->default($this->record->date->addWeek()),

                    \Filament\Forms\Components\Toggle::make('copy_settings')
                        ->label('Copy Block Settings')
                        ->helperText('Use the same reason and notes')
                        ->default(true),
                ])
                ->action(function (array $data) {
                    $this->extendBlockPeriod($data);
                }),

            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
                    // Check for existing bookings
                    // $bookingsCount = \App\Models\Booking::where('hall_id', $this->record->hall_id)
                    //     ->whereDate('booking_date', $this->record->date)
                    //     ->where('time_slot', $this->record->time_slot)
                    //     ->whereIn('status', ['confirmed'])
                    //     ->count();
                    // 
                    // if ($bookingsCount > 0) {
                    //     Notification::make()
                    //         ->danger()
                    //         ->title('Cannot Delete')
                    //         ->body("There are {$bookingsCount} confirmed booking(s) for this slot.")
                    //         ->persistent()
                    //         ->send();
                    //     
                    //     $action->cancel();
                    // }
                })
                ->after(function () {
                    // Clear cache
                    Cache::tags(['availability', 'hall_' . $this->record->hall_id])->flush();
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Availability Deleted')
                        ->body('The availability record has been deleted.')
                ),

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
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Availability Updated')
            ->body('The availability record has been updated successfully.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validate date is not in the past
        if (isset($data['date']) && \Carbon\Carbon::parse($data['date'])->isPast()) {
            Notification::make()
                ->danger()
                ->title('Invalid Date')
                ->body('Cannot set availability for past dates.')
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

        // Check for duplicate if hall/date/slot changed
        if (
            $data['hall_id'] != $this->record->hall_id ||
            $data['date'] != $this->record->date ||
            $data['time_slot'] != $this->record->time_slot
        ) {

            $exists = \App\Models\HallAvailability::where('hall_id', $data['hall_id'])
                ->where('date', $data['date'])
                ->where('time_slot', $data['time_slot'])
                ->where('id', '!=', $this->record->id)
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
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $oldValues = $record->toArray();

        $record->update($data);

        $changes = array_diff_assoc($data, $oldValues);

        // Log the update
        activity()
            ->performedOn($record)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'changes' => $changes,
            ])
            ->log('Hall availability updated');

        return $record;
    }

    protected function afterSave(): void
    {
        // Clear cache for the hall
        //Cache::tags(['availability', 'hall_' . $this->record->hall_id])->flush();

        // Log the update
        Log::info('Hall availability updated', [
            'availability_id' => $this->record->id,
            'hall_id' => $this->record->hall_id,
            'date' => $this->record->date,
            'updated_by' => Auth::id(),
        ]);

        // Handle bookings if status changed
        $wasAvailable = $this->record->getOriginal('is_available');
        $isAvailable = $this->record->is_available;

        if ($wasAvailable && !$isAvailable) {
            $this->handleExistingBookingsWhenBlocked();
        }
    }

    protected function handleExistingBookingsWhenBlocked(): void
    {
        // Check for existing bookings and notify
        // $bookingsCount = \App\Models\Booking::where('hall_id', $this->record->hall_id)
        //     ->whereDate('booking_date', $this->record->date)
        //     ->where('time_slot', $this->record->time_slot)
        //     ->whereIn('status', ['pending', 'confirmed'])
        //     ->count();
        // 
        // if ($bookingsCount > 0) {
        //     Notification::make()
        //         ->warning()
        //         ->title('Existing Bookings Found')
        //         ->body("There are {$bookingsCount} booking(s) for this blocked slot. Please review and handle them.")
        //         ->persistent()
        //         ->actions([
        //             \Filament\Notifications\Actions\Action::make('viewBookings')
        //                 ->label('View Bookings')
        //                 ->url(route('filament.admin.resources.bookings.index')),
        //         ])
        //         ->send();
        // }
    }

    protected function duplicateToOtherDates(array $data): void
    {
        $startDate = \Carbon\Carbon::parse($data['start_date']);
        $endDate = \Carbon\Carbon::parse($data['end_date']);
        $createdCount = 0;
        $skippedCount = 0;

        while ($startDate->lte($endDate)) {
            $timeSlots = $data['copy_same_time_slot']
                ? [$this->record->time_slot]
                : ['morning', 'afternoon', 'evening', 'full_day'];

            foreach ($timeSlots as $timeSlot) {
                $exists = \App\Models\HallAvailability::where('hall_id', $this->record->hall_id)
                    ->where('date', $startDate->toDateString())
                    ->where('time_slot', $timeSlot)
                    ->exists();

                if ($data['skip_existing'] && $exists) {
                    $skippedCount++;
                    continue;
                }

                \App\Models\HallAvailability::create([
                    'hall_id' => $this->record->hall_id,
                    'date' => $startDate->toDateString(),
                    'time_slot' => $timeSlot,
                    'is_available' => $this->record->is_available,
                    'reason' => $this->record->reason,
                    'notes' => $this->record->notes,
                    'custom_price' => $this->record->custom_price,
                ]);

                $createdCount++;
            }

            $startDate->addDay();
        }

        // Clear cache
        Cache::tags(['availability', 'hall_' . $this->record->hall_id])->flush();

        Notification::make()
            ->success()
            ->title('Duplication Completed')
            ->body("Created: {$createdCount}, Skipped: {$skippedCount} records.")
            ->send();
    }

    protected function extendBlockPeriod(array $data): void
    {
        $currentDate = $this->record->date->copy()->addDay();
        $endDate = \Carbon\Carbon::parse($data['extend_until']);
        $createdCount = 0;

        while ($currentDate->lte($endDate)) {
            $exists = \App\Models\HallAvailability::where('hall_id', $this->record->hall_id)
                ->where('date', $currentDate->toDateString())
                ->where('time_slot', $this->record->time_slot)
                ->exists();

            if (!$exists) {
                \App\Models\HallAvailability::create([
                    'hall_id' => $this->record->hall_id,
                    'date' => $currentDate->toDateString(),
                    'time_slot' => $this->record->time_slot,
                    'is_available' => false,
                    'reason' => $data['copy_settings'] ? $this->record->reason : 'blocked',
                    'notes' => $data['copy_settings'] ? $this->record->notes : null,
                ]);

                $createdCount++;
            }

            $currentDate->addDay();
        }

        // Clear cache
        Cache::tags(['availability', 'hall_' . $this->record->hall_id])->flush();

        Notification::make()
            ->success()
            ->title('Block Period Extended')
            ->body("{$createdCount} slot(s) blocked.")
            ->send();
    }

    protected function getDefaultPrice(): float
    {
        // Get default hall price for this time slot
        // Adjust based on your hall pricing structure
        if ($this->record->hall) {
            return $this->record->hall->price ?? 0.000;
        }

        return 0.000;
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
        return 'Edit Availability: ' . $this->record->hall->name;
    }

    public function getSubheading(): ?string
    {
        $date = $this->record->date->format('d M Y');
        $timeSlot = ucfirst(str_replace('_', ' ', $this->record->time_slot));
        $status = $this->record->is_available ? 'Available' : 'Blocked';
        $price = $this->record->custom_price
            ? number_format($this->record->custom_price, 3) . ' OMR (Custom)'
            : 'Default Price';

        return "{$date} • {$timeSlot} • {$status} • {$price}";
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
