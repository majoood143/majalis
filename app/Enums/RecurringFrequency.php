<?php

declare(strict_types=1);

/**
 * RecurringFrequency Enum
 * 
 * Defines the frequency options for recurring expenses.
 * 
 * @package App\Enums
 * @author  Majalis Development Team
 * @version 1.0.0
 */

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * RecurringFrequency Enum
 * 
 * Frequency options for recurring expenses:
 * - Daily, Weekly, Monthly, Quarterly, Yearly
 */
enum RecurringFrequency: string implements HasLabel
{
    /**
     * Daily occurrence
     */
    case Daily = 'daily';
    
    /**
     * Weekly occurrence
     */
    case Weekly = 'weekly';
    
    /**
     * Monthly occurrence
     */
    case Monthly = 'monthly';
    
    /**
     * Quarterly (every 3 months)
     */
    case Quarterly = 'quarterly';
    
    /**
     * Yearly occurrence
     */
    case Yearly = 'yearly';

    /**
     * Get the display label
     *
     * @return string The localized label
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::Daily => app()->getLocale() === 'ar' ? 'يومي' : 'Daily',
            self::Weekly => app()->getLocale() === 'ar' ? 'أسبوعي' : 'Weekly',
            self::Monthly => app()->getLocale() === 'ar' ? 'شهري' : 'Monthly',
            self::Quarterly => app()->getLocale() === 'ar' ? 'ربع سنوي' : 'Quarterly',
            self::Yearly => app()->getLocale() === 'ar' ? 'سنوي' : 'Yearly',
        };
    }

    /**
     * Get the interval in days (approximate)
     *
     * @return int Days between occurrences
     */
    public function getIntervalDays(): int
    {
        return match ($this) {
            self::Daily => 1,
            self::Weekly => 7,
            self::Monthly => 30,
            self::Quarterly => 90,
            self::Yearly => 365,
        };
    }

    /**
     * Get Carbon interval string
     *
     * @return string Carbon interval format
     */
    public function getCarbonInterval(): string
    {
        return match ($this) {
            self::Daily => '1 day',
            self::Weekly => '1 week',
            self::Monthly => '1 month',
            self::Quarterly => '3 months',
            self::Yearly => '1 year',
        };
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
