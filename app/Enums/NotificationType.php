<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * NotificationType Enum
 * 
 * Defines the available notification delivery channels.
 * Used to determine how notifications are sent to recipients.
 * 
 * @package App\Enums
 */
enum NotificationType: string
{
    /**
     * Email notification via SMTP/Mail service.
     */
    case EMAIL = 'email';

    /**
     * SMS notification via SMS gateway.
     */
    case SMS = 'sms';

    /**
     * Push notification via FCM/APNs.
     */
    case PUSH = 'push';

    /**
     * In-app notification (database).
     */
    case IN_APP = 'in_app';

    /**
     * WhatsApp notification via Business API.
     */
    case WHATSAPP = 'whatsapp';

    /**
     * Get human-readable label for the notification type.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::EMAIL => __('Email'),
            self::SMS => __('SMS'),
            self::PUSH => __('Push Notification'),
            self::IN_APP => __('In-App'),
            self::WHATSAPP => __('WhatsApp'),
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
            self::EMAIL => 'heroicon-o-envelope',
            self::SMS => 'heroicon-o-device-phone-mobile',
            self::PUSH => 'heroicon-o-bell',
            self::IN_APP => 'heroicon-o-inbox',
            self::WHATSAPP => 'heroicon-o-chat-bubble-left-ellipsis',
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
            self::EMAIL => 'info',
            self::SMS => 'success',
            self::PUSH => 'warning',
            self::IN_APP => 'gray',
            self::WHATSAPP => 'success',
        };
    }

    /**
     * Check if this notification type is currently enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return match ($this) {
            self::EMAIL => config('notifications.channels.email.enabled', true),
            self::SMS => config('notifications.channels.sms.enabled', false),
            self::PUSH => config('notifications.channels.push.enabled', false),
            self::IN_APP => config('notifications.channels.in_app.enabled', true),
            self::WHATSAPP => config('notifications.channels.whatsapp.enabled', false),
        };
    }

    /**
     * Get all enabled notification types.
     *
     * @return array<self>
     */
    public static function enabled(): array
    {
        return array_filter(
            self::cases(),
            fn (self $type) => $type->isEnabled()
        );
    }

    /**
     * Get options for Filament select fields.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [$type->value => $type->label()])
            ->toArray();
    }
}
