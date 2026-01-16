<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * NotificationEvent Enum
 * 
 * Defines all notification-triggering events in the booking lifecycle.
 * Each event corresponds to a specific notification template and recipient logic.
 * 
 * @package App\Enums
 */
enum NotificationEvent: string
{
    // =========================================================
    // BOOKING LIFECYCLE EVENTS
    // =========================================================

    /**
     * New booking created (pending confirmation).
     */
    case BOOKING_CREATED = 'booking_created';

    /**
     * Booking approved by hall owner.
     */
    case BOOKING_APPROVED = 'booking_approved';

    /**
     * Booking rejected by hall owner.
     */
    case BOOKING_REJECTED = 'booking_rejected';

    /**
     * Booking confirmed (auto or after payment).
     */
    case BOOKING_CONFIRMED = 'booking_confirmed';

    /**
     * Booking cancelled by customer or system.
     */
    case BOOKING_CANCELLED = 'booking_cancelled';

    /**
     * Booking completed (event finished).
     */
    case BOOKING_COMPLETED = 'booking_completed';

    // =========================================================
    // PAYMENT EVENTS
    // =========================================================

    /**
     * Payment received successfully.
     */
    case PAYMENT_RECEIVED = 'payment_received';

    /**
     * Payment failed.
     */
    case PAYMENT_FAILED = 'payment_failed';

    /**
     * Refund processed.
     */
    case PAYMENT_REFUNDED = 'payment_refunded';

    /**
     * Balance payment received (for advance payment bookings).
     */
    case BALANCE_RECEIVED = 'balance_received';

    /**
     * Balance payment reminder.
     */
    case BALANCE_REMINDER = 'balance_reminder';

    // =========================================================
    // REMINDER EVENTS
    // =========================================================

    /**
     * Event reminder (1 day before).
     */
    case EVENT_REMINDER = 'event_reminder';

    /**
     * Review request (after event completion).
     */
    case REVIEW_REQUEST = 'review_request';

    /**
     * Get human-readable label.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::BOOKING_CREATED => __('Booking Created'),
            self::BOOKING_APPROVED => __('Booking Approved'),
            self::BOOKING_REJECTED => __('Booking Rejected'),
            self::BOOKING_CONFIRMED => __('Booking Confirmed'),
            self::BOOKING_CANCELLED => __('Booking Cancelled'),
            self::BOOKING_COMPLETED => __('Booking Completed'),
            self::PAYMENT_RECEIVED => __('Payment Received'),
            self::PAYMENT_FAILED => __('Payment Failed'),
            self::PAYMENT_REFUNDED => __('Payment Refunded'),
            self::BALANCE_RECEIVED => __('Balance Received'),
            self::BALANCE_REMINDER => __('Balance Reminder'),
            self::EVENT_REMINDER => __('Event Reminder'),
            self::REVIEW_REQUEST => __('Review Request'),
        };
    }

    /**
     * Get notification subject line.
     *
     * @param string $locale
     * @return string
     */
    public function subject(string $locale = 'en'): string
    {
        if ($locale === 'ar') {
            return match ($this) {
                self::BOOKING_CREATED => 'تم إنشاء حجزك - مجالس',
                self::BOOKING_APPROVED => 'تمت الموافقة على حجزك - مجالس',
                self::BOOKING_REJECTED => 'تم رفض حجزك - مجالس',
                self::BOOKING_CONFIRMED => 'تم تأكيد حجزك - مجالس',
                self::BOOKING_CANCELLED => 'تم إلغاء حجزك - مجالس',
                self::BOOKING_COMPLETED => 'شكراً لاستخدامك مجالس',
                self::PAYMENT_RECEIVED => 'تم استلام الدفعة - مجالس',
                self::PAYMENT_FAILED => 'فشل الدفع - مجالس',
                self::PAYMENT_REFUNDED => 'تم استرداد المبلغ - مجالس',
                self::BALANCE_RECEIVED => 'تم استلام المبلغ المتبقي - مجالس',
                self::BALANCE_REMINDER => 'تذكير بالمبلغ المتبقي - مجالس',
                self::EVENT_REMINDER => 'تذكير بموعد فعاليتك - مجالس',
                self::REVIEW_REQUEST => 'شاركنا رأيك - مجالس',
            };
        }

        return match ($this) {
            self::BOOKING_CREATED => 'Your Booking Has Been Created - Majalis',
            self::BOOKING_APPROVED => 'Your Booking Has Been Approved - Majalis',
            self::BOOKING_REJECTED => 'Your Booking Has Been Declined - Majalis',
            self::BOOKING_CONFIRMED => 'Your Booking is Confirmed - Majalis',
            self::BOOKING_CANCELLED => 'Your Booking Has Been Cancelled - Majalis',
            self::BOOKING_COMPLETED => 'Thank You for Using Majalis',
            self::PAYMENT_RECEIVED => 'Payment Received - Majalis',
            self::PAYMENT_FAILED => 'Payment Failed - Majalis',
            self::PAYMENT_REFUNDED => 'Refund Processed - Majalis',
            self::BALANCE_RECEIVED => 'Balance Payment Received - Majalis',
            self::BALANCE_REMINDER => 'Balance Payment Reminder - Majalis',
            self::EVENT_REMINDER => 'Your Event is Tomorrow - Majalis',
            self::REVIEW_REQUEST => 'Share Your Experience - Majalis',
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
            self::BOOKING_CREATED => 'heroicon-o-plus-circle',
            self::BOOKING_APPROVED => 'heroicon-o-check-circle',
            self::BOOKING_REJECTED => 'heroicon-o-x-circle',
            self::BOOKING_CONFIRMED => 'heroicon-o-check-badge',
            self::BOOKING_CANCELLED => 'heroicon-o-x-mark',
            self::BOOKING_COMPLETED => 'heroicon-o-trophy',
            self::PAYMENT_RECEIVED => 'heroicon-o-banknotes',
            self::PAYMENT_FAILED => 'heroicon-o-exclamation-triangle',
            self::PAYMENT_REFUNDED => 'heroicon-o-arrow-uturn-left',
            self::BALANCE_RECEIVED => 'heroicon-o-currency-dollar',
            self::BALANCE_REMINDER => 'heroicon-o-clock',
            self::EVENT_REMINDER => 'heroicon-o-calendar',
            self::REVIEW_REQUEST => 'heroicon-o-star',
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
            self::BOOKING_CREATED => 'info',
            self::BOOKING_APPROVED => 'success',
            self::BOOKING_REJECTED => 'danger',
            self::BOOKING_CONFIRMED => 'success',
            self::BOOKING_CANCELLED => 'danger',
            self::BOOKING_COMPLETED => 'info',
            self::PAYMENT_RECEIVED => 'success',
            self::PAYMENT_FAILED => 'danger',
            self::PAYMENT_REFUNDED => 'warning',
            self::BALANCE_RECEIVED => 'success',
            self::BALANCE_REMINDER => 'warning',
            self::EVENT_REMINDER => 'info',
            self::REVIEW_REQUEST => 'purple',
        };
    }

