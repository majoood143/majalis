<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = ['group', 'name', 'locked', 'payload'];

    protected $casts = [
        'payload' => 'json',
        'locked'  => 'boolean',
    ];

    /**
     * Get a setting value by group and name.
     */
    public static function get(string $group, string $name, mixed $default = null): mixed
    {
        $setting = static::where('group', $group)->where('name', $name)->first();

        return $setting !== null ? $setting->payload : $default;
    }

    /**
     * Set a setting value by group and name.
     */
    public static function set(string $group, string $name, mixed $value): void
    {
        static::updateOrCreate(
            ['group' => $group, 'name' => $name],
            ['payload' => $value]
        );
    }

    /**
     * Get all settings for a group as a key => value array.
     */
    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
            ->pluck('payload', 'name')
            ->toArray();
    }
}
