<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Ticket Model
 * 
 * Represents a customer support ticket for booking claims, complaints, and inquiries.
 * Handles ticket lifecycle, SLA tracking, and customer communication management.
 * 
 * @package App\Models
 * @version 1.0.0
 * 
 * @property int $id
 * @property string $ticket_number
 * @property int|null $booking_id
 * @property int $user_id
 * @property int|null $assigned_to
 * @property TicketType $type
 * @property TicketPriority $priority
 * @property TicketStatus $status
 * @property string $subject
 * @property string $description
 * @property string|null $resolution
 * @property string|null $internal_notes
 * @property int|null $rating
 * @property string|null $feedback
 * @property \Carbon\Carbon|null $first_response_at
 * @property \Carbon\Carbon|null $resolved_at
 * @property \Carbon\Carbon|null $closed_at
 * @property \Carbon\Carbon|null $due_date
 * @property array|null $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_number',
        'booking_id',
        'user_id',
        'assigned_to',
        'type',
        'priority',
        'status',
        'subject',
        'description',
        'resolution',
        'internal_notes',
        'rating',
        'feedback',
        'first_response_at',
        'resolved_at',
        'closed_at',
        'due_date',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => TicketType::class,
        'priority' => TicketPriority::class,
        'status' => TicketStatus::class,
        'metadata' => 'array',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'due_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Bootstrap the model and its traits.
     * 
     * Automatically generate ticket number on creation and set due date based on priority.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        // Generate unique ticket number before creation
        static::creating(function (Ticket $ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = $ticket->generateTicketNumber();
            }
            
            // Set due date if not provided
            if (empty($ticket->due_date)) {
                $ticket->due_date = $ticket->calculateDueDate();
            }
        });

        // Track status changes and update timestamps accordingly
        static::updating(function (Ticket $ticket) {
            // If status changed to resolved, set resolved_at
            if ($ticket->isDirty('status') && $ticket->status === TicketStatus::RESOLVED) {
                $ticket->resolved_at = now();
            }

            // If status changed to closed, set closed_at
            if ($ticket->isDirty('status') && $ticket->status === TicketStatus::CLOSED) {
                $ticket->closed_at = now();
            }
        });
    }

    /* ========================================================================
     * RELATIONSHIPS
     * ======================================================================== */

    /**
     * Get the booking associated with this ticket.
     * 
     * @return BelongsTo<Booking, Ticket>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class)->withTrashed();
    }

    /**
     * Get the user (customer) who created this ticket.
     * 
     * @return BelongsTo<User, Ticket>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the staff member assigned to this ticket.
     * 
     * @return BelongsTo<User, Ticket>
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get all messages/responses for this ticket.
     * 
     * @return HasMany<TicketMessage>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class)->orderBy('created_at', 'asc');
    }

    /* ========================================================================
     * QUERY SCOPES
     * ======================================================================== */

    /**
     * Scope to filter open tickets (not closed or cancelled).
     * 
     * @param Builder $query
     * @return Builder
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            TicketStatus::CLOSED->value,
            TicketStatus::CANCELLED->value
        ]);
    }

    /**
     * Scope to filter tickets by status.
     * 
     * @param Builder $query
     * @param TicketStatus|string $status
     * @return Builder
     */
    public function scopeWithStatus(Builder $query, TicketStatus|string $status): Builder
    {
        $statusValue = $status instanceof TicketStatus ? $status->value : $status;
        return $query->where('status', $statusValue);
    }

    /**
     * Scope to filter tickets by priority.
     * 
     * @param Builder $query
     * @param TicketPriority|string $priority
     * @return Builder
     */
    public function scopeWithPriority(Builder $query, TicketPriority|string $priority): Builder
    {
        $priorityValue = $priority instanceof TicketPriority ? $priority->value : $priority;
        return $query->where('priority', $priorityValue);
    }

    /**
     * Scope to filter tickets assigned to a specific user.
     * 
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope to filter overdue tickets.
     * 
     * @param Builder $query
     * @return Builder
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', [
                        TicketStatus::RESOLVED->value,
                        TicketStatus::CLOSED->value,
                        TicketStatus::CANCELLED->value
                    ]);
    }

    /**
     * Scope to filter tickets requiring attention.
     * 
     * @param Builder $query
     * @return Builder
     */
    public function scopeNeedsAttention(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('assigned_to') // Unassigned
              ->orWhere(function (Builder $sq) {
                  $sq->where('priority', TicketPriority::URGENT->value)
                     ->where('status', TicketStatus::OPEN->value);
              })
              ->orWhere('due_date', '<', now()->addHours(24)); // Due soon
        });
    }

    /* ========================================================================
     * ACCESSOR & MUTATOR METHODS
     * ======================================================================== */

    /**
     * Check if ticket is overdue.
     * 
     * @return bool
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date 
            && $this->due_date->isPast() 
            && !in_array($this->status, [TicketStatus::RESOLVED, TicketStatus::CLOSED, TicketStatus::CANCELLED]);
    }

    /**
     * Get time remaining until due date in human-readable format.
     * 
     * @return string|null
     */
    public function getTimeRemainingAttribute(): ?string
    {
        if (!$this->due_date) {
            return null;
        }

        if ($this->is_overdue) {
            return 'Overdue by ' . $this->due_date->diffForHumans(null, true);
        }

        return $this->due_date->diffForHumans();
    }

    /**
     * Calculate response time in hours.
     * 
     * @return float|null
     */
    public function getResponseTimeAttribute(): ?float
    {
        if (!$this->first_response_at) {
            return null;
        }

        return $this->created_at->diffInHours($this->first_response_at, true);
    }

    /**
     * Calculate resolution time in hours.
     * 
     * @return float|null
     */
    public function getResolutionTimeAttribute(): ?float
    {
        if (!$this->resolved_at) {
            return null;
        }

        return $this->created_at->diffInHours($this->resolved_at, true);
    }

    /* ========================================================================
     * BUSINESS LOGIC METHODS
     * ======================================================================== */

    /**
     * Generate a unique ticket number.
     * Format: TCK-YYYYMMDD-XXXXX
     * 
     * @return string
     */
    protected function generateTicketNumber(): string
    {
        $prefix = 'TCK';
        $date = now()->format('Ymd');
        
        // Get the last ticket created today
        $lastTicket = static::whereDate('created_at', today())
            ->latest('id')
            ->first();
        
        $sequence = $lastTicket ? (int) substr($lastTicket->ticket_number, -5) + 1 : 1;
        
        return sprintf('%s-%s-%05d', $prefix, $date, $sequence);
    }

    /**
     * Calculate due date based on priority and SLA rules.
     * 
     * SLA Rules:
     * - Urgent: 4 hours
     * - High: 24 hours
     * - Medium: 48 hours
     * - Low: 72 hours
     * 
     * @return \Carbon\Carbon
     */
    protected function calculateDueDate(): \Carbon\Carbon
    {
        $hours = match($this->priority) {
            TicketPriority::URGENT => 4,
            TicketPriority::HIGH => 24,
            TicketPriority::MEDIUM => 48,
            TicketPriority::LOW => 72,
            default => 48,
        };

        return now()->addHours($hours);
    }

    /**
     * Assign ticket to a staff member.
     * 
     * @param int $userId
     * @return bool
     */
    public function assignTo(int $userId): bool
    {
        return $this->update([
            'assigned_to' => $userId,
            'status' => TicketStatus::IN_PROGRESS,
        ]);
    }

    /**
     * Mark ticket as resolved with optional resolution message.
     * 
     * @param string|null $resolution
     * @return bool
     */
    public function resolve(?string $resolution = null): bool
    {
        return $this->update([
            'status' => TicketStatus::RESOLVED,
            'resolution' => $resolution ?? $this->resolution,
            'resolved_at' => now(),
        ]);
    }

    /**
     * Close the ticket.
     * 
     * @return bool
     */
    public function close(): bool
    {
        return $this->update([
            'status' => TicketStatus::CLOSED,
            'closed_at' => now(),
        ]);
    }

    /**
     * Escalate ticket to higher priority.
     * 
     * @return bool
     */
    public function escalate(): bool
    {
        $newPriority = match($this->priority) {
            TicketPriority::LOW => TicketPriority::MEDIUM,
            TicketPriority::MEDIUM => TicketPriority::HIGH,
            TicketPriority::HIGH => TicketPriority::URGENT,
            default => $this->priority,
        };

        return $this->update([
            'priority' => $newPriority,
            'status' => TicketStatus::ESCALATED,
        ]);
    }

    /**
     * Rate the ticket resolution (customer satisfaction).
     * 
     * @param int $rating Rating from 1-5
     * @param string|null $feedback Optional feedback text
     * @return bool
     */
    public function rate(int $rating, ?string $feedback = null): bool
    {
        if ($rating < 1 || $rating > 5) {
            throw new \InvalidArgumentException('Rating must be between 1 and 5');
        }

        return $this->update([
            'rating' => $rating,
            'feedback' => $feedback,
        ]);
    }

    /**
     * Add a message to the ticket.
     * 
     * @param string $message
     * @param int $userId
     * @param TicketMessageType $type
     * @param array $attachments
     * @param bool $isInternal
     * @return TicketMessage
     */
    public function addMessage(
        string $message, 
        int $userId, 
        TicketMessageType $type = TicketMessageType::CUSTOMER_REPLY,
        array $attachments = [],
        bool $isInternal = false
    ): TicketMessage {
        // Record first response time if this is first staff reply
        if ($type === TicketMessageType::STAFF_REPLY && !$this->first_response_at) {
            $this->update(['first_response_at' => now()]);
        }

        return $this->messages()->create([
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'attachments' => $attachments,
            'is_internal' => $isInternal,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Check if ticket can be closed.
     * 
     * @return bool
     */
    public function canBeClosed(): bool
    {
        return in_array($this->status, [
            TicketStatus::RESOLVED,
            TicketStatus::CANCELLED
        ]);
    }

    /**
     * Check if ticket can be reopened.
     * 
     * @return bool
     */
    public function canBeReopened(): bool
    {
        return $this->status === TicketStatus::CLOSED;
    }
}
