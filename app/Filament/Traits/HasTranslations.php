<?php

declare(strict_types=1);

namespace App\Filament\Traits;

/**
 * HasTranslations Trait
 *
 * Provides translation helper methods for Filament Resources.
 * This trait centralizes translation logic and makes it easy to
 * apply localized labels throughout your admin panel.
 *
 * @package    App\Filament\Traits
 * @author     Majalis Development Team
 * @version    1.1.0
 * @since      2025-01-01
 *
 * Usage in a Resource class:
 * ```php
 * use App\Filament\Traits\HasTranslations;
 *
 * class BookingResource extends Resource
 * {
 *     use HasTranslations;
 *
 *     // Override this method to specify your translation file
 *     protected static function getTranslationNamespace(): string
 *     {
 *         return 'booking';
 *     }
 *
 *     public static function getModelLabel(): string
 *     {
 *         return static::trans('resource.label');
 *     }
 * }
 * ```
 */
trait HasTranslations
{
    /**
     * Get the translation namespace for this resource
     *
     * Override this method in your resource class to specify
     * which translation file to use.
     *
     * @return string The translation namespace (e.g., 'booking', 'hall', 'user')
     *
     * @example
     * ```php
     * protected static function getTranslationNamespace(): string
     * {
     *     return 'booking';
     * }
     * ```
     */
    protected static function getTranslationNamespace(): string
    {
        // Auto-generate from resource class name if not overridden
        // e.g., BookingResource -> booking
        $className = class_basename(static::class);

        return str($className)
            ->replace('Resource', '')
            ->snake()
            ->toString();
    }

    /**
     * Get a translated string from the resource's translation file
     *
     * @param string      $key     The translation key (e.g., 'resource.label')
     * @param array       $replace Replacement parameters for the translation
     * @param string|null $locale  The locale to use (defaults to current app locale)
     *
     * @return string The translated string
     *
     * @example
     * ```php
     * // Simple translation
     * static::trans('resource.label');
     * // Output: "Booking" or "حجز"
     *
     * // With parameters
     * static::trans('notifications.slot_booked', ['number' => 'BK-2025-00001']);
     * // Output: "This time slot is already booked (Booking #BK-2025-00001)"
     * ```
     */
    public static function trans(string $key, array $replace = [], ?string $locale = null): string
    {
        $namespace = static::getTranslationNamespace();

        return __("{$namespace}.{$key}", $replace, $locale);
    }

    /**
     * Get a translated field label
     *
     * Convenience method for getting form field labels.
     *
     * @param string $field The field name
     *
     * @return string The translated label
     *
     * @example
     * ```php
     * TextInput::make('booking_number')
     *     ->label(static::fieldLabel('booking_number'));
     * ```
     */
    public static function fieldLabel(string $field): string
    {
        return static::trans("fields.{$field}.label");
    }

    /**
     * Get a translated field placeholder
     *
     * @param string $field The field name
     *
     * @return string The translated placeholder
     */
    public static function fieldPlaceholder(string $field): string
    {
        return static::trans("fields.{$field}.placeholder");
    }

    /**
     * Get a translated field helper text
     *
     * @param string $field   The field name
     * @param array  $replace Replacement parameters
     *
     * @return string The translated helper text
     */
    public static function fieldHelper(string $field, array $replace = []): string
    {
        return static::trans("fields.{$field}.helper", $replace);
    }

    /**
     * Get a translated section title
     *
     * @param string $section The section name
     *
     * @return string The translated section title
     */
    public static function sectionTitle(string $section): string
    {
        return static::trans("sections.{$section}.title");
    }

    /**
     * Get a translated section description
     *
     * @param string $section The section name
     *
     * @return string The translated section description
     */
    public static function sectionDescription(string $section): string
    {
        return static::trans("sections.{$section}.description");
    }

    /**
     * Get a translated table column label
     *
     * @param string $column The column name
     *
     * @return string The translated column label
     */
    public static function columnLabel(string $column): string
    {
        return static::trans("table.columns.{$column}");
    }

    /**
     * Get a translated tab label
     *
     * @param string $tab The tab name
     *
     * @return string The translated tab label
     */
    public static function tabLabel(string $tab): string
    {
        return static::trans("tabs.{$tab}");
    }

