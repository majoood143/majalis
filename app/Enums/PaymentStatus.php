<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
    case PARTIALLY_REFUNDED = 'partially_refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('Pending Payment'),
            self::PAID => __('Paid'),
            self::FAILED => __('Failed'),
            self::REFUNDED => __('Refunded'),
            self::PARTIALLY_REFUNDED => __('Partially Refunded'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PAID => 'success',
            self::FAILED => 'danger',
            self::REFUNDED => 'gray',
            self::PARTIALLY_REFUNDED => 'info',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::PAID => 'heroicon-o-check-circle',
            self::FAILED => 'heroicon-o-x-circle',
            self::REFUNDED => 'heroicon-o-arrow-path',
            self::PARTIALLY_REFUNDED => 'heroicon-o-arrow-path-rounded-square',
        };
    }
}
