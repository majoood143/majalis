<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case HALL_OWNER = 'hall_owner';
    case CUSTOMER = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => __('Administrator'),
            self::HALL_OWNER => __('Hall Owner'),
            self::CUSTOMER => __('Customer'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ADMIN => 'danger',
            self::HALL_OWNER => 'warning',
            self::CUSTOMER => 'success',
        };
    }

    public function canAccessPanel(): bool
    {
        return match ($this) {
            self::ADMIN, self::HALL_OWNER => true,
            self::CUSTOMER => false,
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($role) => [$role->value => $role->label()])
            ->toArray();
    }
}
