<?php

namespace App\Enums;

enum TimeSlot: string
{
    case MORNING = 'morning';
    case AFTERNOON = 'afternoon';
    case EVENING = 'evening';
    case FULL_DAY = 'full_day';

    public function label(): string
    {
        return match ($this) {
            self::MORNING => __('Morning (8 AM - 12 PM)'),
            self::AFTERNOON => __('Afternoon (12 PM - 5 PM)'),
            self::EVENING => __('Evening (5 PM - 11 PM)'),
            self::FULL_DAY => __('Full Day (8 AM - 11 PM)'),
        };
    }

    public function labelAr(): string
    {
        return match ($this) {
            self::MORNING => 'صباحي (8 صباحاً - 12 ظهراً)',
            self::AFTERNOON => 'ظهري (12 ظهراً - 5 مساءً)',
            self::EVENING => 'مسائي (5 مساءً - 11 مساءً)',
            self::FULL_DAY => 'يوم كامل (8 صباحاً - 11 مساءً)',
        };
    }

    public function startTime(): string
    {
        return match ($this) {
            self::MORNING => '08:00',
            self::AFTERNOON => '12:00',
            self::EVENING => '17:00',
            self::FULL_DAY => '08:00',
        };
    }

    public function endTime(): string
    {
        return match ($this) {
            self::MORNING => '12:00',
            self::AFTERNOON => '17:00',
            self::EVENING => '23:00',
            self::FULL_DAY => '23:00',
        };
    }

    public function durationHours(): int
    {
        return match ($this) {
            self::MORNING => 4,
            self::AFTERNOON => 5,
            self::EVENING => 6,
            self::FULL_DAY => 15,
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($slot) => [$slot->value => $slot->label()])
            ->toArray();
    }
}
