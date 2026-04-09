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
                ->label(fn() => $this->record->is_active ? __('extra-service.page_actions.deactivate') : __('extra-service.page_actions.activate'))
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn() => $this->record->is_active ? 'warning' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn() => $this->record->is_active ? __('extra-service.page_actions.deactivate_heading') : __('extra-service.page_actions.activate_heading'))
                ->modalDescription(fn() => $this->record->is_active
                    ? __('extra-service.page_actions.deactivate_description')
                    : __('extra-service.page_actions.activate_description'))
                ->action(function () {
                    // Prevent deactivation of required services
                    if ($this->record->is_active && $this->record->is_required) {
                        Notification::make()
                            ->danger()
                            ->title(__('extra-service.notifications.cannot_deactivate_title'))
                            ->body(__('extra-service.notifications.cannot_deactivate_body'))
                            ->persistent()
                            ->send();
                        return;
                    }

                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();

                    Notification::make()
                        ->title(__('extra-service.notifications.status_updated_title'))
                        ->body(__('extra-service.notifications.status_updated_body'))
                        ->success()
                        ->send();

                    // Clear cache
                    Cache::tags(['services', 'hall_' . $this->record->hall_id])->flush();

                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('toggleRequired')
                ->label(fn() => $this->record->is_required ? __('extra-service.page_actions.make_optional') : __('extra-service.page_actions.make_required'))
                ->icon(fn() => $this->record->is_required ? 'heroicon-o-x-mark' : 'heroicon-o-star')
                ->color(fn() => $this->record->is_required ? 'gray' : 'warning')
                ->requiresConfirmation()
                ->modalHeading(fn() => $this->record->is_required ? __('extra-service.page_actions.make_optional_heading') : __('extra-service.page_actions.make_required_heading'))
                ->modalDescription(fn() => $this->record->is_required
                    ? __('extra-service.page_actions.make_optional_description')
                    : __('extra-service.page_actions.make_required_description'))
                ->action(function () {
                    $wasRequired = $this->record->is_required;
                    $this->record->is_required = !$this->record->is_required;

                    // Auto-activate if making required
                    if ($this->record->is_required && !$this->record->is_active) {
                        $this->record->is_active = true;
                    }

                    $this->record->save();

                    Notification::make()
                        ->title(__('extra-service.notifications.requirement_updated_title'))
                        ->body(__('extra-service.notifications.requirement_updated_body'))
                        ->success()
                        ->send();

                    // Clear cache
                    Cache::tags(['services', 'hall_' . $this->record->hall_id])->flush();

                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('viewBookings')
                ->label(__('extra-service.page_actions.view_bookings'))
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->url(fn() => route('filament.admin.resources.bookings.index', [
                    'tableFilters' => [
                        'extra_service_id' => ['value' => $this->record->id]
                    ]
                ])),

            Actions\Action::make('calculateRevenue')
                ->label(__('extra-service.page_actions.calculate_revenue'))
                ->icon('heroicon-o-calculator')
                ->color('success')
                ->modalHeading(__('extra-service.page_actions.service_revenue_analysis_heading'))
                ->modalContent(fn() => view('filament.pages.service-revenue-analysis', [
                    'service' => $this->record,
                    'stats' => $this->getRevenueStats(),
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel(__('extra-service.page_actions.close')),

            Actions\Action::make('updatePrice')
                ->label(__('extra-service.page_actions.update_price'))
                ->icon('heroicon-o-currency-dollar')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\TextInput::make('new_price')
                        ->label(__('extra-service.page_actions.new_price'))
                        ->numeric()
                        ->required()
                        ->prefix('OMR')
                        ->step(0.001)
                        ->minValue(0)
                        ->default(fn() => $this->record->price),

                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label(__('extra-service.page_actions.reason_for_price_change'))
                        ->rows(3),

                    \Filament\Forms\Components\Toggle::make('apply_to_pending')
                        ->label(__('extra-service.page_actions.apply_to_pending'))
                        ->helperText(__('extra-service.page_actions.apply_to_pending_helper'))
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
                        ->title(__('extra-service.notifications.price_updated_title'))
                        ->body(__('extra-service.notifications.price_updated_body'))
                        ->send();

                    // Clear cache
                    Cache::tags(['services', 'hall_' . $this->record->hall_id])->flush();
                }),

            Actions\Action::make('duplicate')
                ->label(__('extra-service.page_actions.duplicate'))
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->form([
                    \Filament\Forms\Components\Select::make('target_hall_id')
                        ->label(__('extra-service.page_actions.target_hall'))
                        ->options(\App\Models\Hall::pluck('name', 'id'))
                        ->default($this->record->hall_id)
                        ->required()
                        ->searchable()
                        ->preload(),

                    \Filament\Forms\Components\Toggle::make('copy_image')
                        ->label(__('extra-service.page_actions.copy_image'))
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
                        ->title(__('extra-service.notifications.service_duplicated_title'))
                        ->body(__('extra-service.notifications.service_duplicated_body'))
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label(__('extra-service.page_actions.edit_duplicate'))
                                ->url(ExtraServiceResource::getUrl('edit', ['record' => $newService->id])),
                        ])
                        ->send();
                }),

            Actions\Action::make('replaceImage')
                ->label(__('extra-service.page_actions.replace_image'))
                ->icon('heroicon-o-photo')
                ->color('info')
                ->visible(fn() => $this->record->image !== null)
                ->form([
                    \Filament\Forms\Components\FileUpload::make('new_image')
                        ->label(__('extra-service.page_actions.new_image'))
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
                        ->title(__('extra-service.notifications.image_updated_title'))
                        ->body(__('extra-service.notifications.image_updated_body'))
                        ->send();
                }),

            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
                    // Check if service is used in any bookings
                    // Adjust this based on your actual relationship structure
                    // if ($this->record->bookings()->exists()) {
                    //     Notification::make()
                    //         ->danger()
                    //         ->title(__('extra-service.notifications.cannot_delete_required_title'))
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
                            ->title(__('extra-service.notifications.cannot_delete_required_title'))
                            ->body(__('extra-service.notifications.cannot_delete_required_body'))
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
                        ->title(__('extra-service.notifications.service_deleted_title'))
                        ->body(__('extra-service.notifications.service_deleted_body'))
                ),

            // Actions\Action::make('viewHistory')
            //     ->label('View History')
            //     ->icon('heroicon-o-clock')
            //     ->color('gray')
            //     ->modalContent(fn() => view('filament.pages.activity-log', [
            //         'activities' => activity()
            //             ->forSubject($this->record)
            //             ->latest()
            //             ->get()
            //     ]))
            //     ->modalSubmitAction(false)
            //     ->modalCancelActionLabel('Close'),
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
            ->title(__('extra-service.notifications.service_updated_title'))
            ->body(__('extra-service.notifications.service_updated_body'))
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
                ->title(__('extra-service.notifications.invalid_price_title'))
                ->body(__('extra-service.notifications.invalid_price_body'))
                ->persistent()
                ->send();

            $this->halt();
        }

        // Validate quantity range
        if (isset($data['maximum_quantity']) && $data['maximum_quantity'] < $data['minimum_quantity']) {
            Notification::make()
                ->danger()
                ->title(__('extra-service.notifications.invalid_quantity_range_title'))
                ->body(__('extra-service.notifications.invalid_quantity_range_body'))
                ->persistent()
                ->send();

            $this->halt();
        }

        // Validate required service logic
        if ($data['is_required'] && !$data['is_active']) {
            Notification::make()
                ->warning()
                ->title(__('extra-service.notifications.auto_activation_title'))
                ->body(__('extra-service.notifications.auto_activation_body'))
                ->send();

            $data['is_active'] = true;
        }

        // Check if hall changed
        if ($data['hall_id'] !== $this->record->hall_id) {
            Notification::make()
                ->warning()
                ->title(__('extra-service.notifications.hall_changed_title'))
                ->body(__('extra-service.notifications.hall_changed_body'))
                ->send();
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $oldValues = $record->toArray();

        $record->update($data);

        //$changes = array_diff_assoc($data, $oldValues);

        // Log the update
        // activity()
        //     ->performedOn($record)
        //     ->causedBy(Auth::user())
        //     ->withProperties([
        //         'old' => $oldValues,
        //         'changes' => $changes,
        //     ])
        //     ->log('Extra service updated');

        return $record;
    }

    protected function afterSave(): void
    {
        // Clear cache for both old and new hall if hall changed
        $oldHallId = $this->record->getOriginal('hall_id');
        $newHallId = $this->record->hall_id;

        //Cache::tags(['services'])->flush();

        // if ($oldHallId) {
        //     Cache::tags(['hall_' . $oldHallId])->flush();
        // }

        // if ($newHallId && $newHallId !== $oldHallId) {
        //     Cache::tags(['hall_' . $newHallId])->flush();
        // }

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

    // protected function getRevenueStats(): array
    // {
    //     // Placeholder - implement based on your booking structure
    //     return [
    //         'total_bookings' => 0,
    //         'total_revenue' => 0,
    //         'average_quantity' => 0,
    //         'most_booked_month' => null,
    //     ];
    // }

    protected function getRevenueStats(): array
    {
        $bookings = \App\Models\Booking::whereHas('extraServices', function ($q) {
            $q->where('extra_service_id', $this->record->id);
        })
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->with('extraServices')
            ->get();

        $totalRevenue = 0;
        $totalQuantity = 0;
        $monthlyData = [];

        foreach ($bookings as $booking) {
            $service = $booking->extraServices->firstWhere('id', $this->record->id);
            if ($service) {
                $totalRevenue += $service->pivot->total_price ?? 0;
                $totalQuantity += $service->pivot->quantity ?? 0;

                $month = $booking->booking_date->format('Y-m');
                $monthlyData[$month] = ($monthlyData[$month] ?? 0) + 1;
            }
        }

        $mostBookedMonth = !empty($monthlyData)
            ? array_search(max($monthlyData), $monthlyData)
            : null;

        return [
            'total_bookings' => $bookings->count(),
            'total_revenue' => $totalRevenue,
            'average_quantity' => $totalQuantity > 0 ? $totalQuantity / $bookings->count() : 0,
            'most_booked_month' => $mostBookedMonth ? date('F Y', strtotime($mostBookedMonth . '-01')) : null,
        ];
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
        return __('extra-service.page_titles.edit', ['name' => $this->record->name]);
    }

    public function getSubheading(): ?string
    {
        $hall = $this->record->hall->name ?? __('extra-service.unknown_city');
        $price = number_format($this->record->price, 3) . ' OMR';
        $unit = match ($this->record->unit) {
            'per_person' => __('extra-service.infolist.unit_per_person'),
            'per_item'   => __('extra-service.infolist.unit_per_item'),
            'per_hour'   => __('extra-service.infolist.unit_per_hour'),
            'fixed'      => __('extra-service.infolist.unit_fixed'),
            default      => ucfirst(str_replace('_', ' ', $this->record->unit)),
        };
        $status = $this->record->is_active ? __('extra-service.status.active') : __('extra-service.status.inactive');
        $required = $this->record->is_required ? '• ' . __('extra-service.status.required') : '';

        return "{$hall} • {$status} {$required} • {$price} / {$unit}";
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
