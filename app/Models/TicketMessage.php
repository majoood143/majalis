<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

/**
 * Ticket Message Model
 * 
 * Represents a single message/response in a ticket conversation thread.
 * Supports file attachments, read tracking, and visibility control.
 * 
 * @package App\Models
 * @version 1.0.0
 * 
 * @property int $id
 * @property int $ticket_id
 * @property int $user_id
 * @property TicketMessageType $type
 * @property string $message
 * @property array|null $attachments
 * @property bool $is_read
 * @property \Carbon\Carbon|null $read_at
 * @property bool $is_internal
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property array|null $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class TicketMessage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_id',
        'user_id',
        'type',
        'message',
        'attachments',
        'is_read',
        'read_at',
        'is_internal',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => TicketMessageType::class,
        'attachments' => 'array',
        'metadata' => 'array',
        'is_read' => 'boolean',
        'is_internal' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Bootstrap the model and its traits.
     * 
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        // When deleting a message, also delete its attachments from storage
        static::deleting(function (TicketMessage $message) {
            if ($message->attachments && is_array($message->attachments)) {
                foreach ($message->attachments as $attachment) {
                    if (isset($attachment['path']) && Storage::disk('private')->exists($attachment['path'])) {
                        Storage::disk('private')->delete($attachment['path']);
                    }
                }
            }
        });
    }

    /* ========================================================================
     * RELATIONSHIPS
     * ======================================================================== */

    /**
     * Get the ticket this message belongs to.
     * 
     * @return BelongsTo<Ticket, TicketMessage>
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user who created this message.
     * 
     * @return BelongsTo<User, TicketMessage>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ========================================================================
     * QUERY SCOPES
     * ======================================================================== */

    /**
     * Scope to filter visible messages (exclude internal notes for customers).
     * 
     * @param Builder $query
     * @param bool $isStaff
     * @return Builder
     */
    public function scopeVisible(Builder $query, bool $isStaff = false): Builder
    {
        if ($isStaff) {
            // Staff can see all messages
            return $query;
        }

        // Customers can only see non-internal messages
        return $query->where('is_internal', false);
    }

    /**
     * Scope to filter unread messages.
     * 
     * @param Builder $query
     * @return Builder
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to filter messages by type.
     * 
     * @param Builder $query
     * @param TicketMessageType|string $type
     * @return Builder
     */
    public function scopeOfType(Builder $query, TicketMessageType|string $type): Builder
    {
        $typeValue = $type instanceof TicketMessageType ? $type->value : $type;
        return $query->where('type', $typeValue);
    }

    /**
     * Scope to filter customer-visible messages only.
     * 
     * @param Builder $query
     * @return Builder
     */
    public function scopeCustomerVisible(Builder $query): Builder
    {
        return $query->where('is_internal', false)
                    ->whereIn('type', [
                        TicketMessageType::CUSTOMER_REPLY->value,
                        TicketMessageType::STAFF_REPLY->value,
                        TicketMessageType::STATUS_CHANGE->value,
                    ]);
    }

    /* ========================================================================
     * ACCESSOR & MUTATOR METHODS
     * ======================================================================== */

    /**
     * Get formatted message content with line breaks.
     * 
     * @return string
     */
    public function getFormattedMessageAttribute(): string
    {
        return nl2br(e($this->message));
    }

    /**
     * Check if message has attachments.
     * 
     * @return bool
     */
    public function getHasAttachmentsAttribute(): bool
    {
        return !empty($this->attachments) && is_array($this->attachments);
    }

    /**
     * Get count of attachments.
     * 
     * @return int
     */
    public function getAttachmentsCountAttribute(): int
    {
        return $this->has_attachments ? count($this->attachments) : 0;
    }

    /**
     * Check if message is from a staff member.
     * 
     * @return bool
     */
    public function getIsFromStaffAttribute(): bool
    {
        return in_array($this->type, [
            TicketMessageType::STAFF_REPLY,
            TicketMessageType::INTERNAL_NOTE,
        ]);
    }

    /**
     * Check if message is visible to customers.
     * 
     * @return bool
     */
    public function getIsVisibleToCustomerAttribute(): bool
    {
        return !$this->is_internal && $this->type->isVisibleToCustomer();
    }

    /* ========================================================================
     * BUSINESS LOGIC METHODS
     * ======================================================================== */

    /**
     * Mark message as read.
     * 
     * @return bool
     */
    public function markAsRead(): bool
    {
        if ($this->is_read) {
            return true;
        }

        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark message as unread.
     * 
     * @return bool
     */
    public function markAsUnread(): bool
    {
        return $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Add attachment to the message.
     * 
     * @param string $path File path in storage
     * @param string $originalName Original filename
     * @param string $mimeType File MIME type
     * @param int $size File size in bytes
     * @return bool
     */
    public function addAttachment(string $path, string $originalName, string $mimeType, int $size): bool
    {
        $attachments = $this->attachments ?? [];
        
        $attachments[] = [
            'path' => $path,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'size' => $size,
            'uploaded_at' => now()->toIso8601String(),
        ];

        return $this->update(['attachments' => $attachments]);
    }

    /**
     * Remove an attachment from the message.
     * 
     * @param int $index Attachment array index
     * @return bool
     */
    public function removeAttachment(int $index): bool
    {
        if (!$this->has_attachments || !isset($this->attachments[$index])) {
            return false;
        }

        $attachments = $this->attachments;
        $attachment = $attachments[$index];

        // Delete file from storage
        if (isset($attachment['path']) && Storage::disk('private')->exists($attachment['path'])) {
            Storage::disk('private')->delete($attachment['path']);
        }

        // Remove from array
        array_splice($attachments, $index, 1);

        return $this->update(['attachments' => $attachments]);
    }

    /**
     * Get download URL for an attachment.
     * 
     * @param int $index Attachment array index
     * @return string|null
     */
    public function getAttachmentDownloadUrl(int $index): ?string
    {
        if (!$this->has_attachments || !isset($this->attachments[$index])) {
            return null;
        }

        // Generate a temporary signed URL for secure downloads
        $attachment = $this->attachments[$index];
        
        if (!isset($attachment['path'])) {
            return null;
        }

        return route('customer.tickets.download-attachment', [
            'ticket' => $this->ticket_id,
            'message' => $this->id,
            'index' => $index,
        ]);
    }

    /**
     * Check if user can see this message.
     * 
     * @param User $user
     * @return bool
     */
    public function canBeSeenBy(User $user): bool
    {
        // Staff can see all messages
        if ($user->hasRole('admin') || $user->hasRole('staff')) {
            return true;
        }

        // Customer can only see their ticket's non-internal messages
        if ($this->ticket->user_id === $user->id) {
            return $this->is_visible_to_customer;
        }

        return false;
    }

    /**
     * Get human-readable file size.
     * 
     * @param int $bytes
     * @return string
     */
    public static function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
