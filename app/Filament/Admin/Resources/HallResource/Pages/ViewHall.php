<?php

namespace App\Filament\Admin\Resources\HallResource\Pages;

use App\Filament\Admin\Resources\HallResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ViewHall extends ViewRecord
{
    protected static string $resource = HallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('primary'),

            Actions\Action::make('toggleActive')
                ->label(fn() => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn() => $this->record->is_active ? 'danger' : 'success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();

                    Notification::make()->success()->title('Status Updated')->send();
                    Cache::tags(['halls'])->flush();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('viewBookings')
                ->label('Bookings')
                ->icon('heroicon-o-calendar-days')
                ->badge(fn() => $this->record->bookings()->count())
                ->url(fn() => route('filament.admin.resources.bookings.index', [
                    'tableFilters' => ['hall_id' => ['value' => $this->record->id]]
                ])),

            Actions\Action::make('viewLocation')
                ->label('View on Map')
                ->icon('heroicon-o-map-pin')
                ->color('success')
                ->url(fn() => $this->record->google_maps_url ?: "https://www.google.com/maps/search/?api=1&query={$this->record->latitude},{$this->record->longitude}")
                ->openUrlInNewTab()
                ->visible(fn() => $this->record->latitude && $this->record->longitude),

            Actions\DeleteAction::make()
                ->successRedirectUrl(route('filament.admin.resources.halls.index')),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Hall Overview')
                    ->schema([
                        Infolists\Components\ImageEntry::make('featured_image')
                            ->label('')
                            ->disk('public')
                            ->height(300)
                            ->columnSpanFull()
                            ->visible(fn($record) => $record->featured_image),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->formatStateUsing(fn($record) => $record->name)
                                    ->badge()
                                    ->color('primary')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->icon('heroicon-o-building-office-2'),

                                Infolists\Components\TextEntry::make('city.name')
                                    ->formatStateUsing(fn($record) => $record->city->name)
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-map-pin'),

                                Infolists\Components\TextEntry::make('owner.name')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-user'),
                            ]),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('Description')
                    ->schema([
                        Infolists\Components\TextEntry::make('description_en')
                            ->label('Description (English)')
                            ->html()
                    
                    ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('description_ar')
                            ->label('Description (Arabic)')
                            ->html()
                    
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-document-text')
                    ->collapsible(),

                Infolists\Components\Section::make('Capacity & Pricing')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('capacity_min')
                                    ->label('Min Capacity')
                                    ->suffix(' guests')
                                    ->badge()
                                    ->color('info'),

                                Infolists\Components\TextEntry::make('capacity_max')
                                    ->label('Max Capacity')
                                    ->suffix(' guests')
                                    ->badge()
                                    ->color('success')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),

                                Infolists\Components\TextEntry::make('price_per_slot')
                                    ->label('Base Price')
                                    ->money('OMR')
                                    ->badge()
                                    ->color('warning')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),

                                Infolists\Components\TextEntry::make('average_rating')
                                    ->label('Rating')
                                    ->badge()
                                    ->color('warning')
                                    ->suffix('/5')
                                    ->icon('heroicon-o-star'),
                            ]),
                    ])
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsible(),

                Infolists\Components\Section::make('Contact Information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('phone')
                                    ->icon('heroicon-o-phone')
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('whatsapp')
                                    ->icon('heroicon-o-device-phone-mobile')
                                    ->copyable()
                                    ->placeholder('Not provided'),

                                Infolists\Components\TextEntry::make('email')
                                    ->icon('heroicon-o-envelope')
                                    ->copyable()
                                    ->placeholder('Not provided'),
                            ]),
                    ])
                    ->icon('heroicon-o-phone')
                    ->collapsible(),

                Infolists\Components\Section::make('Location')
                    ->schema([
                        Infolists\Components\TextEntry::make('address')
                            ->columnSpanFull(),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('latitude')
                                    ->copyable()
                                    ->placeholder('Not set'),

                                Infolists\Components\TextEntry::make('longitude')
                                    ->copyable()
                                    ->placeholder('Not set'),
                            ]),
                    ])
                    ->icon('heroicon-o-map-pin')
                    ->collapsible(),

                Infolists\Components\Section::make('Features')
                    ->schema([
                        Infolists\Components\TextEntry::make('features')
                            ->label('')
                            ->state(function ($record) {
                    return collect($record->features)->map(fn($feature) => $feature->name)->implode(', ') ?: 'No features added';
                                //return $record->features->map(fn($feature) => $feature->name)->implode(', ') ?: 'No features added';
                            })
                            ->badge()
                            ->separator(',')
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-star')
                    ->collapsible(),

                Infolists\Components\Section::make('Statistics')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('total_bookings')
                                    ->label('Total Bookings')
                                    ->state(fn($record) => $record->bookings()->count())
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('confirmed_bookings')
                                    ->label('Confirmed')
                                    ->state(fn($record) => $record->bookings()->where('status', 'confirmed')->count())
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-check-circle'),

                                Infolists\Components\TextEntry::make('total_revenue')
                                    ->label('Total Revenue')
                                    ->state(fn($record) => number_format($this->getTotalRevenue($record), 3) . ' OMR')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-banknotes'),

                                Infolists\Components\TextEntry::make('reviews_count')
                                    ->label('Reviews')
                                    ->state(fn($record) => $record->reviews()->count())
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-star'),
                            ]),
                    ])
                    ->icon('heroicon-o-chart-bar')
                    ->collapsible(),

                Infolists\Components\Section::make('Settings')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\IconEntry::make('is_active')
                                    ->label('Active')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),

                                Infolists\Components\IconEntry::make('is_featured')
                                    ->label('Featured')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-star')
                                    ->falseIcon('heroicon-o-minus-circle')
                                    ->trueColor('warning')
                                    ->falseColor('gray'),

                                Infolists\Components\IconEntry::make('requires_approval')
                                    ->label('Requires Approval')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-shield-check')
                                    ->falseIcon('heroicon-o-shield-exclamation')
                                    ->trueColor('info')
                                    ->falseColor('gray'),

                                Infolists\Components\TextEntry::make('slug')
                                    ->label('URL Slug')
                                    ->copyable()
                                    ->badge()
                                    ->color('gray'),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('cancellation_hours')
                                    ->label('Cancellation Window')
                                    ->suffix(' hours')
                                    ->badge()
                                    ->color('info'),

                                Infolists\Components\TextEntry::make('cancellation_fee_percentage')
                                    ->label('Cancellation Fee')
                                    ->suffix('%')
                                    ->badge()
                                    ->color('warning'),
                            ]),
                    ])
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible(),

                Infolists\Components\Section::make('Media')
                    ->schema([
                        Infolists\Components\TextEntry::make('gallery_count')
                            ->label('Gallery Images')
                            ->state(fn($record) => is_array($record->gallery) ? count($record->gallery) : 0)
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-photo'),

                        Infolists\Components\TextEntry::make('video_url')
                            ->label('Video')
                            ->url(fn($record) => $record->video_url)
                            ->openUrlInNewTab()
                            ->placeholder('No video')
                            ->icon('heroicon-o-video-camera'),
                    ])
                    ->icon('heroicon-o-photo')
                    ->collapsible()
                    ->collapsed(),

                Infolists\Components\Section::make('System Information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('id')
                                    ->label('Hall ID')
                                    ->badge()
                                    ->color('gray')
                                    ->copyable(),

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
            ]);
    }

    public function getTitle(): string
    {
        return 'Hall: ' . $this->record->name;
    }

    public function getSubheading(): ?string
    {
        $city = $this->record->city->name ?? 'Unknown City';
        $status = $this->record->is_active ? 'Active' : 'Inactive';
        $featured = $this->record->is_featured ? '• Featured' : '';
        $bookings = $this->record->bookings()->count();

        return "{$city} • {$status} {$featured} • {$bookings} Booking(s)";
    }

    protected function getTotalRevenue($record): float
    {
        // Implement based on your booking structure
        return 0.000;
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
