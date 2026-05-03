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
 * - Performance widgets (stats, charts, recent bookings)
 *
 * @package App\Filament\Admin\Resources\HallResource\Pages
 * @version 2.1.0
 * @author Majalis Development Team
 */

namespace App\Filament\Admin\Resources\HallResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\TextSize;
use Filament\Infolists\Components\IconEntry;
use App\Filament\Admin\Resources\HallResource;
use App\Filament\Admin\Resources\HallResource\Widgets\HallStatsOverviewWidget;
use App\Filament\Admin\Resources\HallResource\Widgets\HallBookingTrendWidget;
use App\Filament\Admin\Resources\HallResource\Widgets\HallRevenueChartWidget;
use App\Filament\Admin\Resources\HallResource\Widgets\HallBookingStatusWidget;
use App\Filament\Admin\Resources\HallResource\Widgets\HallRecentBookingsWidget;
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
     * Get the header widgets for the view page.
     *
     * Displays performance widgets above the infolist content.
     * Widgets automatically receive the $record property.
     *
     * @return array<class-string>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            // Stats Overview - Key metrics at a glance
            HallStatsOverviewWidget::class,
        ];
    }

    /**
     * Get the footer widgets for the view page.
     *
     * Displays detailed analysis widgets below the infolist content.
     * Includes charts and recent bookings table.
     *
     * @return array<class-string>
     */
    protected function getFooterWidgets(): array
    {
        return [
            // Charts row - Booking trends and Revenue analysis
            HallBookingTrendWidget::class,
            HallRevenueChartWidget::class,

            // Booking status distribution
            HallBookingStatusWidget::class,

            // Recent bookings table
            HallRecentBookingsWidget::class,
        ];
    }

    /**
     * Get the number of widget columns.
     *
     * Controls the grid layout for widgets.
     * Using 2 columns allows charts to display side by side.
     *
     * @return int|string|array
     */
    public function getHeaderWidgetsColumns(): int|array
    {
        return 1; // Stats overview takes full width
    }

    /**
     * Get the number of footer widget columns.
     *
     * @return int|string|array
     */
    public function getFooterWidgetsColumns(): int|array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 2,
            'xl' => 2,
        ];
    }

    /**
     * Define header actions for the view page.
     *
     * @return array List of action buttons
     */
    protected function getHeaderActions(): array
    {
        return [
            // Edit action
            EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('primary'),

            // Toggle active status
            Action::make('toggleActive')
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
            Action::make('viewBookings')
                ->label(__('Bookings'))
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->badge(fn() => $this->record->bookings()->count())
                ->url(fn() => route('filament.admin.resources.bookings.index', [
                    'tableFilters' => ['hall_id' => ['value' => $this->record->id]]
                ])),

            // Open in Google Maps
            Action::make('viewLocation')
                ->label(__('Open in Maps'))
                ->icon('heroicon-o-map-pin')
                ->color('success')
                ->url(fn() => $this->record->google_maps_url
                    ?: "https://www.google.com/maps/search/?api=1&query={$this->record->latitude},{$this->record->longitude}")
                ->openUrlInNewTab()
                ->visible(fn() => $this->record->latitude && $this->record->longitude),

            // Delete action
            DeleteAction::make()
                ->successRedirectUrl(route('filament.admin.resources.halls.index')),
        ];
    }

    /**
     * Define the infolist schema for displaying hall details.
     *
     * @param Schema $infolist The Filament infolist instance
     * @return Schema Configured infolist with all sections
     */
    public function infolist(Schema $schema): Schema
    {
        return $infolist
            ->schema([
                // =============================================
                // SECTION: Hall Overview
                // =============================================
                Section::make(__('Hall Overview'))
                    ->schema([
                        // Featured image
                        ImageEntry::make('featured_image')
                            ->label(__('Featured Image'))
                            ->disk('public')
                            ->height(300)
                            ->columnSpanFull()
                            ->visible(fn($record) => $record->featured_image),

                        // Basic info grid
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('admin.fields.hall_name'))
                                    ->formatStateUsing(fn($record) => $record->name)
                                    ->badge()
                                    ->color('primary')
                                    ->size(TextSize::Large)
                                    ->icon('heroicon-o-building-office-2'),

                    TextEntry::make('region.name')
                        ->label(__('admin.fields.region'))
                        ->formatStateUsing(fn($record) => $record->region->name ?? 'N/A')
                        ->badge()
                        ->color('success')
                        ->icon('heroicon-o-map-pin'),

                                TextEntry::make('city.name')
                                    ->label(__('admin.fields.city'))
                                    ->formatStateUsing(fn($record) => $record->city->name ?? 'N/A')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-map-pin'),

                                TextEntry::make('owner.name')
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
                Section::make(__('Description'))
                    ->schema([
                        TextEntry::make('description_en')
                            ->label(__('Description (English)'))
                            ->html()
                            ->columnSpanFull()
                            ->getStateUsing(fn($record) => $record->getTranslation('description', 'en') ?? 'N/A'),

                        TextEntry::make('description_ar')
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
                Section::make(__('Capacity & Pricing'))
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('capacity_min')
                                    ->label(__('Min Capacity'))
                                    ->suffix(' ' . __('guests'))
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('capacity_max')
                                    ->label(__('Max Capacity'))
                                    ->suffix(' ' . __('guests'))
                                    ->badge()
                                    ->color('success')
                                    ->size(TextSize::Large),

                                TextEntry::make('price_per_slot')
                                    ->label(__('Base Price'))
                                    ->money('OMR')
                                    ->badge()
                                    ->color('warning')
                                    ->size(TextSize::Large),

                                TextEntry::make('average_rating')
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
                // SECTION: Statistics (Quick Summary)
                // =============================================
                Section::make(__('Quick Statistics'))
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('total_bookings')
                                    ->label(__('Total Bookings'))
                                    ->state(fn($record) => $record->bookings()->count())
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-o-calendar-days'),

                                TextEntry::make('total_revenue')
                                    ->label(__('Total Revenue'))
                                    ->state(fn($record) => number_format($this->getTotalRevenue($record), 3) . ' OMR')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-banknotes'),

                                TextEntry::make('reviews_count')
                                    ->label(__('Reviews'))
                                    ->state(fn($record) => $record->reviews()->count())
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-star'),

                                TextEntry::make('pending_bookings')
                                    ->label(__('Pending'))
                                    ->state(fn($record) => $record->bookings()->where('status', 'pending')->count())
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->icon('heroicon-o-chart-bar')
                    ->collapsible(),

                // =============================================
                // SECTION: Settings
                // =============================================
                Section::make(__('Settings'))
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                IconEntry::make('is_active')
                                    ->label(__('Active'))
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),

                                IconEntry::make('is_featured')
                                    ->label(__('Featured'))
                                    ->boolean()
                                    ->trueIcon('heroicon-o-star')
                                    ->falseIcon('heroicon-o-minus-circle')
                                    ->trueColor('warning')
                                    ->falseColor('gray'),

                                IconEntry::make('requires_approval')
                                    ->label(__('Requires Approval'))
                                    ->boolean()
                                    ->trueIcon('heroicon-o-shield-check')
                                    ->falseIcon('heroicon-o-shield-exclamation')
                                    ->trueColor('info')
                                    ->falseColor('gray'),

                                TextEntry::make('slug')
                                    ->label(__('URL Slug'))
                                    ->copyable()
                                    ->badge()
                                    ->color('gray'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('cancellation_hours')
                                    ->label(__('Cancellation Window'))
                                    ->suffix(' ' . __('hours'))
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('cancellation_fee_percentage')
                                    ->label(__('Cancellation Fee'))
                                    ->suffix('%')
                                    ->badge()
                                    ->color('warning'),
                            ]),
                    ])
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible(),

                // =============================================
                // SECTION: System Information
                // =============================================
                Section::make(__('System Information'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('id')
                                    ->label(__('Hall ID'))
                                    ->badge()
                                    ->color('gray')
                                    ->copyable(),

                                TextEntry::make('created_at')
                                    ->label(__('Created At'))
                                    ->dateTime('d M Y, h:i A')
                                    ->icon('heroicon-o-calendar'),

                                TextEntry::make('updated_at')
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
