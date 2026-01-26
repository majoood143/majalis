<?php

declare(strict_types=1);

/**
 * ExpensePaymentStatus Enum
 * 
 * Tracks the payment status of expenses to manage accounts payable.
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
 * ExpensePaymentStatus Enum
 * 
 * Tracks payment status:
 * - Pending: Not yet paid
 * - Paid: Fully paid
 * - Partial: Partially paid
 * - Cancelled: Payment cancelled
 */
enum ExpensePaymentStatus: string implements HasLabel, HasColor, HasIcon
{
    /**
     * Payment is pending
     */
    case Pending = 'pending';
    
    /**
     * Fully paid
     */
    case Paid = 'paid';
    
    /**
     * Partially paid
     */
    case Partial = 'partial';
    
    /**
     * Payment cancelled
     */
    case Cancelled = 'cancelled';

    /**
     * Get the display label for the payment status
     *
     * @return string The localized label
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => app()->getLocale() === 'ar' ? 'في الانتظار' : 'Pending',
            self::Paid => app()->getLocale() === 'ar' ? 'مدفوع' : 'Paid',
            self::Partial => app()->getLocale() === 'ar' ? 'مدفوع جزئياً' : 'Partial',
            self::Cancelled => app()->getLocale() === 'ar' ? 'ملغي' : 'Cancelled',
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
            self::Pending => 'warning',
            self::Paid => 'success',
            self::Partial => 'info',
            self::Cancelled => 'danger',
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
            self::Pending => 'heroicon-o-clock',
            self::Paid => 'heroicon-o-check-badge',
            self::Partial => 'heroicon-o-minus-circle',
            self::Cancelled => 'heroicon-o-x-mark',
        };
    }

    /**
     * Check if expense requires payment attention
     *
     * @return bool
     */
    public function requiresAttention(): bool
    {
        return in_array($this, [self::Pending, self::Partial]);
    }

    /**
     * Check if payment is complete
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this === self::Paid;
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
