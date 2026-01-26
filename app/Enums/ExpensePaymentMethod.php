<?php

declare(strict_types=1);

/**
 * ExpensePaymentMethod Enum
 * 
 * Defines the payment methods available for expense payments.
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
 * ExpensePaymentMethod Enum
 * 
 * Tracks how the expense was paid:
 * - Cash: Cash payment
 * - BankTransfer: Bank transfer
 * - Card: Credit/Debit card
 * - Cheque: Cheque payment
 * - Other: Other methods
 */
enum ExpensePaymentMethod: string implements HasLabel, HasColor, HasIcon
{
    /**
     * Cash payment
     */
    case Cash = 'cash';
    
    /**
     * Bank transfer
     */
    case BankTransfer = 'bank_transfer';
    
    /**
     * Credit/Debit card
     */
    case Card = 'card';
    
    /**
     * Cheque payment
     */
    case Cheque = 'cheque';
    
    /**
     * Other payment methods
     */
    case Other = 'other';

    /**
     * Get the display label for the payment method
     *
     * @return string The localized label
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::Cash => app()->getLocale() === 'ar' ? 'نقداً' : 'Cash',
            self::BankTransfer => app()->getLocale() === 'ar' ? 'تحويل بنكي' : 'Bank Transfer',
            self::Card => app()->getLocale() === 'ar' ? 'بطاقة' : 'Card',
            self::Cheque => app()->getLocale() === 'ar' ? 'شيك' : 'Cheque',
            self::Other => app()->getLocale() === 'ar' ? 'أخرى' : 'Other',
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
            self::Cash => 'success',
            self::BankTransfer => 'info',
            self::Card => 'primary',
            self::Cheque => 'warning',
            self::Other => 'gray',
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
            self::Cash => 'heroicon-o-banknotes',
            self::BankTransfer => 'heroicon-o-building-library',
            self::Card => 'heroicon-o-credit-card',
            self::Cheque => 'heroicon-o-document-text',
            self::Other => 'heroicon-o-ellipsis-horizontal-circle',
        };
    }

    /**
     * Check if method requires a reference number
     *
     * @return bool
     */
    public function requiresReference(): bool
    {
        return in_array($this, [self::BankTransfer, self::Cheque, self::Card]);
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
