<?php

namespace App\Filament\Admin\Resources\HallAvailabilityResource\Pages;

use App\Filament\Admin\Resources\HallAvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
                ->label(fn () => $this->record->is_available ? 'Block Slot' : 'Unblock Slot')
                ->icon(fn () => $this->record->is_available ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                ->color(fn () => $this->record->is_available ? 'danger' : 'success')
                ->requiresConfirmation()
                ->action(function () {
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
                        ->send();
                    
                    Cache::tags(['availability', 'hall_' . $this->record->hall_id])->flush();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),
            
            Actions\Action::make('viewHall')
                ->label('View Hall')
                ->icon('heroicon-o-building-storefront')
                ->color('info')
                ->url(fn () => route('filament.admin.resources.halls.view', [
                    'record' => $this->record->hall_id
                ])),
            
            Actions\Action::make('viewBookings')
                ->label('View Bookings')
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->url(fn () => route('filament.admin.resources.bookings.index', [
                    'tableFilters' => [
                        'hall_id' => ['value' => $this->record->hall_id],
                        'date' => ['value' => $this->record->date->format('Y-m-d')],
                    ]
                ])),
            
            Actions\Action::make('viewRelatedSlots')
                ->label('View Same Day Slots')
                ->icon('heroicon-o-squares-2x2')
                ->color('gray')
                ->modalHeading('All Slots for ' . $this->record->date->format('d M Y'))
                ->modalContent(fn () => view('filament.pages.related-slots', [
                    'slots' => \App\Models\HallAvailability::where('hall_id', $this->record->hall_id)
                        ->where('date', $this->record->date)
                        ->orderBy('time_slot')
                        ->get()
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),
            
            Actions\Action::make('duplicate')
                ->label('Duplicate')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function () {
                    $newAvailability = $this->record->replicate();
                    $newAvailability->date = $this->record->date->addDay();
                    $newAvailability->save();
                    
                    Notification::make()
                        ->success()
                        ->title('Availability Duplicated')
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
                Infolists\Components\Section::make('Slot Information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('hall.name')
                                    ->label('Hall')
                                    ->badge()
                                    ->color('success')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->icon('heroicon-o-server')
                    ->collapsed(),
                
                Infolists\Components\Section::make('Activity History')
                    ->schema([
                        Infolists\Components\ViewEntry::make('activity_log')
                            ->label('')
                            ->view('filament.infolists.components.activity-log', [
                                'activities' => fn ($record) => activity()
                                    ->forSubject($record)
                                    ->latest()
                                    ->limit(10)
                                    ->get()
                            ]),
                    ])
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->visible(fn () => class_exists(\Spatie\Activitylog\Models\Activity::class)),
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
        $status = $this->record->is_available ? 'Available' : 'Blocked';
        
        return "{$date} • {$timeSlot} • {$status}";
    }

    protected function getTotalBookings($record): int
    {
        // Implement based on your booking structure
        // return \App\Models\Booking::where('hall_id', $record->hall_id)
        //     ->whereDate('booking_date', $record->date)
        //     ->where('time_slot', $record->time_slot)
        //     ->count();
        
        return 0;
    }

    protected function getConfirmedBookings($record): int
    {
        // Implement based on your booking structure
        // return \App\Models\Booking::where('hall_id', $record->hall_id)
        //     ->whereDate('booking_date', $record->date)
        //     ->where('time_slot', $record->time_slot)
        //     ->where('status', 'confirmed')
        //     ->count();
        
        return 0;
    }

    protected function getPendingBookings($record): int
    {
        // Implement based on your booking structure
        // return \App\Models\Booking::where('hall_id', $record->hall_id)
        //     ->whereDate('booking_date', $record->date)
        //     ->where('time_slot', $record->time_slot)
        //     ->where('status', 'pending')
        //     ->count();
        
        return 0;
    }

    protected function getPotentialRevenue($record): float
    {
        // Implement based on your booking structure
        // $bookingsCount = $this->getTotalBookings($record);
        // return $bookingsCount * $record->getEffectivePrice();
        
        return 0.000;
    }

    public function getBreadcrumb(): string
    {
        return $this->record->date->format('d M Y') . ' - ' . ucfirst($this->record->time_slot);
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
