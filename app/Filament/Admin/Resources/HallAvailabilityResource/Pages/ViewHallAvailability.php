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
                ->label(fn() => $this->record->is_available ? 'Block Slot' : 'Unblock Slot')
                ->icon(fn() => $this->record->is_available ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                ->color(fn() => $this->record->is_available ? 'danger' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn() => $this->record->is_available ? 'Block This Slot?' : 'Unblock This Slot?')
                ->modalDescription(fn() => $this->record->is_available
                    ? 'This will prevent new bookings for this time slot.'
                    : 'This will make the slot available for bookings again.')
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
                        ->title('Availability Updated')
                        ->body($this->record->is_available ? 'Slot is now available for bookings.' : 'Slot has been blocked.')
                        ->send();

                    Cache::tags(['availability', 'hall_' . $this->record->hall_id])->flush();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('viewHall')
                ->label('View Hall')
                ->icon('heroicon-o-building-storefront')
                ->color('info')
                ->url(fn() => route('filament.admin.resources.halls.view', [
                    'record' => $this->record->hall_id
                ])),

            Actions\Action::make('viewBookings')
                ->label('View Bookings')
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
                ->label('Duplicate')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Duplicate Availability')
                ->modalDescription('Create a copy of this availability for the next day.')
                ->action(function () {
                    $newAvailability = $this->record->replicate();
                    $newAvailability->date = $this->record->date->addDay();
                    $newAvailability->save();

                    Notification::make()
                        ->success()
                        ->title('Availability Duplicated')
                        ->body('A new availability has been created for ' . $newAvailability->date->format('d M Y'))
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label('View Duplicate')
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
                Infolists\Components\Section::make('Slot Information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('hall.name')
                                    ->label('Hall')
                                    ->formatStateUsing(fn($record) => $record->hall->name)
                                    ->badge()
                                    ->color('success')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->icon('heroicon-o-building-storefront')
                                    ->url(fn($record) => route('filament.admin.resources.halls.view', ['record' => $record->hall_id]))
                                    ->openUrlInNewTab(),

                                Infolists\Components\TextEntry::make('date')
                                    ->label('Date')
                                    ->date('l, d F Y')
                                    ->badge()
                                    ->color(fn($record) => $record->date->isPast() ? 'gray' : 'primary')
                                    ->icon('heroicon-o-calendar')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),

                                Infolists\Components\TextEntry::make('time_slot')
                                    ->label('Time Slot')
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'morning' => 'Morning',
                                        'afternoon' => 'Afternoon',
                                        'evening' => 'Evening',
                                        'full_day' => 'Full Day',
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
                Infolists\Components\Section::make('Status & Availability')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('is_available')
                                    ->label('Availability Status')
                                    ->formatStateUsing(fn($state) => $state ? 'Available' : 'Blocked')
                                    ->badge()
                                    ->color(fn($state) => $state ? 'success' : 'danger')
                                    ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),

                                Infolists\Components\TextEntry::make('reason')
                                    ->label('Block Reason')
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'maintenance' => 'Under Maintenance',
                                        'blocked' => 'Blocked by Owner',
                                        'custom' => 'Custom Block',
                                        'holiday' => 'Holiday',
                                        null => 'N/A',
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
                                    ->label('Active Bookings')
                                    ->state(fn($record) => $this->getBookingsCount())
                                    ->badge()
                                    ->color(fn($state) => $state > 0 ? 'warning' : 'gray')
                                    ->icon('heroicon-o-calendar-days'),
                            ]),

                        Infolists\Components\TextEntry::make('notes')
                            ->label('Notes')
                            ->columnSpanFull()
                            ->placeholder('No additional notes')
                            ->visible(fn($record) => !empty($record->notes)),
                    ])
                    ->icon('heroicon-o-signal')
                    ->collapsible(),

                // Pricing Information Section
                Infolists\Components\Section::make('Pricing Information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('custom_price')
                                    ->label('Custom Price')
                                    ->money('OMR', locale: 'en_OM')
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->placeholder('Using Default Price')
                                    ->visible(fn($record) => $record->custom_price !== null),

                                Infolists\Components\TextEntry::make('default_price')
                                    ->label('Default Hall Price')
                                    ->state(fn($record) => $this->getDefaultPrice())
                                    ->money('OMR', locale: 'en_OM')
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-banknotes'),

                                Infolists\Components\TextEntry::make('effective_price')
                                    ->label('Effective Price')
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
                Infolists\Components\Section::make('Hall Details')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('hall.city.name')
                                    ->label('City')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-map-pin'),

                                Infolists\Components\TextEntry::make('hall.owner.name')
                                    ->label('Hall Owner')
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-o-user'),

                                Infolists\Components\TextEntry::make('hall.capacity_max')
                                    ->label('Hall Capacity')
                                    ->suffix(' guests')
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-users'),
                            ]),
                    ])
                    ->icon('heroicon-o-building-storefront')
                    ->collapsed(),

                // Statistics & Insights Section
                Infolists\Components\Section::make('Statistics & Insights')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('days_until')
                                    ->label('Days Until')
                                    ->state(fn($record) => max(0, $record->date->diffInDays(now(), false) * -1))
                                    ->suffix(' days')
                                    ->badge()
                                    ->color(fn($state) => match (true) {
                                        $state < 0 => 'gray',
                                        $state <= 7 => 'danger',
                                        $state <= 30 => 'warning',
                                        default => 'success',
                                    })
                                    ->icon('heroicon-o-clock'),

                                Infolists\Components\TextEntry::make('same_day_slots')
                                    ->label('Same Day Slots')
                                    ->state(fn($record) => $this->getSameDaySlotsCount())
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-squares-2x2'),

                                Infolists\Components\TextEntry::make('day_of_week')
                                    ->label('Day of Week')
                                    ->state(fn($record) => $record->date->format('l'))
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-o-calendar-days'),

                                Infolists\Components\TextEntry::make('week_number')
                                    ->label('Week Number')
                                    ->state(fn($record) => 'Week ' . $record->date->weekOfYear)
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-calendar'),
                            ]),
                    ])
                    ->icon('heroicon-o-chart-bar')
                    ->collapsed(),

                // System Information Section
                Infolists\Components\Section::make('System Information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('id')
                                    ->label('Availability ID')
                                    ->badge()
                                    ->color('gray')
                                    ->copyable()
                                    ->icon('heroicon-o-hashtag'),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime('d M Y, h:i A')
                                    ->icon('heroicon-o-calendar')
                                    ->since()
                                    ->tooltip(fn($record) => $record->created_at->format('d M Y, h:i A')),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Last Updated')
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
        return 'View Availability: ' . $this->record->hall->name;
    }

    public function getSubheading(): ?string
    {
        $date = $this->record->date->format('l, d F Y');
        $timeSlot = match ($this->record->time_slot) {
            'morning' => 'Morning',
            'afternoon' => 'Afternoon',
            'evening' => 'Evening',
            'full_day' => 'Full Day',
            default => ucfirst($this->record->time_slot),
        };
        $status = $this->record->is_available ? '✓ Available' : '✗ Blocked';

        return "{$date} • {$timeSlot} • {$status}";
    }

    public function getBreadcrumb(): string
    {
        return $this->record->date->format('d M Y') . ' - ' . ucfirst($this->record->time_slot);
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
