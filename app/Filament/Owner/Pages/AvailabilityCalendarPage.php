<?php

declare(strict_types=1);

namespace App\Filament\Owner\Pages;

use App\Filament\Owner\Widgets\AvailabilityCalendarWidget;
use Filament\Pages\Page;

/**
 * AvailabilityCalendarPage - Full Page Calendar View
 *
 * Dedicated page for the FullCalendar availability widget.
 * Provides a full-screen calendar experience for managing hall availability.
 *
 * @package App\Filament\Owner\Pages
 */
class AvailabilityCalendarPage extends Page
{
    /**
     * The navigation icon.
     */
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    /**
     * The navigation group.
     */
    //protected static ?string $navigationGroup = 'Hall Management';

    public static function getNavigationGroup(): ?string
    {
        return __('owner.nav_groups.hall_management');
    }

    /**
     * The navigation sort order.
     */
    protected static ?int $navigationSort = 3;

    /**
     * The view for this page.
     */
    protected static string $view = 'filament.owner.pages.availability-calendar-page';

    /**
     * Get the navigation label.
     */
    public static function getNavigationLabel(): string
    {
        return __('owner.fullcalendar.navigation');
    }

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return __('owner.fullcalendar.page_title');
    }

    /**
     * Get the page heading.
     */
    public function getHeading(): string
    {
        return __('owner.fullcalendar.heading');
    }

    /**
     * Get the subheading.
     */
    public function getSubheading(): ?string
    {
        return __('owner.fullcalendar.subheading');
    }

    /**
     * Get the widgets for this page.
     *
     * @return array<class-string>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            AvailabilityCalendarWidget::class,
        ];
    }

    /**
     * Get the header widgets column configuration.
     */
    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }
}
