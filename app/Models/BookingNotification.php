<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NotificationEvent;
use App\Enums\NotificationStatus;
use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * BookingNotification Model
 * 
 * Logs all notifications sent for bookings.
 * Tracks delivery status, retries, and provides audit trail.
 * 
 * @property int $id
 * @property int $booking_id
 * @property int|null $user_id
 * @property string $type
 * @property string $event
 * @property string|null $recipient_email
 * @property string|null $recipient_phone
 * @property string|null $subject
 * @property string $message
 * @property array|null $data
 * @property string $status
 * @property string|null $error_message
 * @property int $retry_count
 * @property \Carbon\Carbon|null $sent_at
 * @property \Carbon\Carbon|null $read_at
 * @property \Carbon\Carbon|null $clicked_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read Booking $booking
 * @property-read User|null $user
 * @property-read NotificationType $typeEnum
 * @property-read NotificationEvent $eventEnum
 * @property-read NotificationStatus $statusEnum
 */
class BookingNotification extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'booking_notifications';

    /**
     * Maximum retry attempts for failed notifications.
     */
    public const MAX_RETRY_COUNT = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'user_id',
        'type',
        'event',
        'recipient_email',
        'recipient_phone',
        'subject',
        'message',
        'data',
        'status',
        'error_message',
        'retry_count',
        'sent_at',
        'read_at',
        'clicked_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'retry_count' => 'integer',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'pending',
        'retry_count' => 0,
    ];

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    /**
     * Get the booking this notification belongs to.
     *
     * @return BelongsTo<Booking, BookingNotification>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user this notification was sent to.
     *
     * @return BelongsTo<User, BookingNotification>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // =========================================================
    // ENUM ACCESSORS
    // =========================================================

    /**
     * Get the notification type as enum.
     *
     * @return NotificationType
     */
    public function getTypeEnumAttribute(): NotificationType
    {
        return NotificationType::from($this->type);
    }

    /**
     * Get the notification event as enum.
     *
     * @return NotificationEvent
     */
    public function getEventEnumAttribute(): NotificationEvent
    {
        return NotificationEvent::from($this->event);
    }

    /**
     * Get the notification status as enum.
     *
     * @return NotificationStatus
     */
    public function getStatusEnumAttribute(): NotificationStatus
    {
        return NotificationStatus::from($this->status);
    }

    // =========================================================
    // STATUS HELPERS
    // =========================================================

    /**
     * Check if notification is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === NotificationStatus::PENDING->value;
    }

    /**
     * Check if notification was sent successfully.
     *
     * @return bool
     */
    public function isSent(): bool
    {
        return in_array($this->status, [
            NotificationStatus::SENT->value,
            NotificationStatus::DELIVERED->value,
        ]);
    }

    /**
     * Check if notification failed.
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->status === NotificationStatus::FAILED->value;
    }

    /**
     * Check if notification can be retried.
     *
     * @return bool
     */
    public function canRetry(): bool
    {
        return $this->isFailed() && $this->retry_count < self::MAX_RETRY_COUNT;
    }

    /**
     * Check if notification has been read.
     *
     * @return bool
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    // =========================================================
    // STATUS TRANSITIONS
    // =========================================================

    /**
     * Mark notification as processing.
     *
     * @return bool
     */
    public function markAsProcessing(): bool
    {
        return $this->update([
            'status' => NotificationStatus::PROCESSING->value,
        ]);
    }

    /**
     * Mark notification as sent.
     *
     * @return bool
     */
    public function markAsSent(): bool
    {
        return $this->update([
            'status' => NotificationStatus::SENT->value,
            'sent_at' => now(),
            'error_message' => null,
        ]);
    }

    /**
     * Mark notification as delivered.
     *
     * @return bool
     */
    public function markAsDelivered(): bool
    {
        return $this->update([
            'status' => NotificationStatus::DELIVERED->value,
            'sent_at' => $this->sent_at ?? now(),
        ]);
    }

    /**
     * Mark notification as failed.
     *
     * @param string $errorMessage
     * @return bool
     */
    public function markAsFailed(string $errorMessage): bool
    {
        return $this->update([
            'status' => NotificationStatus::FAILED->value,
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1,
        ]);
    }

    /**
     * Mark notification as cancelled.
     *
     * @return bool
     */
    public function markAsCancelled(): bool
    {
        return $this->update([
            'status' => NotificationStatus::CANCELLED->value,
        ]);
    }

    /**
     * Mark notification as read.
     *
     * @return bool
     */
    public function markAsRead(): bool
    {
        if ($this->read_at !== null) {
            return true;
        }

        return $this->update([
            'read_at' => now(),
        ]);
    }

    /**
     * Mark notification link as clicked.
     *
     * @return bool
     */
    public function markAsClicked(): bool
    {
        return $this->update([
            'clicked_at' => now(),
            'read_at' => $this->read_at ?? now(),
        ]);
    }

    /**
     * Reset for retry.
     *
     * @return bool
     */
    public function resetForRetry(): bool
    {
        if (!$this->canRetry()) {
            return false;
        }

        return $this->update([
            'status' => NotificationStatus::PENDING->value,
            'error_message' => null,
        ]);
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Scope to filter by status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param NotificationStatus|string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, NotificationStatus|string $status)
    {
        $value = $status instanceof NotificationStatus ? $status->value : $status;
        return $query->where('status', $value);
    }

    /**
     * Scope to filter by type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param NotificationType|string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeType($query, NotificationType|string $type)
    {
        $value = $type instanceof NotificationType ? $type->value : $type;
        return $query->where('type', $value);
    }

    /**
     * Scope to filter by event.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param NotificationEvent|string $event
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEvent($query, NotificationEvent|string $event)
    {
        $value = $event instanceof NotificationEvent ? $event->value : $event;
        return $query->where('event', $value);
    }

    /**
     * Scope to get pending notifications.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', NotificationStatus::PENDING->value);
    }

    /**
     * Scope to get failed notifications that can be retried.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRetryable($query)
    {
        return $query
            ->where('status', NotificationStatus::FAILED->value)
            ->where('retry_count', '<', self::MAX_RETRY_COUNT);
    }

    /**
     * Scope to get unread notifications.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // =========================================================
    // FACTORY METHODS
    // =========================================================

    /**
     * Create a new notification record.
     *
     * @param Booking $booking
     * @param NotificationType $type
     * @param NotificationEvent $event
     * @param array $attributes
     * @return static
     */
    public static function createForBooking(
        Booking $booking,
        NotificationType $type,
        NotificationEvent $event,
        array $attributes = []
    ): static {
        return static::create(array_merge([
            'booking_id' => $booking->id,
            'type' => $type->value,
            'event' => $event->value,
            'status' => NotificationStatus::PENDING->value,
        ], $attributes));
    }
}
