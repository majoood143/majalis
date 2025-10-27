<?php

namespace App\Filament\Admin\Resources\ExtraServiceResource\Pages;

use App\Filament\Admin\Resources\ExtraServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ViewExtraService extends ViewRecord
{
    protected static string $resource = ExtraServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('primary'),

            Actions\Action::make('toggleActive')
                ->label(fn() => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn() => $this->record->is_active ? 'warning' : 'success')
                ->requiresConfirmation()
                ->action(function () {
                    if ($this->record->is_active && $this->record->is_required) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot Deactivate')
                            ->body('Required services cannot be deactivated.')
                            ->send();
                        return;
                    }

                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();

                    Notification::make()
                        ->success()
                        ->title('Status Updated')
                        ->send();

                    Cache::tags(['services', 'hall_' . $this->record->hall_id])->flush();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('toggleRequired')
                ->label(fn() => $this->record->is_required ? 'Make Optional' : 'Make Required')
                ->icon(fn() => $this->record->is_required ? 'heroicon-o-x-mark' : 'heroicon-o-star')
                ->color(fn() => $this->record->is_required ? 'gray' : 'warning')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->is_required = !$this->record->is_required;

                    if ($this->record->is_required && !$this->record->is_active) {
                        $this->record->is_active = true;
                    }

                    $this->record->save();

                    Notification::make()
                        ->success()
                        ->title('Requirement Status Updated')
                        ->send();

                    Cache::tags(['services', 'hall_' . $this->record->hall_id])->flush();
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
                        'extra_service_id' => ['value' => $this->record->id]
                    ]
                ])),

            Actions\Action::make('calculateRevenue')
                ->label('Revenue Analysis')
                ->icon('heroicon-o-chart-bar')
                ->color('success')
                ->modalHeading('Service Revenue Analysis')
                ->modalContent(fn() => view('filament.pages.service-revenue-analysis', [
                    'service' => $this->record,
                    'stats' => $this->getRevenueStats(),
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),

            Actions\Action::make('duplicate')
                ->label('Duplicate')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function () {
                    $newService = $this->record->replicate();

                    $name = $newService->getTranslations('name');
                    foreach ($name as $locale => $value) {
                        $name[$locale] = $value . ' (Copy)';
                    }
                    $newService->setTranslations('name', $name);

                    $newService->save();

                    Notification::make()
                        ->success()
                        ->title('Service Duplicated')
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label('View Duplicate')
                                ->url(ExtraServiceResource::getUrl('view', ['record' => $newService->id])),
                        ])
                        ->send();
                }),

            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
                    if ($this->record->is_required) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot Delete')
                            ->body('Required services cannot be deleted.')
                            ->persistent()
                            ->send();

                        $action->cancel();
                    }
                })
                ->after(function () {
                    if ($this->record->image) {
                        Storage::disk('public')->delete($this->record->image);
                    }

                    Cache::tags(['services', 'hall_' . $this->record->hall_id])->flush();
                })
                ->successRedirectUrl(route('filament.admin.resources.extra-services.index')),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Service Overview')
                    ->schema([
                        Infolists\Components\ImageEntry::make('image')
                            ->label('')
                            ->disk('public')
                            ->height(200)
                            ->visible(fn($record) => $record->image !== null)
                            ->columnSpanFull(),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Service Name')
                                    ->formatStateUsing(fn($record) => $record->name)
                                    ->badge()
                                    ->color('primary')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->icon('heroicon-o-gift'),

                                Infolists\Components\TextEntry::make('hall.name')
                                    ->label('Hall')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-building-storefront'),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('name.en')
                                    ->label('Name (English)')
                                    ->icon('heroicon-o-language'),

                                Infolists\Components\TextEntry::make('name.ar')
                                    ->label('Name (Arabic)')
                                    ->icon('heroicon-o-language'),
                            ]),
                    ])
                    ->icon('heroicon-o-information-circle')
                    ->collapsible(),

                Infolists\Components\Section::make('Description')
                    ->schema([
                        Infolists\Components\TextEntry::make('description.en')
                            ->label('Description (English)')
                            ->html()
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('description.ar')
                            ->label('Description (Arabic)')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-document-text')
                    ->collapsible(),

                Infolists\Components\Section::make('Pricing Details')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('price')
                                    ->label('Price')
                                    ->money('OMR')
                                    ->badge()
                                    ->color('success')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->icon('heroicon-o-currency-dollar')
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('unit')
                                    ->label('Unit')
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'per_person' => 'Per Person',
                                        'per_item' => 'Per Item',
                                        'per_hour' => 'Per Hour',
                                        'fixed' => 'Fixed Price',
                                        default => ucfirst($state),
                                    })
                                    ->badge()
                                    ->color('info')
                                    ->icon(fn($state) => match ($state) {
                                        'per_person' => 'heroicon-o-user-group',
                                        'per_item' => 'heroicon-o-cube',
                                        'per_hour' => 'heroicon-o-clock',
                                        'fixed' => 'heroicon-o-banknotes',
                                        default => 'heroicon-o-tag',
                                    }),

                                Infolists\Components\TextEntry::make('minimum_quantity')
                                    ->label('Min Quantity')
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-arrow-down-circle'),

                                Infolists\Components\TextEntry::make('maximum_quantity')
                                    ->label('Max Quantity')
                                    ->placeholder('Unlimited')
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-arrow-up-circle'),
                            ]),

                        Infolists\Components\TextEntry::make('price_range')
                            ->label('Price Range')
                            ->state(function ($record) {
                                if ($record->unit === 'fixed') {
                                    return number_format($record->price, 3) . ' OMR (Fixed)';
                                }

                                $min = $record->price * $record->minimum_quantity;
                                $max = $record->maximum_quantity
                                    ? $record->price * $record->maximum_quantity
                                    : 'Unlimited';

                                $maxDisplay = is_numeric($max) ? number_format($max, 3) . ' OMR' : $max;

                                return number_format($min, 3) . ' OMR - ' . $maxDisplay;
                            })
                            ->icon('heroicon-o-calculator')
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-banknotes')
                    ->collapsible(),

                Infolists\Components\Section::make('Service Settings')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\IconEntry::make('is_active')
                                    ->label('Active Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger')
                                    ->size(Infolists\Components\IconEntry\IconEntrySize::Large),

                                Infolists\Components\IconEntry::make('is_required')
                                    ->label('Required Service')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-star')
                                    ->falseIcon('heroicon-o-minus-circle')
                                    ->trueColor('warning')
                                    ->falseColor('gray')
                                    ->size(Infolists\Components\IconEntry\IconEntrySize::Large),

                                Infolists\Components\TextEntry::make('order')
                                    ->label('Display Order')
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-bars-3'),

                                Infolists\Components\TextEntry::make('image_status')
                                    ->label('Image')
                                    ->state(fn($record) => $record->image ? 'Available' : 'No Image')
                                    ->badge()
                                    ->color(fn($record) => $record->image ? 'success' : 'gray')
                                    ->icon(fn($record) => $record->image ? 'heroicon-o-photo' : 'heroicon-o-x-mark'),
                            ]),
                    ])
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible(),

                Infolists\Components\Section::make('Usage Statistics')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('total_bookings')
                                    ->label('Total Bookings')
                                    ->state(fn($record) => $this->getTotalBookings($record))
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('total_revenue')
                                    ->label('Total Revenue')
                                    ->state(fn($record) => number_format($this->getTotalRevenue($record), 3) . ' OMR')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-banknotes'),

                                Infolists\Components\TextEntry::make('avg_quantity')
                                    ->label('Avg Quantity')
                                    ->state(fn($record) => number_format($this->getAverageQuantity($record), 2))
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-chart-bar'),

                                Infolists\Components\TextEntry::make('last_booked')
                                    ->label('Last Booked')
                                    ->state(fn($record) => $this->getLastBookedDate($record))
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->icon('heroicon-o-chart-bar-square')
                    ->collapsible(),

                Infolists\Components\Section::make('System Information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('id')
                                    ->label('Service ID')
                                    ->badge()
                                    ->color('gray')
                                    ->copyable()
                                    ->icon('heroicon-o-hashtag'),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime('d M Y, h:i A')
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime('d M Y, h:i A')
                                    ->since()
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
                                'activities' => fn($record) => activity()
                                    ->forSubject($record)
                                    ->latest()
                                    ->limit(10)
                                    ->get()
                            ]),
                    ])
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->visible(fn() => class_exists(\Spatie\Activitylog\Models\Activity::class)),
            ]);
    }

    public function getTitle(): string
    {
        return 'View Service: ' . $this->record->name;
    }

    public function getSubheading(): ?string
    {
        $hall = $this->record->hall->name ?? 'Unknown Hall';
        $price = number_format($this->record->price, 3) . ' OMR';
        $unit = match ($this->record->unit) {
            'per_person' => 'Per Person',
            'per_item' => 'Per Item',
            'per_hour' => 'Per Hour',
            'fixed' => 'Fixed',
            default => ucfirst($this->record->unit),
        };
        $status = $this->record->is_active ? 'Active' : 'Inactive';
        $required = $this->record->is_required ? '• Required' : '';

        return "{$hall} • {$status} {$required} • {$price} / {$unit}";
    }

    // protected function getRevenueStats(): array
    // {
    //     return [
    //         'total_bookings' => $this->getTotalBookings($this->record),
    //         'total_revenue' => $this->getTotalRevenue($this->record),
    //         'average_quantity' => $this->getAverageQuantity($this->record),
    //         'last_booked' => $this->getLastBookedDate($this->record),
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

    protected function getTotalBookings($record): int
    {
        // Implement based on your booking structure
        return 0;
    }

    protected function getTotalRevenue($record): float
    {
        // Implement based on your payment structure
        return 0.000;
    }

    protected function getAverageQuantity($record): float
    {
        // Implement based on your booking structure
        return 0.00;
    }

    protected function getLastBookedDate($record): string
    {
        // Implement based on your booking structure
        return 'Never';
    }

    public function getBreadcrumb(): string
    {
        return $this->record->name;
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
