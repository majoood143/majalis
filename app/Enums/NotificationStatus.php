<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * NotificationStatus Enum
 * 
 * Defines the possible states of a notification in its lifecycle.
 * 
 * @package App\Enums
 */
enum NotificationStatus: string
{
    /**
     * Notification is queued and waiting to be sent.
     */
    case PENDING = 'pending';

    /**
     * Notification is currently being processed.
     */
    case PROCESSING = 'processing';

    /**
     * Notification was sent successfully.
     */
    case SENT = 'sent';

    /**
     * Notification was delivered (confirmed by provider).
     */
    case DELIVERED = 'delivered';

    /**
     * Notification failed to send.
     */
    case FAILED = 'failed';

    /**
     * Notification was cancelled before sending.
     */
    case CANCELLED = 'cancelled';

    /**
     * Get human-readable label.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('Pending'),
            self::PROCESSING => __('Processing'),
            self::SENT => __('Sent'),
            self::DELIVERED => __('Delivered'),
            self::FAILED => __('Failed'),
            self::CANCELLED => __('Cancelled'),
        };
    }

    /**
     * Get icon for UI display.
     *
     * @return string
     */
    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::PROCESSING => 'heroicon-o-arrow-path',
            self::SENT => 'heroicon-o-paper-airplane',
            self::DELIVERED => 'heroicon-o-check-circle',
            self::FAILED => 'heroicon-o-x-circle',
            self::CANCELLED => 'heroicon-o-minus-circle',
        };
    }

    /**
     * Get color for UI display.
     *
     * @return string
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PROCESSING => 'info',
            self::SENT => 'success',
            self::DELIVERED => 'success',
            self::FAILED => 'danger',
            self::CANCELLED => 'gray',
        };
    }

    /**
     * Check if notification can be retried.
     *
     * @return bool
     */
    public function canRetry(): bool
    {
        return $this === self::FAILED;
    }

    /**
     * Check if notification is in a final state.
     *
     * @return bool
     */
    public function isFinal(): bool
    {
        return in_array($this, [
            self::SENT,
            self::DELIVERED,
            self::CANCELLED,
        ]);
    }

    /**
     * Get options for Filament select fields.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status) => [$status->value => $status->label()])
            ->toArray();
    }
}
