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
                ->label(fn() => $this->record->is_active ? __('extra-service.page_actions.deactivate') : __('extra-service.page_actions.activate'))
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn() => $this->record->is_active ? 'warning' : 'success')
                ->requiresConfirmation()
                ->action(function () {
                    if ($this->record->is_active && $this->record->is_required) {
                        Notification::make()
                            ->danger()
                            ->title(__('extra-service.notifications.cannot_deactivate_title'))
                            ->body(__('extra-service.notifications.cannot_deactivate_body_short'))
                            ->send();
                        return;
                    }

                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();

                    Notification::make()
                        ->success()
                        ->title(__('extra-service.notifications.status_updated_title'))
                        ->send();

                    Cache::tags(['services', 'hall_' . $this->record->hall_id])->flush();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('toggleRequired')
                ->label(fn() => $this->record->is_required ? __('extra-service.page_actions.make_optional') : __('extra-service.page_actions.make_required'))
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
                        ->title(__('extra-service.notifications.requirement_updated_title'))
                        ->send();

                    Cache::tags(['services', 'hall_' . $this->record->hall_id])->flush();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('viewHall')
                ->label(__('extra-service.page_actions.view_hall'))
                ->icon('heroicon-o-building-storefront')
                ->color('info')
                ->url(fn() => route('filament.admin.resources.halls.view', [
                    'record' => $this->record->hall_id
                ])),

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
                ->label(__('extra-service.page_actions.revenue_analysis'))
                ->icon('heroicon-o-chart-bar')
                ->color('success')
                ->modalHeading(__('extra-service.page_actions.service_revenue_analysis_heading'))
                ->modalContent(fn() => view('filament.pages.service-revenue-analysis', [
                    'service' => $this->record,
                    'stats' => $this->getRevenueStats(),
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel(__('extra-service.page_actions.close')),

            Actions\Action::make('duplicate')
                ->label(__('extra-service.page_actions.duplicate'))
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
                        ->title(__('extra-service.notifications.service_duplicated_title'))
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label(__('extra-service.page_actions.view_duplicate'))
                                ->url(ExtraServiceResource::getUrl('view', ['record' => $newService->id])),
                        ])
                        ->send();
                }),

            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
                    if ($this->record->is_required) {
                        Notification::make()
                            ->danger()
                            ->title(__('extra-service.notifications.cannot_delete_title'))
                            ->body(__('extra-service.notifications.cannot_delete_body'))
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
                Infolists\Components\Section::make(__('extra-service.infolist.service_overview'))
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
                                    ->label(__('extra-service.infolist.service_name'))
                                    ->formatStateUsing(fn($record) => $record->name)
                                    ->badge()
                                    ->color('primary')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->icon('heroicon-o-gift'),

                                Infolists\Components\TextEntry::make('hall.name')
                                    ->label(__('extra-service.infolist.hall'))
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-building-storefront'),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('name.en')
                                    ->label(__('extra-service.infolist.name_en'))
                                    ->icon('heroicon-o-language'),

                                Infolists\Components\TextEntry::make('name.ar')
                                    ->label(__('extra-service.infolist.name_ar'))
                                    ->icon('heroicon-o-language'),
                            ]),
                    ])
                    ->icon('heroicon-o-information-circle')
                    ->collapsible(),

                Infolists\Components\Section::make(__('extra-service.infolist.description_section'))
                    ->schema([
                        Infolists\Components\TextEntry::make('description.en')
                            ->label(__('extra-service.infolist.description_en'))
                            ->html()
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('description.ar')
                            ->label(__('extra-service.infolist.description_ar'))
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-document-text')
                    ->collapsible(),

                Infolists\Components\Section::make(__('extra-service.infolist.pricing_details'))
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('price')
                                    ->label(__('extra-service.infolist.price'))
                                    ->money('OMR')
                                    ->badge()
                                    ->color('success')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->icon('heroicon-o-currency-dollar')
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('unit')
                                    ->label(__('extra-service.infolist.unit'))
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'per_person' => __('extra-service.infolist.unit_per_person'),
                                        'per_item'   => __('extra-service.infolist.unit_per_item'),
                                        'per_hour'   => __('extra-service.infolist.unit_per_hour'),
                                        'fixed'      => __('extra-service.units.fixed'),
                                        default      => ucfirst($state),
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
                                    ->label(__('extra-service.infolist.min_quantity'))
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-arrow-down-circle'),

                                Infolists\Components\TextEntry::make('maximum_quantity')
                                    ->label(__('extra-service.infolist.max_quantity'))
                                    ->placeholder(__('extra-service.infolist.unlimited'))
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-arrow-up-circle'),
                            ]),

                        Infolists\Components\TextEntry::make('price_range')
                            ->label(__('extra-service.infolist.price_range'))
                            ->state(function ($record) {
                                if ($record->unit === 'fixed') {
                                    return number_format($record->price, 3) . ' OMR (' . __('extra-service.infolist.unit_fixed') . ')';
                                }

                                $min = $record->price * $record->minimum_quantity;
                                $max = $record->maximum_quantity
                                    ? $record->price * $record->maximum_quantity
                                    : __('extra-service.infolist.unlimited');

                                $maxDisplay = is_numeric($max) ? number_format($max, 3) . ' OMR' : $max;

                                return number_format($min, 3) . ' OMR - ' . $maxDisplay;
                            })
                            ->icon('heroicon-o-calculator')
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-banknotes')
                    ->collapsible(),

                Infolists\Components\Section::make(__('extra-service.infolist.service_settings'))
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\IconEntry::make('is_active')
                                    ->label(__('extra-service.infolist.active_status'))
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger')
                                    ->size(Infolists\Components\IconEntry\IconEntrySize::Large),

                                Infolists\Components\IconEntry::make('is_required')
                                    ->label(__('extra-service.infolist.required_service'))
                                    ->boolean()
                                    ->trueIcon('heroicon-o-star')
                                    ->falseIcon('heroicon-o-minus-circle')
                                    ->trueColor('warning')
                                    ->falseColor('gray')
                                    ->size(Infolists\Components\IconEntry\IconEntrySize::Large),

                                Infolists\Components\TextEntry::make('order')
                                    ->label(__('extra-service.infolist.display_order'))
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-bars-3'),

                                Infolists\Components\TextEntry::make('image_status')
                                    ->label(__('extra-service.infolist.image'))
                                    ->state(fn($record) => $record->image ? __('extra-service.infolist.image_available') : __('extra-service.infolist.no_image'))
                                    ->badge()
                                    ->color(fn($record) => $record->image ? 'success' : 'gray')
                                    ->icon(fn($record) => $record->image ? 'heroicon-o-photo' : 'heroicon-o-x-mark'),
                            ]),
                    ])
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible(),

                Infolists\Components\Section::make(__('extra-service.infolist.usage_statistics'))
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('total_bookings')
                                    ->label(__('extra-service.infolist.total_bookings'))
                                    ->state(fn($record) => $this->getTotalBookings($record))
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('total_revenue')
                                    ->label(__('extra-service.infolist.total_revenue'))
                                    ->state(fn($record) => number_format($this->getTotalRevenue($record), 3) . ' OMR')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-banknotes'),

                                Infolists\Components\TextEntry::make('avg_quantity')
                                    ->label(__('extra-service.infolist.avg_quantity'))
                                    ->state(fn($record) => number_format($this->getAverageQuantity($record), 2))
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-chart-bar'),

                                Infolists\Components\TextEntry::make('last_booked')
                                    ->label(__('extra-service.infolist.last_booked'))
                                    ->state(fn($record) => $this->getLastBookedDate($record))
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->icon('heroicon-o-chart-bar-square')
                    ->collapsible(),

                Infolists\Components\Section::make(__('extra-service.infolist.system_information'))
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('id')
                                    ->label(__('extra-service.infolist.service_id'))
                                    ->badge()
                                    ->color('gray')
                                    ->copyable()
                                    ->icon('heroicon-o-hashtag'),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('extra-service.infolist.created_at'))
                                    ->dateTime('d M Y, h:i A')
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label(__('extra-service.infolist.last_updated'))
                                    ->dateTime('d M Y, h:i A')
                                    ->since()
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->icon('heroicon-o-server')
                    ->collapsed(),

                Infolists\Components\Section::make(__('extra-service.infolist.activity_history'))
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
        return __('extra-service.page_titles.view', ['name' => $this->record->name]);
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
            default      => ucfirst($this->record->unit),
        };
        $status = $this->record->is_active ? __('extra-service.status.active') : __('extra-service.status.inactive');
        $required = $this->record->is_required ? '• ' . __('extra-service.status.required') : '';

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
        return __('extra-service.infolist.never');
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