    /**
     * Get a translated action label
     *
     * @param string $action The action name
     * @param string $subkey Optional sub-key (e.g., 'label', 'modal_heading')
     *
     * @return string The translated action label
     */
    public static function actionLabel(string $action, string $subkey = 'label'): string
    {
        // Check if it's a simple action or has sub-keys
        $key = "actions.{$action}";

        // If subkey is provided and action has sub-keys, use them
        if ($subkey !== 'label') {
            return static::trans("{$key}.{$subkey}");
        }

        // Try the nested structure first
        $nested = static::trans("{$key}.label");

        // If it returns the same key (not translated), try direct key
        if ($nested === "{$key}.label" || str_contains($nested, '.label')) {
            return static::trans($key);
        }

        return $nested;
    }

    /**
     * Get a translated notification title
     *
     * @param string $notification The notification key
     *
     * @return string The translated notification title
     */
    public static function notificationTitle(string $notification): string
    {
        return static::trans("notifications.{$notification}.title");
    }

    /**
     * Get a translated notification body
     *
     * @param string $notification The notification key
     * @param array  $replace      Replacement parameters
     *
     * @return string The translated notification body
     */
    public static function notificationBody(string $notification, array $replace = []): string
    {
        return static::trans("notifications.{$notification}.body", $replace);
    }

    /**
     * Get a translated status label
     *
     * @param string $type   The status type (e.g., 'booking', 'payment')
     * @param string $status The status value
     *
     * @return string The translated status label
     */
    public static function statusLabel(string $type, string $status): string
    {
        return static::trans("statuses.{$type}.{$status}");
    }

    /**
     * Get translated status options for a select field
     *
     * @param string $type The status type (e.g., 'booking', 'payment')
     *
     * @return array<string, string> Array of status value => translated label
     *
     * @example
     * ```php
     * Select::make('status')
     *     ->options(static::statusOptions('booking'));
     * ```
     */
    public static function statusOptions(string $type): array
    {
        $statuses = config("majalis.statuses.{$type}", []);
        $options = [];

        foreach ($statuses as $status) {
            $options[$status] = static::statusLabel($type, $status);
        }

        return $options;
    }

    /**
     * Get translated time slot options
     *
     * @return array<string, string> Array of slot value => translated label
     */
    public static function timeSlotOptions(): array
    {
        $slots = ['morning', 'afternoon', 'evening', 'full_day', 'morning_afternoon', 'afternoon_evening'];
        $options = [];

        foreach ($slots as $slot) {
            $options[$slot] = static::trans("time_slots.{$slot}");
        }

        return $options;
    }

    /**
     * Get translated event type options
     *
     * @return array<string, string> Array of event type value => translated label
     */
    public static function eventTypeOptions(): array
    {
        $types = [
            'wedding',
            'engagement',
            'birthday',
            'corporate',
            'conference',
            'seminar',
            'workshop',
            'exhibition',
            'graduation',
            'anniversary',
            'memorial',
            'religious',
            'social',
            'other',
        ];

        $options = [];

        foreach ($types as $type) {
            $options[$type] = static::trans("event_types.{$type}");
        }

        return $options;
    }

    /**
     * Get translated payment method options
     *
     * @return array<string, string> Array of payment method value => translated label
     */
    public static function paymentMethodOptions(): array
    {
        $methods = ['thawani', 'bank_transfer', 'cash', 'card', 'cheque'];
        $options = [];

        foreach ($methods as $method) {
            $options[$method] = static::trans("payment_methods.{$method}");
        }

        return $options;
    }

    /**
     * Get a general message translation
     *
     * @param string $key     The message key
     * @param array  $replace Replacement parameters
     *
     * @return string The translated message
     */
    public static function message(string $key, array $replace = []): string
    {
        return static::trans("messages.{$key}", $replace);
    }

    /**
     * Get a validation message translation
     *
     * @param string $key     The validation key
     * @param array  $replace Replacement parameters
     *
     * @return string The translated validation message
     */
    public static function validationMessage(string $key, array $replace = []): string
    {
        return static::trans("validation.{$key}", $replace);
    }
}
