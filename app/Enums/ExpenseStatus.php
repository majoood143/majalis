<?php

declare(strict_types=1);

/**
 * ExpenseStatus Enum
 * 
 * Defines the workflow status for expenses.
 * Supports optional approval workflow for larger organizations.
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
 * ExpenseStatus Enum
 * 
 * Tracks the lifecycle status of an expense:
 * - Draft: Not yet submitted
 * - Submitted: Awaiting approval (if workflow enabled)
 * - Approved: Approved and counted in reports
 * - Rejected: Rejected by approver
 * - Archived: Archived for historical purposes
 */
enum ExpenseStatus: string implements HasLabel, HasColor, HasIcon
{
    /**
     * Draft - expense is being prepared
     */
    case Draft = 'draft';
    
    /**
     * Submitted - awaiting approval
     */
    case Submitted = 'submitted';
    
    /**
     * Approved - expense is approved and final
     */
    case Approved = 'approved';
    
    /**
     * Rejected - expense was rejected
     */
    case Rejected = 'rejected';
    
    /**
     * Archived - kept for historical records
     */
    case Archived = 'archived';

    /**
     * Get the display label for the status
     *
     * @return string The localized label
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => app()->getLocale() === 'ar' ? 'مسودة' : 'Draft',
            self::Submitted => app()->getLocale() === 'ar' ? 'تم الإرسال' : 'Submitted',
            self::Approved => app()->getLocale() === 'ar' ? 'معتمد' : 'Approved',
            self::Rejected => app()->getLocale() === 'ar' ? 'مرفوض' : 'Rejected',
            self::Archived => app()->getLocale() === 'ar' ? 'مؤرشف' : 'Archived',
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
            self::Draft => 'gray',
            self::Submitted => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::Archived => 'gray',
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
            self::Draft => 'heroicon-o-pencil-square',
            self::Submitted => 'heroicon-o-clock',
            self::Approved => 'heroicon-o-check-circle',
            self::Rejected => 'heroicon-o-x-circle',
            self::Archived => 'heroicon-o-archive-box',
        };
    }

    /**
     * Check if expense is editable in this status
     *
     * @return bool
     */
    public function isEditable(): bool
    {
        return in_array($this, [self::Draft, self::Rejected]);
    }

    /**
     * Check if expense counts in financial reports
     *
     * @return bool
     */
    public function countsInReports(): bool
    {
        return $this === self::Approved;
    }

    /**
     * Check if status transition is allowed
     *
     * @param self $newStatus
     * @return bool
     */
    public function canTransitionTo(self $newStatus): bool
    {
        return match ($this) {
            self::Draft => in_array($newStatus, [self::Submitted, self::Approved]),
            self::Submitted => in_array($newStatus, [self::Approved, self::Rejected]),
            self::Approved => $newStatus === self::Archived,
            self::Rejected => in_array($newStatus, [self::Draft, self::Submitted]),
            self::Archived => false,
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

    /**
     * Get statuses that count in reports
     *
     * @return array<self>
     */
    public static function reportableStatuses(): array
    {
        return [self::Approved];
    }
}
