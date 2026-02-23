<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\HallResource\Pages;

use App\Filament\Owner\Resources\HallResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Owner\Resources\HallResource\Widgets\HallStatsOverviewWidget;
use App\Filament\Owner\Resources\HallResource\Widgets\HallBookingTrendWidget;
use App\Filament\Owner\Resources\HallResource\Widgets\HallRevenueChartWidget;
use App\Filament\Owner\Resources\HallResource\Widgets\HallBookingStatusWidget;
use App\Filament\Owner\Resources\HallResource\Widgets\HallRecentBookingsWidget;

/**
 * ViewHall Page for Owner Panel
 *
 * Displays detailed hall information for the owner.
 */
class ViewHall extends ViewRecord
{
    /**
     * The resource this page belongs to.
     *
     * @var string
     */
    protected static string $resource = HallResource::class;

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return $this->record->getTranslation('name', app()->getLocale());
    }

    /**
     * Get the header actions.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            // Edit Hall
            Actions\EditAction::make()
                ->label(__('owner.halls.actions.edit'))
                ->icon('heroicon-o-pencil'),

            // Manage Availability
            Actions\Action::make('availability')
                ->label(__('owner.halls.actions.availability'))
                ->icon('heroicon-o-calendar')
                ->color('info')
                ->url(fn () => HallResource::getUrl('availability', ['record' => $this->record])),

            // View on Website
            Actions\Action::make('view_public')
                ->label(__('owner.halls.actions.view_public'))
                ->icon('heroicon-o-globe-alt')
                ->color('gray')
                ->url(fn () => route('customer.halls.show', $this->record->slug))
                ->openUrlInNewTab(),
        ];
    }

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
     * Get the relation managers to display.
     *
     * @return array<class-string>
     */
    public function getRelationManagers(): array
    {
        return [
            // Show relation managers in view mode too
        ];
    }
}
