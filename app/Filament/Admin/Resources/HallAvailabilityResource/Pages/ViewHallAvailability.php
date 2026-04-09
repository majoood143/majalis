<?php

namespace App\Filament\Admin\Resources\HallAvailabilityResource\Pages;

use App\Filament\Admin\Resources\HallAvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class ViewHallAvailability extends ViewRecord
{
    protected static string $resource = HallAvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('primary'),

            Actions\Action::make('toggleAvailability')
                ->label(fn() => $this->record->is_available
                    ? __('hall-availability.view_page.block_slot')
                    : __('hall-availability.view_page.unblock_slot'))
                ->icon(fn() => $this->record->is_available ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                ->color(fn() => $this->record->is_available ? 'danger' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn() => $this->record->is_available
                    ? __('hall-availability.view_page.block_modal_heading')
                    : __('hall-availability.view_page.unblock_modal_heading'))
                ->modalDescription(fn() => $this->record->is_available
                    ? __('hall-availability.view_page.block_modal_description')
                    : __('hall-availability.view_page.unblock_modal_description'))
                ->action(function () {
                    $wasAvailable = $this->record->is_available;
                    $this->record->is_available = !$this->record->is_available;

                    if (!$this->record->is_available && !$this->record->reason) {
                        $this->record->reason = 'blocked';
                    } elseif ($this->record->is_available) {
                        $this->record->reason = null;
                        $this->record->notes = null;
                    }

                    $this->record->save();

                    Notification::make()
                        ->success()
                        ->title(__('hall-availability.notifications.availability_updated'))
                        ->body($this->record->is_available
                            ? __('hall-availability.notifications.slot_now_available')
                            : __('hall-availability.notifications.slot_blocked'))
                        ->send();

                    Cache::tags(['availability', 'hall_' . $this->record->hall_id])->flush();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('viewHall')
                ->label(__('hall-availability.view_page.view_hall'))
                ->icon('heroicon-o-building-storefront')
                ->color('info')
                ->url(fn() => route('filament.admin.resources.halls.view', [
                    'record' => $this->record->hall_id
                ])),

            Actions\Action::make('viewBookings')
                ->label(__('hall-availability.view_page.view_bookings'))
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->url(fn() => route('filament.admin.resources.bookings.index', [
                    'tableFilters' => [
                        'hall_id' => ['value' => $this->record->hall_id],
                        'date' => ['value' => $this->record->date->format('Y-m-d')],
                    ]
                ]))
                ->badge(fn() => $this->getBookingsCount())
                ->badgeColor('warning'),

            Actions\Action::make('duplicate')
                ->label(__('hall-availability.view_page.duplicate'))
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading(__('hall-availability.view_page.duplicate_heading'))
                ->modalDescription(__('hall-availability.view_page.duplicate_description'))
                ->action(function () {
                    $newAvailability = $this->record->replicate();
                    $newAvailability->date = $this->record->date->addDay();
                    $newAvailability->save();

                    Notification::make()
                        ->success()
                        ->title(__('hall-availability.notifications.duplicated'))
                        ->body(__('hall-availability.notifications.duplicated_body', [
                            'date' => $newAvailability->date->format('d M Y'),
                        ]))
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label(__('hall-availability.view_page.view_duplicate'))
                                ->url(HallAvailabilityResource::getUrl('view', ['record' => $newAvailability->id])),
                        ])
                        ->send();
                }),

            Actions\DeleteAction::make()
                ->successRedirectUrl(route('filament.admin.resources.hall-availabilities.index')),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Slot Information Section
                Infolists\Components\Section::make(__('hall-availability.view_page.slot_information'))
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('hall.name')
                                    ->label(__('hall-availability.view_page.hall_label'))
                                    ->formatStateUsing(fn($record) => $record->hall->name)
                                    ->badge()
                                    ->color('success')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->icon('heroicon-o-building-storefront')
                                    ->url(fn($record) => route('filament.admin.resources.halls.view', ['record' => $record->hall_id]))
                                    ->openUrlInNewTab(),

                                Infolists\Components\TextEntry::make('date')
                                    ->label(__('hall-availability.view_page.date_label'))
                                    ->date('l, d F Y')
                                    ->badge()
                                    ->color(fn($record) => $record->date->isPast() ? 'gray' : 'primary')
                                    ->icon('heroicon-o-calendar')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),

                                Infolists\Components\TextEntry::make('time_slot')
                                    ->label(__('hall-availability.view_page.time_slot_label'))
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'morning' => __('hall-availability.view_page.time_slot_morning'),
                                        'afternoon' => __('hall-availability.view_page.time_slot_afternoon'),
                                        'evening' => __('hall-availability.view_page.time_slot_evening'),
                                        'full_day' => __('hall-availability.view_page.time_slot_full_day'),
                                        default => ucfirst(str_replace('_', ' ', $state)),
                                    })
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-clock')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                            ]),
                    ])
                    ->icon('heroicon-o-information-circle')
                    ->collapsible(),

                // Status & Availability Section
                Infolists\Components\Section::make(__('hall-availability.view_page.status_availability'))
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('is_available')
                                    ->label(__('hall-availability.view_page.availability_status'))
                                    ->formatStateUsing(fn($state) => $state
                                        ? __('hall-availability.status.available')
                                        : __('hall-availability.status.blocked'))
                                    ->badge()
                                    ->color(fn($state) => $state ? 'success' : 'danger')
                                    ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),

                                Infolists\Components\TextEntry::make('reason')
                                    ->label(__('hall-availability.view_page.block_reason'))
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'maintenance' => __('hall-availability.view_page.reason_maintenance'),
                                        'blocked' => __('hall-availability.view_page.reason_blocked'),
                                        'custom' => __('hall-availability.view_page.reason_custom'),
                                        'holiday' => __('hall-availability.view_page.reason_holiday'),
                                        null => __('hall-availability.view_page.reason_na'),
                                        default => ucfirst($state),
                                    })
                                    ->badge()
                                    ->color(fn($state) => match ($state) {
                                        'maintenance' => 'warning',
                                        'blocked' => 'danger',
                                        'holiday' => 'info',
                                        null => 'gray',
                                        default => 'gray',
                                    })
                                    ->icon('heroicon-o-information-circle')
                                    ->visible(fn($record) => !$record->is_available),

                                Infolists\Components\TextEntry::make('bookings_count')
                                    ->label(__('hall-availability.view_page.active_bookings'))
                                    ->state(fn($record) => $this->getBookingsCount())
                                    ->badge()
                                    ->color(fn($state) => $state > 0 ? 'warning' : 'gray')
                                    ->icon('heroicon-o-calendar-days'),
                            ]),

                        Infolists\Components\TextEntry::make('notes')
                            ->label(__('hall-availability.view_page.notes_label'))
                            ->columnSpanFull()
                            ->placeholder(__('hall-availability.view_page.no_notes'))
                            ->visible(fn($record) => !empty($record->notes)),
                    ])
                    ->icon('heroicon-o-signal')
                    ->collapsible(),

                // Pricing Information Section
                Infolists\Components\Section::make(__('hall-availability.view_page.pricing_information'))
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('custom_price')
                                    ->label(__('hall-availability.view_page.custom_price_label'))
                                    ->money('OMR', locale: 'en_OM')
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->placeholder(__('hall-availability.view_page.using_default_price'))
                                    ->visible(fn($record) => $record->custom_price !== null),

                                Infolists\Components\TextEntry::make('default_price')
                                    ->label(__('hall-availability.view_page.default_hall_price'))
                                    ->state(fn($record) => $this->getDefaultPrice())
                                    ->money('OMR', locale: 'en_OM')
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-banknotes'),

                                Infolists\Components\TextEntry::make('effective_price')
                                    ->label(__('hall-availability.view_page.effective_price_label'))
                                    ->state(fn($record) => $record->custom_price ?? $this->getDefaultPrice())
                                    ->money('OMR', locale: 'en_OM')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-check-badge')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                            ]),
                    ])
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsible(),

                // Hall Details Section
                Infolists\Components\Section::make(__('hall-availability.view_page.hall_details'))
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('hall.city.name')
                                    ->label(__('hall-availability.view_page.city'))
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-map-pin'),

                                Infolists\Components\TextEntry::make('hall.owner.name')
                                    ->label(__('hall-availability.view_page.hall_owner'))
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-o-user'),

                                Infolists\Components\TextEntry::make('hall.capacity_max')
                                    ->label(__('hall-availability.view_page.hall_capacity'))
                                    ->suffix(__('hall-availability.status.guests_suffix'))
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-users'),
                            ]),
                    ])
                    ->icon('heroicon-o-building-storefront')
                    ->collapsed(),

                // Statistics & Insights Section
                Infolists\Components\Section::make(__('hall-availability.view_page.statistics_insights'))
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('days_until')
                                    ->label(__('hall-availability.view_page.days_until'))
                                    ->state(fn($record) => max(0, $record->date->diffInDays(now(), false) * -1))
                                    ->suffix(__('hall-availability.status.days_suffix'))
                                    ->badge()
                                    ->color(fn($state) => match (true) {
                                        $state < 0 => 'gray',
                                        $state <= 7 => 'danger',
                                        $state <= 30 => 'warning',
                                        default => 'success',
                                    })
                                    ->icon('heroicon-o-clock'),

                                Infolists\Components\TextEntry::make('same_day_slots')
                                    ->label(__('hall-availability.view_page.same_day_slots'))
                                    ->state(fn($record) => $this->getSameDaySlotsCount())
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-squares-2x2'),

                                Infolists\Components\TextEntry::make('day_of_week')
                                    ->label(__('hall-availability.view_page.day_of_week'))
                                    ->state(fn($record) => $record->date->format('l'))
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-o-calendar-days'),

                                Infolists\Components\TextEntry::make('week_number')
                                    ->label(__('hall-availability.view_page.week_number'))
                                    ->state(fn($record) => __('hall-availability.status.week_prefix') . $record->date->weekOfYear)
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-calendar'),
                            ]),
                    ])
                    ->icon('heroicon-o-chart-bar')
                    ->collapsed(),

                // System Information Section
                Infolists\Components\Section::make(__('hall-availability.view_page.system_information'))
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('id')
                                    ->label(__('hall-availability.view_page.availability_id'))
                                    ->badge()
                                    ->color('gray')
                                    ->copyable()
                                    ->icon('heroicon-o-hashtag'),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('hall-availability.view_page.created_at'))
                                    ->dateTime('d M Y, h:i A')
                                    ->icon('heroicon-o-calendar')
                                    ->since()
                                    ->tooltip(fn($record) => $record->created_at->format('d M Y, h:i A')),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label(__('hall-availability.view_page.last_updated'))
                                    ->dateTime('d M Y, h:i A')
                                    ->since()
                                    ->icon('heroicon-o-clock')
                                    ->tooltip(fn($record) => $record->updated_at->format('d M Y, h:i A')),
                            ]),
                    ])
                    ->icon('heroicon-o-server')
                    ->collapsed(),
            ]);
    }

    public function getTitle(): string
    {
        return __('hall-availability.view_page.title') . ': ' . $this->record->hall->name;
    }

    public function getSubheading(): ?string
    {
        $date = $this->record->date->format('l, d F Y');
        $timeSlot = __('hall-availability.time_slots_short.' . $this->record->time_slot)
            ?: ucfirst($this->record->time_slot);
        $status = $this->record->is_available
            ? __('hall-availability.view_page.available_status')
            : __('hall-availability.view_page.blocked_status');

        return "{$date} • {$timeSlot} • {$status}";
    }

    public function getBreadcrumb(): string
    {
        $timeSlot = __('hall-availability.time_slots_short.' . $this->record->time_slot)
            ?: ucfirst($this->record->time_slot);

        return $this->record->date->format('d M Y') . ' - ' . $timeSlot;
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    // Helper Methods
    protected function getBookingsCount(): int
    {
        return \App\Models\Booking::where('hall_id', $this->record->hall_id)
            ->whereDate('booking_date', $this->record->date)
            ->where('time_slot', $this->record->time_slot)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();
    }

    protected function getDefaultPrice(): float
    {
        if ($this->record->hall && method_exists($this->record->hall, 'getPriceForSlot')) {
            return $this->record->hall->getPriceForSlot($this->record->time_slot);
        }

        return $this->record->hall->price_per_slot ?? 0.000;
    }

    protected function getSameDaySlotsCount(): int
    {
        return \App\Models\HallAvailability::where('hall_id', $this->record->hall_id)
            ->where('date', $this->record->date)
            ->count();
    }
}
