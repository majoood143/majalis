<?php

declare(strict_types=1);

/**
 * ViewHall - View Hall Record Page
 *
 * Displays comprehensive hall information including:
 * - Hall overview with featured image
 * - Interactive map showing hall location (OpenStreetMap)
 * - Capacity, pricing, and contact details
 * - Statistics and settings
 *
 * @package App\Filament\Admin\Resources\HallResource\Pages
 * @version 2.0.0
 * @author Majalis Development Team
 */

namespace App\Filament\Admin\Resources\HallResource\Pages;

use App\Filament\Admin\Resources\HallResource;
use App\Models\HallFeature;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ViewHall extends ViewRecord
{
    /**
     * The parent resource class.
     */
    protected static string $resource = HallResource::class;

    /**
     * Default coordinates for Oman (Muscat).
     */
    private const DEFAULT_LATITUDE = 23.5880;
    private const DEFAULT_LONGITUDE = 58.3829;
    private const DEFAULT_ZOOM = 15;

    /**
     * Define header actions for the view page.
     *
     * @return array List of action buttons
     */
    protected function getHeaderActions(): array
    {
        return [
            // Edit action
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('primary'),

            // Toggle active status
            Actions\Action::make('toggleActive')
                ->label(fn() => $this->record->is_active ? __('Deactivate') : __('Activate'))
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn() => $this->record->is_active ? 'danger' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn() => $this->record->is_active ? __('Deactivate Hall') : __('Activate Hall'))
                ->modalDescription(fn() => $this->record->is_active
                    ? __('This hall will be hidden from customers. Are you sure?')
                    : __('This hall will be visible to customers. Are you sure?'))
                ->action(function () {
                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();

                    Notification::make()
                        ->success()
                        ->title(__('Status Updated'))
                        ->body($this->record->is_active ? __('Hall is now active') : __('Hall is now inactive'))
                        ->send();

                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            // View bookings action
            Actions\Action::make('viewBookings')
                ->label(__('Bookings'))
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->badge(fn() => $this->record->bookings()->count())
                ->url(fn() => route('filament.admin.resources.bookings.index', [
                    'tableFilters' => ['hall_id' => ['value' => $this->record->id]]
                ])),

            // Open in Google Maps
            Actions\Action::make('viewLocation')
                ->label(__('Open in Maps'))
                ->icon('heroicon-o-map-pin')
                ->color('success')
                ->url(fn() => $this->record->google_maps_url
                    ?: "https://www.google.com/maps/search/?api=1&query={$this->record->latitude},{$this->record->longitude}")
                ->openUrlInNewTab()
                ->visible(fn() => $this->record->latitude && $this->record->longitude),

            // Delete action
            Actions\DeleteAction::make()
                ->successRedirectUrl(route('filament.admin.resources.halls.index')),
        ];
    }

    /**
     * Define the infolist schema for displaying hall details.
     *
     * @param Infolist $infolist The Filament infolist instance
     * @return Infolist Configured infolist with all sections
     */
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // =============================================
                // SECTION: Hall Overview
                // =============================================
                Infolists\Components\Section::make(__('Hall Overview'))
                    ->schema([
                        // Featured image
                        Infolists\Components\ImageEntry::make('featured_image')
                            ->label(__('Featured Image'))
                            ->disk('public')
                            ->height(300)
                            ->columnSpanFull()
                            ->visible(fn($record) => $record->featured_image),

                        // Basic info grid
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label(__('admin.fields.hall_name'))
                                    ->formatStateUsing(fn($record) => $record->name)
                                    ->badge()
                                    ->color('primary')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->icon('heroicon-o-building-office-2'),

                    Infolists\Components\TextEntry::make('region.name')
                        ->label(__('admin.fields.region'))
                        ->formatStateUsing(fn($record) => $record->region->name ?? 'N/A')
                        ->badge()
                        ->color('success')
                        ->icon('heroicon-o-map-pin'),

                                Infolists\Components\TextEntry::make('city.name')
                                    ->label(__('admin.fields.city'))
                                    ->formatStateUsing(fn($record) => $record->city->name ?? 'N/A')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-map-pin'),

                                Infolists\Components\TextEntry::make('owner.name')
                                    //->label(__('Owner'))
                                    ->label(__('admin.reports.table.owner'))
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-user'),
                            ]),
                    ])
                    ->collapsible(),

                // =============================================
                // SECTION: Description
                // =============================================
                Infolists\Components\Section::make(__('Description'))
                    ->schema([
                        Infolists\Components\TextEntry::make('description_en')
                            ->label(__('Description (English)'))
                            ->html()
                            ->columnSpanFull()
                            ->getStateUsing(fn($record) => $record->getTranslation('description', 'en') ?? 'N/A'),

                        Infolists\Components\TextEntry::make('description_ar')
                            ->label(__('Description (Arabic)'))
                            ->html()
                            ->columnSpanFull()
                            ->getStateUsing(fn($record) => $record->getTranslation('description', 'ar') ?? 'N/A'),
                    ])
                    ->icon('heroicon-o-document-text')
                    ->collapsible(),

                // =============================================
                // SECTION: Capacity & Pricing
                // =============================================
                Infolists\Components\Section::make(__('Capacity & Pricing'))
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('capacity_min')
                                    ->label(__('Min Capacity'))
                                    ->suffix(' ' . __('guests'))
                                    ->badge()
                                    ->color('info'),

                                Infolists\Components\TextEntry::make('capacity_max')
                                    ->label(__('Max Capacity'))
                                    ->suffix(' ' . __('guests'))
                                    ->badge()
                                    ->color('success')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),

                                Infolists\Components\TextEntry::make('price_per_slot')
                                    ->label(__('Base Price'))
                                    ->money('OMR')
                                    ->badge()
                                    ->color('warning')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),

                                Infolists\Components\TextEntry::make('average_rating')
                                    ->label(__('Rating'))
                                    ->badge()
                                    ->color('warning')
                                    ->suffix('/5')
                                    ->icon('heroicon-o-star')
                                    ->default('N/A'),
                            ]),
                    ])
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsible(),

            // =============================================
            // SECTION: Time Slots & Pricing
            // Displays pricing for each time slot with visual indicators
            // =============================================
            Infolists\Components\Section::make(__('Time Slots & Pricing'))
                ->schema([
                    // Pricing explanation
                    Infolists\Components\TextEntry::make('pricing_note')
                        ->hiddenLabel()
                        ->state(__('Prices shown per slot. Custom slot prices override the base price.'))
                        ->color('gray')
                        ->size(Infolists\Components\TextEntry\TextEntrySize::Small)
                        ->columnSpanFull(),

                    // Base Price Display
                    Infolists\Components\TextEntry::make('base_price_display')
                        ->label(__('Base Price (Default)'))
                        ->state(fn($record) => number_format((float) $record->price_per_slot, 3) . ' OMR')
                        ->badge()
                        ->color('gray')
                        ->icon('heroicon-o-banknotes')
                        ->columnSpanFull(),

                    // Time Slots Grid
                    Infolists\Components\Grid::make(4)
                        ->schema([
                            // Morning Slot
                            Infolists\Components\TextEntry::make('price_morning')
                                ->label(__('Morning'))
                                ->helperText('8:00 AM - 12:00 PM')
                                ->state(function ($record): string {
                                    $price = $record->getPriceForSlot('morning');
                                    $isOverride = isset($record->pricing_override['morning']);
                                    return number_format($price, 3) . ' OMR';
                                })
                                ->badge()
                                ->color(fn($record) => isset($record->pricing_override['morning']) ? 'success' : 'info')
                                ->icon('heroicon-o-sun'),

                            // Afternoon Slot
                            Infolists\Components\TextEntry::make('price_afternoon')
                                ->label(__('Afternoon'))
                                ->helperText('12:00 PM - 5:00 PM')
                                ->state(function ($record): string {
                                    $price = $record->getPriceForSlot('afternoon');
                                    return number_format($price, 3) . ' OMR';
                                })
                                ->badge()
                                ->color(fn($record) => isset($record->pricing_override['afternoon']) ? 'success' : 'info')
                                ->icon('heroicon-o-cloud'),

                            // Evening Slot
                            Infolists\Components\TextEntry::make('price_evening')
                                ->label(__('Evening'))
                                ->helperText('5:00 PM - 11:00 PM')
                                ->state(function ($record): string {
                                    $price = $record->getPriceForSlot('evening');
                                    return number_format($price, 3) . ' OMR';
                                })
                                ->badge()
                                ->color(fn($record) => isset($record->pricing_override['evening']) ? 'success' : 'info')
                                ->icon('heroicon-o-moon'),

                            // Full Day Slot
                            Infolists\Components\TextEntry::make('price_full_day')
                                ->label(__('Full Day'))
                                ->helperText('8:00 AM - 11:00 PM')
                                ->state(function ($record): string {
                                    $price = $record->getPriceForSlot('full_day');
                                    return number_format($price, 3) . ' OMR';
                                })
                                ->badge()
                                ->color(fn($record) => isset($record->pricing_override['full_day']) ? 'success' : 'info')
                                ->icon('heroicon-o-calendar-days'),
                        ]),

                    // Legend for color coding
                    Infolists\Components\Grid::make(2)
                        ->schema([
                            Infolists\Components\TextEntry::make('legend_base')
                                ->hiddenLabel()
                                ->state(__('🔵 Blue = Base Price'))
                                ->color('info')
                                ->size(Infolists\Components\TextEntry\TextEntrySize::Small),

                            Infolists\Components\TextEntry::make('legend_custom')
                                ->hiddenLabel()
                                ->state(__('🟢 Green = Custom Slot Price'))
                                ->color('success')
                                ->size(Infolists\Components\TextEntry\TextEntrySize::Small),
                        ]),

                    // Advance Payment Info (if enabled)
                    Infolists\Components\Fieldset::make(__('Advance Payment Settings'))
                        ->schema([
                            Infolists\Components\IconEntry::make('allows_advance_payment')
                                ->label(__('Advance Payment'))
                                ->boolean()
                                ->trueIcon('heroicon-o-check-circle')
                                ->falseIcon('heroicon-o-x-circle')
                                ->trueColor('success')
                                ->falseColor('gray'),

                            Infolists\Components\TextEntry::make('advance_payment_percentage')
                                ->label(__('Advance Percentage'))
                                ->suffix('%')
                                ->badge()
                                ->color('warning')
                                ->visible(fn($record) => $record->allows_advance_payment && $record->advance_payment_percentage),

                            Infolists\Components\TextEntry::make('advance_payment_amount')
                                ->label(__('Fixed Advance'))
                                ->money('OMR')
                                ->badge()
                                ->color('warning')
                                ->visible(fn($record) => $record->allows_advance_payment && $record->advance_payment_amount),

                            Infolists\Components\TextEntry::make('minimum_advance_payment')
                                ->label(__('Minimum Advance'))
                                ->money('OMR')
                                ->badge()
                                ->color('info')
                                ->visible(fn($record) => $record->allows_advance_payment && $record->minimum_advance_payment),
                        ])
                        ->columns(4)
                        ->visible(fn($record) => $record->allows_advance_payment),
                ])
                ->icon('heroicon-o-clock')
                ->description(__('Pricing breakdown for each booking time slot'))
                ->collapsible(),

                // =============================================
                // SECTION: Contact Information
                // =============================================
                Infolists\Components\Section::make(__('Contact Information'))
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('phone')
                                    ->label(__('Phone'))
                                    ->icon('heroicon-o-phone')
                                    ->copyable()
                                    ->copyMessage(__('Phone copied!')),

                                Infolists\Components\TextEntry::make('whatsapp')
                                    ->label(__('WhatsApp'))
                                    ->icon('heroicon-o-device-phone-mobile')
                                    ->copyable()
                                    ->copyMessage(__('WhatsApp copied!'))
                                    ->placeholder(__('Not provided')),

                                Infolists\Components\TextEntry::make('email')
                                    ->label(__('Email'))
                                    ->icon('heroicon-o-envelope')
                                    ->copyable()
                                    ->copyMessage(__('Email copied!'))
                                    ->placeholder(__('Not provided')),
                            ]),
                    ])
                    ->icon('heroicon-o-phone')
                    ->collapsible(),

                // =============================================
                // SECTION: Location with Interactive Map
                // =============================================
                Infolists\Components\Section::make(__('Location'))
                    ->schema([
                        // Address information
                        Infolists\Components\TextEntry::make('address')
                            ->label(__('Full Address'))
                            ->icon('heroicon-o-map-pin')
                            ->columnSpanFull()
                            ->copyable()
                            ->copyMessage(__('Address copied!')),

                        // Coordinates display
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('latitude')
                                    ->label(__('Latitude'))
                                    ->badge()
                                    ->color('gray')
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('longitude')
                                    ->label(__('Longitude'))
                                    ->badge()
                                    ->color('gray')
                                    ->copyable(),
                            ]),


                        // =============================================
                        // INTERACTIVE MAP DISPLAY
                        // Shows the hall location on OpenStreetMap
                        // =============================================
                        Infolists\Components\ViewEntry::make('location_map')
                            ->label(__('Hall Location on Map'))
                            ->view('filament.infolists.components.hall-map')
                            ->columnSpanFull()
                            ->visible(fn($record) => $record->latitude && $record->longitude),

                        // Google Maps link (if no coordinates)
                        Infolists\Components\TextEntry::make('google_maps_url')
                            ->label(__('Google Maps Link'))
                            ->url(fn($record) => $record->google_maps_url)
                            ->openUrlInNewTab()
                            ->visible(fn($record) => $record->google_maps_url)
                            ->icon('heroicon-o-arrow-top-right-on-square'),
                    ])
                    ->icon('heroicon-o-map-pin')
                    ->collapsible(),

                // =============================================
                // SECTION: Features
                // =============================================
                Infolists\Components\Section::make(__('Features'))
                    ->schema([
                        Infolists\Components\TextEntry::make('features_display')
                            ->label(__('Available Features'))
                            ->getStateUsing(function ($record) {
                                if (empty($record->features)) {
                                    return __('No features assigned');
                                }

                                // Get feature names from IDs
                                $featureIds = is_array($record->features) ? $record->features : [];
                                $features = HallFeature::whereIn('id', $featureIds)
                                    ->where('is_active', true)
                                    ->pluck('name')
                                    ->toArray();

                                return implode(', ', $features) ?: __('No features assigned');
                            })
                            ->badge()
                            ->separator(',')
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-star')
                    ->collapsible(),

                // =============================================
                // SECTION: Statistics
                // =============================================
                Infolists\Components\Section::make(__('Statistics'))
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('total_bookings')
                                    ->label(__('Total Bookings'))
                                    ->state(fn($record) => $record->bookings()->count())
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('confirmed_bookings')
                                    ->label(__('Confirmed'))
                                    ->state(fn($record) => $record->bookings()->where('status', 'confirmed')->count())
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-check-circle'),

                                Infolists\Components\TextEntry::make('total_revenue')
                                    ->label(__('Total Revenue'))
                                    ->state(fn($record) => number_format($this->getTotalRevenue($record), 3) . ' OMR')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-banknotes'),

                                Infolists\Components\TextEntry::make('reviews_count')
                                    ->label(__('Reviews'))
                                    ->state(fn($record) => $record->reviews()->count())
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-star'),
                            ]),
                    ])
                    ->icon('heroicon-o-chart-bar')
                    ->collapsible(),

                // =============================================
                // SECTION: Settings
                // =============================================
                Infolists\Components\Section::make(__('Settings'))
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\IconEntry::make('is_active')
                                    ->label(__('Active'))
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),

                                Infolists\Components\IconEntry::make('is_featured')
                                    ->label(__('Featured'))
                                    ->boolean()
                                    ->trueIcon('heroicon-o-star')
                                    ->falseIcon('heroicon-o-minus-circle')
                                    ->trueColor('warning')
                                    ->falseColor('gray'),

                                Infolists\Components\IconEntry::make('requires_approval')
                                    ->label(__('Requires Approval'))
                                    ->boolean()
                                    ->trueIcon('heroicon-o-shield-check')
                                    ->falseIcon('heroicon-o-shield-exclamation')
                                    ->trueColor('info')
                                    ->falseColor('gray'),

                                Infolists\Components\TextEntry::make('slug')
                                    ->label(__('URL Slug'))
                                    ->copyable()
                                    ->badge()
                                    ->color('gray'),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('cancellation_hours')
                                    ->label(__('Cancellation Window'))
                                    ->suffix(' ' . __('hours'))
                                    ->badge()
                                    ->color('info'),

                                Infolists\Components\TextEntry::make('cancellation_fee_percentage')
                                    ->label(__('Cancellation Fee'))
                                    ->suffix('%')
                                    ->badge()
                                    ->color('warning'),
                            ]),
                    ])
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible(),

                // =============================================
                // SECTION: Media Gallery
                // =============================================
                Infolists\Components\Section::make(__('Media'))
                    ->schema([
                        Infolists\Components\TextEntry::make('gallery_count')
                            ->label(__('Gallery Images'))
                            ->state(fn($record) => is_array($record->gallery) ? count($record->gallery) : 0)
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-photo'),

                        Infolists\Components\TextEntry::make('video_url')
                            ->label(__('Video'))
                            ->url(fn($record) => $record->video_url)
                            ->openUrlInNewTab()
                            ->placeholder(__('No video'))
                            ->icon('heroicon-o-video-camera'),
                    ])
                    ->icon('heroicon-o-photo')
                    ->collapsible()
                    ->collapsed(),

                // =============================================
                // SECTION: System Information
                // =============================================
                Infolists\Components\Section::make(__('System Information'))
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('id')
                                    ->label(__('Hall ID'))
                                    ->badge()
                                    ->color('gray')
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('Created At'))
                                    ->dateTime('d M Y, h:i A')
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label(__('Last Updated'))
                                    ->dateTime('d M Y, h:i A')
                                    ->since()
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->icon('heroicon-o-server')
                    ->collapsed(),
            ]);
    }

    /**
     * Get the page title.
     *
     * @return string Page title
     */
    public function getTitle(): string
    {
        return __('Hall') . ': ' . $this->record->name;
    }

    /**
     * Get the page subheading with key info.
     *
     * @return string|null Subheading text
     */
    public function getSubheading(): ?string
    {
        $city = $this->record->city->name ?? __('Unknown City');
        $status = $this->record->is_active ? __('Active') : __('Inactive');
        $featured = $this->record->is_featured ? '• ' . __('Featured') : '';
        $bookings = $this->record->bookings()->count();

        return "{$city} • {$status} {$featured} • {$bookings} " . __('Booking(s)');
    }

    /**
     * Calculate total revenue for the hall.
     *
     * @param mixed $record The hall record
     * @return float Total revenue amount
     */
    protected function getTotalRevenue($record): float
    {
        // Calculate revenue from confirmed/completed bookings
        return (float) $record->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('total_amount') ?? 0.000;
    }

    /**
     * Get the breadcrumb label.
     *
     * @return string Breadcrumb text
     */
    public function getBreadcrumb(): string
    {
        return $this->record->name;
    }

    /**
     * Enable combined relation manager tabs.
     *
     * @return bool Whether to combine tabs
     */
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
