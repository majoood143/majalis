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
 * ✅ FIX: Moved widget from getHeaderWidgets() to getFooterWidgets().
 *
 *    Filament rendering order: Header Widgets → Page Content (blade) → Footer Widgets
 *
 *    Original: getHeaderWidgets() → Calendar appeared ABOVE the legend/instructions
 *    Fixed:    getFooterWidgets() → Legend/instructions appear FIRST, then calendar
 *
 *    IMPORTANT: The widget MUST be rendered through Filament's widget system
 *    (getHeaderWidgets/getFooterWidgets), NOT via @livewire directive.
 *    Using @livewire breaks the action/modal lifecycle needed for
 *    create/edit/delete modals and header actions.
 *
 * @package App\Filament\Owner\Pages
 */
class AvailabilityCalendarPage extends Page
{
    /**
     * The navigation icon.
     */
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar';

    /**
     * Get the navigation group.
     */
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
    protected string $view = 'filament.owner.pages.availability-calendar-page';

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
     * ✅ FIX: Calendar widget renders as FOOTER widget.
     *
     * This ensures the blade content (legend + instructions) appears
     * BEFORE the calendar in the page layout.
     *
     * Rendering order:
     *  1. Page Content / Blade → legend + instructions
     *  2. Footer Widgets       → calendar
     *
     * @return array<class-string>
     */
    protected function getFooterWidgets(): array
    {
        return [
            AvailabilityCalendarWidget::class,
        ];
    }

    /**
     * Get the footer widgets column configuration.
     */
    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }
}
