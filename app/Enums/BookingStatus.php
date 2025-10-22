<?php

namespace App\Enums;

enum BookingStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('Pending'),
            self::CONFIRMED => __('Confirmed'),
            self::CANCELLED => __('Cancelled'),
            self::COMPLETED => __('Completed'),
            self::REFUNDED => __('Refunded'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'success',
            self::CANCELLED => 'danger',
            self::COMPLETED => 'info',
            self::REFUNDED => 'gray',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::CONFIRMED => 'heroicon-o-check-circle',
            self::CANCELLED => 'heroicon-o-x-circle',
            self::COMPLETED => 'heroicon-o-check-badge',
            self::REFUNDED => 'heroicon-o-arrow-path',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($status) => [$status->value => $status->label()])
            ->toArray();
    }
}
