<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * PayoutStatus Enum
 *
 * Defines all possible statuses for owner payouts.
 * Used for type-safe status management in the payout workflow.
 *
 * Status Flow:
 * pending -> processing -> completed
 *                      -> failed
 *         -> on_hold -> processing
 *         -> cancelled
 *
 * @package App\Enums
 */
enum PayoutStatus: string
{
    /**
     * Payout is pending processing.
     * Initial status when payout is created.
     */
    case PENDING = 'pending';

    /**
     * Payout is being processed.
     * Payment is in progress with bank/payment processor.
     */
    case PROCESSING = 'processing';

    /**
     * Payout has been completed successfully.
     * Funds have been transferred to owner.
     */
    case COMPLETED = 'completed';

    /**
     * Payout attempt failed.
     * May be retried after fixing the issue.
     */
    case FAILED = 'failed';

    /**
     * Payout has been cancelled.
     * Will not be processed.
     */
    case CANCELLED = 'cancelled';

    /**
     * Payout is temporarily on hold.
     * May be due to verification or dispute.
     */
    case ON_HOLD = 'on_hold';

    /**
     * Get the human-readable label for the status.
     *
     * @return string The translated label
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => __('enums.payout_status.pending'),
            self::PROCESSING => __('enums.payout_status.processing'),
            self::COMPLETED => __('enums.payout_status.completed'),
            self::FAILED => __('enums.payout_status.failed'),
            self::CANCELLED => __('enums.payout_status.cancelled'),
            self::ON_HOLD => __('enums.payout_status.on_hold'),
        };
    }

    /**
     * Get the color associated with this status.
     *
     * @return string Tailwind color class name
     */
    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PROCESSING => 'info',
            self::COMPLETED => 'success',
            self::FAILED => 'danger',
            self::CANCELLED => 'gray',
            self::ON_HOLD => 'warning',
        };
    }

    /**
     * Get the icon for this status.
     *
     * @return string Heroicon name
     */
    public function getIcon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::PROCESSING => 'heroicon-o-arrow-path',
            self::COMPLETED => 'heroicon-o-check-circle',
            self::FAILED => 'heroicon-o-x-circle',
            self::CANCELLED => 'heroicon-o-no-symbol',
            self::ON_HOLD => 'heroicon-o-pause-circle',
        };
    }

    /**
     * Check if the payout can be processed.
     *
     * @return bool True if payout can move to processing
     */
    public function canProcess(): bool
    {
        return in_array($this, [self::PENDING, self::ON_HOLD, self::FAILED], true);
    }

    /**
     * Check if the payout can be cancelled.
     *
     * @return bool True if payout can be cancelled
     */
    public function canCancel(): bool
    {
        return in_array($this, [self::PENDING, self::ON_HOLD], true);
    }

    /**
     * Check if the payout is in a terminal state.
     *
     * @return bool True if payout is finalized
     */
    public function isTerminal(): bool
    {
        return in_array($this, [self::COMPLETED, self::CANCELLED], true);
    }

    /**
     * Get all statuses as an array for select options.
     *
     * @return array<string, string> Status value => label pairs
     */
    public static function toSelectArray(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status): array => [
                $status->value => $status->getLabel(),
            ])
            ->toArray();
    }

    /**
     * Get active statuses (not terminal).
     *
     * @return array<self> Array of active statuses
     */
    public static function activeStatuses(): array
    {
        return [self::PENDING, self::PROCESSING, self::ON_HOLD];
    }
}
