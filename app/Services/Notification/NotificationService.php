<?php

declare(strict_types=1);

namespace App\Services\Notification;

use App\Enums\NotificationEvent;
use App\Enums\NotificationStatus;
use App\Enums\NotificationType;
use App\Jobs\Notification\SendNotificationJob;
use App\Models\Booking;
use App\Models\BookingNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * NotificationService
 * 
 * Main orchestrator for the notification system.
 * Handles notification creation, queueing, and coordination between channels.
 * 
 * @package App\Services\Notification
 */
class NotificationService
{
    /**
     * Create and dispatch notifications for a booking event.
     *
     * @param Booking $booking
     * @param NotificationEvent $event
     * @param array $additionalData Additional context data
     * @return array<BookingNotification> Created notifications
     */
    public function notify(
        Booking $booking,
        NotificationEvent $event,
        array $additionalData = []
    ): array {
        $notifications = [];

        // Get enabled channels for this event
        $channels = $this->getEnabledChannels($event);

        // Get recipients for this event
        $recipients = $this->getRecipients($booking, $event);

        foreach ($recipients as $recipientType => $recipientData) {
            foreach ($channels as $channel) {
                try {
                    $notification = $this->createNotification(
                        booking: $booking,
                        type: $channel,
                        event: $event,
                        recipientType: $recipientType,
                        recipientData: $recipientData,
                        additionalData: $additionalData
                    );

                    if ($notification) {
                        $notifications[] = $notification;

                        // Dispatch to queue
                        $this->dispatchNotification($notification);
                    }
                } catch (\Throwable $e) {
                    Log::error('Failed to create notification', [
                        'booking_id' => $booking->id,
                        'event' => $event->value,
                        'channel' => $channel->value,
                        'recipient_type' => $recipientType,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $notifications;
    }

    /**
     * Send a single notification for a specific booking event to a customer.
     *
     * @param Booking $booking
     * @param NotificationEvent $event
     * @param array $additionalData
     * @return BookingNotification|null
     */
    public function notifyCustomer(
        Booking $booking,
        NotificationEvent $event,
        array $additionalData = []
    ): ?BookingNotification {
        // For now, only email channel
        $channel = NotificationType::EMAIL;

        if (!$channel->isEnabled()) {
            return null;
        }

        $notification = $this->createNotification(
            booking: $booking,
            type: $channel,
            event: $event,
            recipientType: 'customer',
            recipientData: [
                'user' => $booking->user,
                'email' => $booking->customer_email,
                'phone' => $booking->customer_phone,
                'name' => $booking->customer_name,
                'locale' => $booking->user?->language_preference ?? 'en',
            ],
            additionalData: $additionalData
        );

        if ($notification) {
            $this->dispatchNotification($notification);
        }

        return $notification;
    }

    /**
     * Send a notification to the hall owner.
     *
     * @param Booking $booking
     * @param NotificationEvent $event
     * @param array $additionalData
     * @return BookingNotification|null
     */
    public function notifyOwner(
        Booking $booking,
        NotificationEvent $event,
        array $additionalData = []
    ): ?BookingNotification {
        $channel = NotificationType::EMAIL;

        if (!$channel->isEnabled()) {
            return null;
        }

        $owner = $booking->hall?->owner;
        if (!$owner) {
            return null;
        }

        $notification = $this->createNotification(
            booking: $booking,
            type: $channel,
            event: $event,
            recipientType: 'owner',
            recipientData: [
                'user' => $owner,
                'email' => $owner->email,
                'phone' => $owner->phone,
                'name' => $owner->name,
                'locale' => $owner->language_preference ?? 'en',
            ],
            additionalData: $additionalData
        );

        if ($notification) {
            $this->dispatchNotification($notification);
        }

        return $notification;
    }

    /**
     * Get enabled channels for an event.
     *
     * @param NotificationEvent $event
     * @return array<NotificationType>
     */
    protected function getEnabledChannels(NotificationEvent $event): array
    {
        $defaultChannels = $event->defaultChannels();

        return array_filter(
            $defaultChannels,
            fn (NotificationType $type) => $type->isEnabled()
        );
    }

    /**
     * Get recipients for an event.
     *
     * @param Booking $booking
     * @param NotificationEvent $event
     * @return array<string, array>
     */
    protected function getRecipients(Booking $booking, NotificationEvent $event): array
    {
        $recipientTypes = $event->recipients();
        $recipients = [];

        foreach ($recipientTypes as $type) {
            if ($type === 'customer') {
                $recipients['customer'] = [
                    'user' => $booking->user,
                    'email' => $booking->customer_email,
                    'phone' => $booking->customer_phone,
                    'name' => $booking->customer_name,
                    'locale' => $booking->user?->language_preference ?? 'en',
                ];
            }

            if ($type === 'owner') {
                $owner = $booking->hall?->owner;
                if ($owner) {
                    $recipients['owner'] = [
                        'user' => $owner,
                        'email' => $owner->email,
                        'phone' => $owner->phone,
                        'name' => $owner->name,
                        'locale' => $owner->language_preference ?? 'en',
                    ];
                }
            }
        }

        return $recipients;
    }

    /**
     * Create a notification record.
     *
     * @param Booking $booking
     * @param NotificationType $type
     * @param NotificationEvent $event
     * @param string $recipientType
     * @param array $recipientData
     * @param array $additionalData
     * @return BookingNotification|null
     */
    protected function createNotification(
        Booking $booking,
        NotificationType $type,
        NotificationEvent $event,
        string $recipientType,
        array $recipientData,
        array $additionalData = []
    ): ?BookingNotification {
        // Skip if no valid recipient contact
        if ($type === NotificationType::EMAIL && empty($recipientData['email'])) {
            return null;
        }

        if ($type === NotificationType::SMS && empty($recipientData['phone'])) {
            return null;
        }

        $locale = $recipientData['locale'] ?? 'en';

        // Build notification data
        $notificationData = array_merge([
            'booking_number' => $booking->booking_number,
            'hall_name' => $this->getHallName($booking, $locale),
            'booking_date' => $booking->booking_date?->format('d M Y'),
            'time_slot' => $this->getTimeSlotLabel($booking->time_slot, $locale),
            'customer_name' => $booking->customer_name,
            'total_amount' => 'OMR ' . number_format((float) $booking->total_amount, 3),
            'recipient_type' => $recipientType,
            'locale' => $locale,
        ], $additionalData);

        // Generate subject and message
        $subject = $event->subject($locale);
        $message = $this->generateMessage($event, $notificationData, $locale);

        return BookingNotification::create([
            'booking_id' => $booking->id,
            'user_id' => $recipientData['user']?->id,
            'type' => $type->value,
            'event' => $event->value,
            'recipient_email' => $recipientData['email'] ?? null,
            'recipient_phone' => $recipientData['phone'] ?? null,
            'subject' => $subject,
            'message' => $message,
            'data' => $notificationData,
            'status' => NotificationStatus::PENDING->value,
        ]);
    }

    /**
     * Dispatch notification to queue.
     *
     * @param BookingNotification $notification
     * @return void
     */
    protected function dispatchNotification(BookingNotification $notification): void
    {
        $queue = match ($notification->type) {
            NotificationType::SMS->value => 'sms',
            default => 'notifications',
        };

        $delay = config("notifications.channels.{$notification->type}.delay", 0);

        SendNotificationJob::dispatch($notification)
            ->onQueue($queue)
            ->delay(now()->addSeconds($delay));
    }

    /**
     * Get localized hall name.
     *
     * @param Booking $booking
     * @param string $locale
     * @return string
     */
    protected function getHallName(Booking $booking, string $locale): string
    {
        $hall = $booking->hall;
        if (!$hall) {
            return 'N/A';
        }

        $name = $hall->name;
        if (is_array($name)) {
            return $name[$locale] ?? $name['en'] ?? 'N/A';
        }

        return (string) $name;
    }

    /**
     * Get localized time slot label.
     *
     * @param string|null $slot
     * @param string $locale
     * @return string
     */
    protected function getTimeSlotLabel(?string $slot, string $locale): string
    {
        if (!$slot) {
            return 'N/A';
        }

        $labels = [
            'en' => [
                'morning' => 'Morning',
                'afternoon' => 'Afternoon',
                'evening' => 'Evening',
                'full_day' => 'Full Day',
            ],
            'ar' => [
                'morning' => 'صباحي',
                'afternoon' => 'ظهري',
                'evening' => 'مسائي',
                'full_day' => 'يوم كامل',
            ],
        ];

        return $labels[$locale][$slot] ?? ucfirst(str_replace('_', ' ', $slot));
    }

    /**
     * Generate message content for notification.
     *
     * @param NotificationEvent $event
     * @param array $data
     * @param string $locale
     * @return string
     */
    protected function generateMessage(
        NotificationEvent $event,
        array $data,
        string $locale
    ): string {
        // This will be replaced by actual template rendering
        // For now, return a simple message
        return $this->getSimpleMessage($event, $data, $locale);
    }

    /**
     * Get simple message for notification (fallback).
     *
     * @param NotificationEvent $event
     * @param array $data
     * @param string $locale
     * @return string
     */
    protected function getSimpleMessage(
        NotificationEvent $event,
        array $data,
        string $locale
    ): string {
        $bookingNumber = $data['booking_number'] ?? '';
        $hallName = $data['hall_name'] ?? '';
        $bookingDate = $data['booking_date'] ?? '';

        if ($locale === 'ar') {
            return match ($event) {
                NotificationEvent::BOOKING_APPROVED => "تمت الموافقة على حجزك رقم {$bookingNumber} في {$hallName} بتاريخ {$bookingDate}.",
                NotificationEvent::BOOKING_REJECTED => "نأسف، تم رفض حجزك رقم {$bookingNumber}. السبب: " . ($data['rejection_reason'] ?? 'غير محدد'),
                NotificationEvent::BOOKING_CONFIRMED => "تم تأكيد حجزك رقم {$bookingNumber} في {$hallName} بتاريخ {$bookingDate}.",
                NotificationEvent::BOOKING_CANCELLED => "تم إلغاء حجزك رقم {$bookingNumber}.",
                NotificationEvent::PAYMENT_RECEIVED => "تم استلام دفعتك لحجز رقم {$bookingNumber}. شكراً لك!",
                NotificationEvent::BALANCE_RECEIVED => "تم استلام المبلغ المتبقي لحجزك رقم {$bookingNumber}.",
                default => "تحديث على حجزك رقم {$bookingNumber}.",
            };
        }

        return match ($event) {
            NotificationEvent::BOOKING_APPROVED => "Your booking #{$bookingNumber} at {$hallName} on {$bookingDate} has been approved!",
            NotificationEvent::BOOKING_REJECTED => "We're sorry, your booking #{$bookingNumber} has been declined. Reason: " . ($data['rejection_reason'] ?? 'Not specified'),
            NotificationEvent::BOOKING_CONFIRMED => "Your booking #{$bookingNumber} at {$hallName} on {$bookingDate} is confirmed!",
            NotificationEvent::BOOKING_CANCELLED => "Your booking #{$bookingNumber} has been cancelled.",
            NotificationEvent::PAYMENT_RECEIVED => "Payment received for booking #{$bookingNumber}. Thank you!",
            NotificationEvent::BALANCE_RECEIVED => "Balance payment received for booking #{$bookingNumber}.",
            default => "Update on your booking #{$bookingNumber}.",
        };
    }

    /**
     * Retry a failed notification.
     *
     * @param BookingNotification $notification
     * @return bool
     */
    public function retry(BookingNotification $notification): bool
    {
        if (!$notification->canRetry()) {
            return false;
        }

        $notification->resetForRetry();
        $this->dispatchNotification($notification);

        return true;
    }

    /**
     * Retry all failed notifications that can be retried.
     *
     * @return int Number of notifications queued for retry
     */
    public function retryAllFailed(): int
    {
        $notifications = BookingNotification::retryable()->get();
        $count = 0;

        foreach ($notifications as $notification) {
            if ($this->retry($notification)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Cancel a pending notification.
     *
     * @param BookingNotification $notification
     * @return bool
     */
    public function cancel(BookingNotification $notification): bool
    {
        if (!$notification->isPending()) {
            return false;
        }

        return $notification->markAsCancelled();
    }
}
