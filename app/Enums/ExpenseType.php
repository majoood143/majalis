<?php

declare(strict_types=1);

/**
 * ExpenseType Enum
 * 
 * Defines the different types of expenses that can be recorded in the system.
 * Each type has specific use cases and reporting implications.
 * 
 * @package App\Enums
 * @author  Majalis Development Team
 * @version 1.0.0
 */

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

/**
 * ExpenseType Enum
 * 
 * Classifies expenses into distinct categories:
 * - Booking: Directly related to a specific booking/event
 * - Operational: Day-to-day operational costs
 * - Recurring: Regularly occurring expenses (rent, utilities)
 * - OneTime: One-off expenses (equipment purchase, repairs)
 */
enum ExpenseType: string implements HasLabel, HasColor, HasIcon
{
    /**
     * Booking-specific expenses (catering, decoration, cleaning)
     */
    case Booking = 'booking';
    
    /**
     * Day-to-day operational expenses
     */
    case Operational = 'operational';
    
    /**
     * Regularly recurring expenses (monthly, yearly)
     */
    case Recurring = 'recurring';
    
    /**
     * One-time expenses (equipment, repairs)
     */
    case OneTime = 'one_time';

    /**
     * Get the display label for the expense type
     * Supports bilingual display (Arabic/English)
     *
     * @return string The localized label
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::Booking => __('expenses.types.booking', [], app()->getLocale()) !== 'expenses.types.booking' 
                ? __('expenses.types.booking') 
                : (app()->getLocale() === 'ar' ? 'مصروفات الحجز' : 'Booking Expense'),
            self::Operational => __('expenses.types.operational', [], app()->getLocale()) !== 'expenses.types.operational'
                ? __('expenses.types.operational')
                : (app()->getLocale() === 'ar' ? 'مصروفات تشغيلية' : 'Operational'),
            self::Recurring => __('expenses.types.recurring', [], app()->getLocale()) !== 'expenses.types.recurring'
                ? __('expenses.types.recurring')
                : (app()->getLocale() === 'ar' ? 'مصروفات متكررة' : 'Recurring'),
            self::OneTime => __('expenses.types.one_time', [], app()->getLocale()) !== 'expenses.types.one_time'
                ? __('expenses.types.one_time')
                : (app()->getLocale() === 'ar' ? 'مصروفات لمرة واحدة' : 'One-Time'),
        };
    }

    /**
     * Get the color for UI display
     *
     * @return string|array|null Filament color identifier
     */
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Booking => 'info',      // Blue - booking related
            self::Operational => 'warning', // Yellow/Orange - day-to-day
            self::Recurring => 'success',   // Green - predictable
            self::OneTime => 'gray',        // Gray - one-off
        };
    }

    /**
     * Get the icon for UI display
     *
     * @return string|null Heroicon name
     */
    public function getIcon(): ?string
    {
        return match ($this) {
            self::Booking => 'heroicon-o-calendar-days',
            self::Operational => 'heroicon-o-cog-6-tooth',
            self::Recurring => 'heroicon-o-arrow-path',
            self::OneTime => 'heroicon-o-bolt',
        };
    }

    /**
     * Get description for the expense type
     *
     * @return string Localized description
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::Booking => app()->getLocale() === 'ar' 
                ? 'مصروفات مرتبطة بحجز معين (تموين، ديكور، تنظيف)'
                : 'Expenses directly linked to a specific booking (catering, decoration, cleaning)',
            self::Operational => app()->getLocale() === 'ar'
                ? 'مصروفات التشغيل اليومية للقاعة'
                : 'Day-to-day operational costs for running the hall',
            self::Recurring => app()->getLocale() === 'ar'
                ? 'مصروفات تتكرر بشكل منتظم (إيجار، كهرباء، ماء)'
                : 'Regularly occurring expenses (rent, utilities, subscriptions)',
            self::OneTime => app()->getLocale() === 'ar'
                ? 'مصروفات لمرة واحدة (شراء معدات، إصلاحات)'
                : 'One-off expenses (equipment purchase, major repairs)',
        };
    }

    /**
     * Check if this type can be linked to a booking
     *
     * @return bool
     */
    public function canLinkToBooking(): bool
    {
        return $this === self::Booking;
    }

    /**
     * Check if this type supports recurring settings
     *
     * @return bool
     */
    public function supportsRecurring(): bool
    {
        return $this === self::Recurring;
    }

    /**
     * Get all values as array for form options
     *
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn($case) => $case->getLabel(), self::cases())
        );
    }
}