    /**
     * Get recipient types for this event.
     * Returns who should receive notifications for this event.
     *
     * @return array<string>
     */
    public function recipients(): array
    {
        return match ($this) {
            // Customer only
            self::BOOKING_APPROVED,
            self::BOOKING_REJECTED,
            self::BALANCE_REMINDER,
            self::EVENT_REMINDER,
            self::REVIEW_REQUEST => ['customer'],

            // Customer + Owner
            self::BOOKING_CREATED,
            self::BOOKING_CONFIRMED,
            self::BOOKING_CANCELLED,
            self::PAYMENT_RECEIVED,
            self::BALANCE_RECEIVED => ['customer', 'owner'],

            // Customer only for these
            self::PAYMENT_FAILED,
            self::PAYMENT_REFUNDED,
            self::BOOKING_COMPLETED => ['customer'],
        };
    }

    /**
     * Get default notification types for this event.
     *
     * @return array<NotificationType>
     */
    public function defaultChannels(): array
    {
        return match ($this) {
            // High priority - Email + SMS
            self::BOOKING_APPROVED,
            self::BOOKING_REJECTED,
            self::BOOKING_CONFIRMED,
            self::PAYMENT_RECEIVED => [
                NotificationType::EMAIL,
                NotificationType::SMS,
            ],

            // Medium priority - Email only
            self::BOOKING_CREATED,
            self::BOOKING_CANCELLED,
            self::BALANCE_RECEIVED,
            self::PAYMENT_REFUNDED => [
                NotificationType::EMAIL,
            ],

            // Low priority - Email only
            self::BOOKING_COMPLETED,
            self::PAYMENT_FAILED,
            self::BALANCE_REMINDER,
            self::EVENT_REMINDER,
            self::REVIEW_REQUEST => [
                NotificationType::EMAIL,
            ],
        };
    }

    /**
     * Get options for Filament select fields.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $event) => [$event->value => $event->label()])
            ->toArray();
    }

    /**
     * Get booking-related events only.
     *
     * @return array<self>
     */
    public static function bookingEvents(): array
    {
        return [
            self::BOOKING_CREATED,
            self::BOOKING_APPROVED,
            self::BOOKING_REJECTED,
            self::BOOKING_CONFIRMED,
            self::BOOKING_CANCELLED,
            self::BOOKING_COMPLETED,
        ];
    }

    /**
     * Get payment-related events only.
     *
     * @return array<self>
     */
    public static function paymentEvents(): array
    {
        return [
            self::PAYMENT_RECEIVED,
            self::PAYMENT_FAILED,
            self::PAYMENT_REFUNDED,
            self::BALANCE_RECEIVED,
            self::BALANCE_REMINDER,
        ];
    }
}
