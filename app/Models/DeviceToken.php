<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Push notification token of a mobile device (Expo / FCM / APNs).
 */
class DeviceToken extends Model
{
    public const PLATFORMS = ['ios', 'android'];

    protected $fillable = [
        'user_id',
        'token',
        'platform',
        'device_name',
        'locale',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
