<?php

namespace App\Filament\Admin\Resources\ExtraServiceResource\Pages;

use App\Filament\Admin\Resources\ExtraServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EditExtraService extends EditRecord
{
    protected static string $resource = ExtraServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('toggleActive')
                ->label(fn() => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn() => $this->record->is_active ? 'warning' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn() => $this->record->is_active ? 'Deactivate Service' : 'Activate Service')
                ->modalDescription(fn() => $this->record->is_active
                    ? 'This will deactivate the service. It will no longer be available for new bookings.'
                    : 'This will activate the service and make it available for bookings.')
                ->action(function () {
                    // Prevent deactivation of required services
                    if ($this->record->is_active && $this->record->is_required) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot Deactivate')
                            ->body('Required services cannot be deactivated. Remove the required flag first.')
                            ->persistent()
                            ->send();
                        return;
                    }

                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();

                    Notification::make()
                        ->title('Status Updated')
                        ->body('Service status has been updated successfully.')
                        ->success()
                        ->send();

                    // Clear cache
                    Cache::tags(['services', 'hall_' . $this->record->hall_id])->flush();

                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('toggleRequired')
                ->label(fn() => $this->record->is_required ? 'Make Optional' : 'Make Required')
                ->icon(fn() => $this->record->is_required ? 'heroicon-o-x-mark' : 'heroicon-o-star')
                ->color(fn() => $this->record->is_required ? 'gray' : 'warning')
                ->requiresConfirmation()
                ->modalHeading(fn() => $this->record->is_required ? 'Make Service Optional' : 'Make Service Required')
                ->modalDescription(fn() => $this->record->is_required
                    ? 'This service will no longer be automatically added to bookings.'
                    : 'This service will be automatically added to all new bookings for this hall.')
                ->action(function () {
                    $wasRequired = $this->record->is_required;
                    $this->record->is_required = !$this->record->is_required;

                    // Auto-activate if making required
                    if ($this->record->is_required && !$this->record->is_active) {
                        $this->record->is_active = true;
                    }

                    $this->record->save();

                    Notification::make()
                        ->title('Requirement Status Updated')
                        ->body('Service requirement status has been updated.')
                        ->success()
                        ->send();

                    // Clear cache
                    Cache::tags(['services', 'hall_' . $this->record->hall_id])->flush();

                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('viewBookings')
                ->label('View Bookings')
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->url(fn() => route('filament.admin.resources.bookings.index', [
                    'tableFilters' => [
                        'extra_service_id' => ['value' => $this->record->id]
                    ]
                ])),

            Actions\Action::make('calculateRevenue')
                ->label('Calculate Revenue')
                ->icon('heroicon-o-calculator')
                ->color('success')
                ->modalHeading('Service Revenue Analysis')
                ->modalContent(fn() => view('filament.pages.service-revenue-analysis', [
                    'service' => $this->record,
                    'stats' => $this->getRevenueStats(),
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),

            Actions\Action::make('updatePrice')
                ->label('Update Price')
                ->icon('heroicon-o-currency-dollar')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\TextInput::make('new_price')
                        ->label('New Price')
                        ->numeric()
                        ->required()
                        ->prefix('OMR')
                        ->step(0.001)
                        ->minValue(0)
                        ->default(fn() => $this->record->price),

                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Reason for Price Change')
                        ->rows(3),

                    \Filament\Forms\Components\Toggle::make('apply_to_pending')
                        ->label('Apply to Pending Bookings')
                        ->helperText('Update price for pending bookings that include this service')
                        ->default(false),
                ])
                ->action(function (array $data) {
                    $oldPrice = $this->record->price;
                    $this->record->price = $data['new_price'];
                    $this->record->save();

                    // Log price change
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(Auth::user())
                        ->withProperties([
                            'old_price' => $oldPrice,
                            'new_price' => $data['new_price'],
                            'reason' => $data['reason'] ?? 'No reason provided',
                        ])
                        ->log('Service price updated');

                    // Update pending bookings if requested
                    if ($data['apply_to_pending']) {
                        $this->updatePendingBookingsPrices($data['new_price']);
                    }

                    Notification::make()
                        ->success()
                        ->title('Price Updated')
                        ->body('Service price has been updated successfully.')
                        ->send();

                    // Clear cache
                    Cache::tags(['services', 'hall_' . $this->record->hall_id])->flush();
                }),

            Actions\Action::make('duplicate')
                ->label('Duplicate')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->form([
                    \Filament\Forms\Components\Select::make('target_hall_id')
                        ->label('Target Hall')
                        ->options(\App\Models\Hall::pluck('name', 'id'))
                        ->default($this->record->hall_id)
                        ->required()
                        ->searchable()
                        ->preload(),

                    \Filament\Forms\Components\Toggle::make('copy_image')
                        ->label('Copy Image')
                        ->default(true),
                ])
                ->action(function (array $data) {
                    $newService = $this->record->replicate();
                    $newService->hall_id = $data['target_hall_id'];

                    // Update name to indicate it's a copy
                    $name = $newService->getTranslations('name');
                    foreach ($name as $locale => $value) {
                        $name[$locale] = $value . ' (Copy)';
                    }
                    $newService->setTranslations('name', $name);

                    $newService->save();

                    // Copy image if requested
                    if ($data['copy_image'] && $this->record->image) {
                        $newService->image = $this->record->image;
                        $newService->save();
                    }

                    Notification::make()
                        ->success()
                        ->title('Service Duplicated')
                        ->body('The service has been duplicated successfully.')
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label('Edit Duplicate')
                                ->url(ExtraServiceResource::getUrl('edit', ['record' => $newService->id])),
                        ])
                        ->send();
                }),

            Actions\Action::make('replaceImage')
                ->label('Replace Image')
                ->icon('heroicon-o-photo')
                ->color('info')
                ->visible(fn() => $this->record->image !== null)
                ->form([
                    \Filament\Forms\Components\FileUpload::make('new_image')
                        ->label('New Image')
                        ->image()
                        ->required()
                        ->directory('services'),
                ])
                ->action(function (array $data) {
                    // Delete old image
                    if ($this->record->image) {
                        Storage::disk('public')->delete($this->record->image);
                    }

                    $this->record->image = $data['new_image'];
                    $this->record->save();

                    Notification::make()
                        ->success()
                        ->title('Image Updated')
                        ->body('Service image has been replaced successfully.')
                        ->send();
                }),

            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
                    // Check if service is used in any bookings
                    // Adjust this based on your actual relationship structure
                    // if ($this->record->bookings()->exists()) {
                    //     Notification::make()
                    //         ->danger()
                    //         ->title('Cannot Delete Service')
                    //         ->body('This service is used in existing bookings.')
                    //         ->persistent()
                    //         ->send();
                    //     
                    //     $action->cancel();
                    // }

                    // Check if service is required
                    if ($this->record->is_required) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot Delete Required Service')
                            ->body('Required services cannot be deleted. Make it optional first.')
                            ->persistent()
                            ->send();

                        $action->cancel();
                    }
                })
                ->after(function () {
                    // Delete image if exists
                    if ($this->record->image) {
                        Storage::disk('public')->delete($this->record->image);
                    }

                    // Clear cache
                    Cache::tags(['services', 'hall_' . $this->record->hall_id])->flush();
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Service Deleted')
                        ->body('The extra service has been deleted successfully.')
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
            ->title('Service Updated')
            ->body('The extra service has been updated successfully.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
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

        // Validate required service logic
        if ($data['is_required'] && !$data['is_active']) {
            Notification::make()
                ->warning()
                ->title('Auto-Activation')
                ->body('Required services must be active. Service has been activated automatically.')
                ->send();

            $data['is_active'] = true;
        }

        // Check if hall changed
        if ($data['hall_id'] !== $this->record->hall_id) {
            Notification::make()
                ->warning()
                ->title('Hall Changed')
                ->body('Moving service to a different hall. Existing bookings will not be affected.')
                ->send();
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
            ->log('Extra service updated');

        return $record;
    }

    protected function afterSave(): void
    {
        // Clear cache for both old and new hall if hall changed
        $oldHallId = $this->record->getOriginal('hall_id');
        $newHallId = $this->record->hall_id;

        Cache::tags(['services'])->flush();

        if ($oldHallId) {
            Cache::tags(['hall_' . $oldHallId])->flush();
        }

        if ($newHallId && $newHallId !== $oldHallId) {
            Cache::tags(['hall_' . $newHallId])->flush();
        }

        // Log the update
        Log::info('Extra service updated', [
            'service_id' => $this->record->id,
            'hall_id' => $this->record->hall_id,
            'name' => $this->record->name,
            'updated_by' => Auth::id(),
        ]);

        // Notify if significant price change
        if ($this->hasSignificantPriceChange()) {
            $this->notifyPriceChange();
        }
    }

    protected function hasSignificantPriceChange(): bool
    {
        $original = $this->record->getOriginal('price');
        $current = $this->record->price;

        if ($original && $current && $original != $current) {
            $changePercent = abs(($current - $original) / $original * 100);
            return $changePercent > 10;
        }

        return false;
    }

    protected function notifyPriceChange(): void
    {
        // Notify hall owner about significant price changes
        if ($this->record->hall && $this->record->hall->owner) {
            // $this->record->hall->owner->notify(new ServicePriceChanged($this->record));
        }
    }

    protected function updatePendingBookingsPrices(float $newPrice): void
    {
        // Update price in pending bookings
        // Adjust based on your actual booking-service relationship structure

        // Example:
        // \DB::table('booking_extra_service')
        //     ->whereIn('booking_id', function ($query) {
        //         $query->select('id')
        //             ->from('bookings')
        //             ->where('status', 'pending')
        //             ->where('hall_id', $this->record->hall_id);
        //     })
        //     ->where('extra_service_id', $this->record->id)
        //     ->update(['price' => $newPrice]);
    }

    protected function getRevenueStats(): array
    {
        // Placeholder - implement based on your booking structure
        return [
            'total_bookings' => 0,
            'total_revenue' => 0,
            'average_quantity' => 0,
            'most_booked_month' => null,
        ];
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
        return 'Edit Service: ' . $this->record->name;
    }

    public function getSubheading(): ?string
    {
        $hall = $this->record->hall->name ?? 'Unknown Hall';
        $price = number_format($this->record->price, 3) . ' OMR';
        $unit = ucfirst(str_replace('_', ' ', $this->record->unit));
        $status = $this->record->is_active ? 'Active' : 'Inactive';
        $required = $this->record->is_required ? '• Required' : '';

        return "{$hall} • {$status} {$required} • {$price} / {$unit}";
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
