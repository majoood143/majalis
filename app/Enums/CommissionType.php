<?php

namespace App\Enums;

enum CommissionType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';

    public function label(): string
    {
        return match ($this) {
            self::PERCENTAGE => __('Percentage'),
            self::FIXED => __('Fixed Amount'),
        };
    }

    public function symbol(): string
    {
        return match ($this) {
            self::PERCENTAGE => '%',
            self::FIXED => 'OMR',
        };
    }

    public static function options(): array
    {
        return [
            self::PERCENTAGE->value => self::PERCENTAGE->label(),
            self::FIXED->value => self::FIXED->label(),
        ];
    }
}
