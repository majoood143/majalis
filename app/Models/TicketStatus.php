<?php

namespace App\Models;

/**
 * Ticket Status Enum
 * 
 * Defines all possible status states for a ticket throughout its lifecycle.
 * Includes workflow validation to ensure proper status transitions.
 * 
 * @package App\Models
 * @version 1.0.0
 */
enum TicketStatus: string
{
    /**
     * New ticket, not yet assigned to staff
     */
    case OPEN = 'open';
    
    /**
     * Awaiting customer response or information
     */
    case PENDING = 'pending';
    
    /**
     * Staff is actively working on the ticket
     */
    case IN_PROGRESS = 'in_progress';
    
    /**
     * Ticket temporarily paused or waiting for external action
     */
    case ON_HOLD = 'on_hold';
    
    /**
     * Issue has been resolved, awaiting customer confirmation
     */
    case RESOLVED = 'resolved';
    
    /**
     * Ticket has been closed and completed
     */
    case CLOSED = 'closed';
    
    /**
     * Ticket cancelled by customer
     */
    case CANCELLED = 'cancelled';
    
    /**
     * Ticket escalated to higher authority or management
     */
    case ESCALATED = 'escalated';

    /**
     * Get human-readable label for the status.
     * 
     * @return string
     */
    public function getLabel(): string
    {
        return match($this) {
            self::OPEN => 'Open',
            self::PENDING => 'Pending Response',
            self::IN_PROGRESS => 'In Progress',
            self::ON_HOLD => 'On Hold',
            self::RESOLVED => 'Resolved',
            self::CLOSED => 'Closed',
            self::CANCELLED => 'Cancelled',
            self::ESCALATED => 'Escalated',
        };
    }

    /**
     * Get color for Filament badge/display.
     * 
     * @return string Filament color name
     */
    public function getColor(): string
    {
        return match($this) {
            self::OPEN => 'info',
            self::PENDING => 'warning',
            self::IN_PROGRESS => 'primary',
            self::ON_HOLD => 'gray',
            self::RESOLVED => 'success',
            self::CLOSED => 'gray',
            self::CANCELLED => 'danger',
            self::ESCALATED => 'danger',
        };
    }

    /**
     * Get icon for Filament display.
     * 
     * @return string Heroicon name
     */
    public function getIcon(): string
    {
        return match($this) {
            self::OPEN => 'heroicon-o-envelope-open',
            self::PENDING => 'heroicon-o-clock',
            self::IN_PROGRESS => 'heroicon-o-arrow-path',
            self::ON_HOLD => 'heroicon-o-pause',
            self::RESOLVED => 'heroicon-o-check-circle',
            self::CLOSED => 'heroicon-o-lock-closed',
            self::CANCELLED => 'heroicon-o-x-circle',
            self::ESCALATED => 'heroicon-o-arrow-trending-up',
        };
    }

    /**
     * Get description for the status.
     * 
     * @return string
     */
    public function getDescription(): string
    {
        return match($this) {
            self::OPEN => 'New ticket awaiting assignment',
            self::PENDING => 'Waiting for customer response or additional information',
            self::IN_PROGRESS => 'Staff member is actively working on resolving this ticket',
            self::ON_HOLD => 'Ticket is temporarily paused pending external action',
            self::RESOLVED => 'Issue has been resolved, awaiting customer confirmation',
            self::CLOSED => 'Ticket has been completed and closed',
            self::CANCELLED => 'Ticket was cancelled by the customer',
            self::ESCALATED => 'Ticket has been escalated to management',
        };
    }

    /**
     * Check if status is considered "active" (ticket is still being worked on).
     * 
     * @return bool
     */
    public function isActive(): bool
    {
        return in_array($this, [
            self::OPEN,
            self::PENDING,
            self::IN_PROGRESS,
            self::ON_HOLD,
            self::ESCALATED,
        ]);
    }

    /**
     * Check if status is considered "completed" (no further action needed).
     * 
     * @return bool
     */
    public function isCompleted(): bool
    {
        return in_array($this, [
            self::RESOLVED,
            self::CLOSED,
            self::CANCELLED,
        ]);
    }

    /**
     * Check if ticket can transition to another status.
     * Implements business rules for valid status transitions.
     * 
     * @param self $newStatus
     * @return bool
     */
    public function canTransitionTo(self $newStatus): bool
    {
        // Same status is always allowed
        if ($this === $newStatus) {
            return true;
        }

        // Define allowed transitions
        $allowedTransitions = [
            self::OPEN->value => [
                self::IN_PROGRESS->value,
                self::PENDING->value,
                self::CANCELLED->value,
                self::ESCALATED->value,
            ],
            self::PENDING->value => [
                self::IN_PROGRESS->value,
                self::ON_HOLD->value,
                self::CANCELLED->value,
                self::ESCALATED->value,
            ],
            self::IN_PROGRESS->value => [
                self::PENDING->value,
                self::ON_HOLD->value,
                self::RESOLVED->value,
                self::CANCELLED->value,
                self::ESCALATED->value,
            ],
            self::ON_HOLD->value => [
                self::IN_PROGRESS->value,
                self::PENDING->value,
                self::CANCELLED->value,
            ],
            self::RESOLVED->value => [
                self::CLOSED->value,
                self::IN_PROGRESS->value, // Reopen if customer not satisfied
            ],
            self::CLOSED->value => [
                self::OPEN->value, // Allow reopening
            ],
            self::CANCELLED->value => [], // Cannot transition from cancelled
            self::ESCALATED->value => [
                self::IN_PROGRESS->value,
                self::RESOLVED->value,
            ],
        ];

        return in_array($newStatus->value, $allowedTransitions[$this->value] ?? []);
    }

    /**
     * Get allowed next statuses from current status.
     * 
     * @return array<self>
     */
    public function getAllowedTransitions(): array
    {
        return collect(self::cases())
            ->filter(fn(self $status) => $this->canTransitionTo($status))
            ->values()
            ->all();
    }

    /**
     * Get all statuses as an array for select inputs.
     * 
     * @return array<string, string>
     */
    public static function toSelectArray(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn(self $status) => [$status->value => $status->getLabel()])
            ->all();
    }

    /**
     * Get active statuses only.
     * 
     * @return array<self>
     */
    public static function getActiveStatuses(): array
    {
        return collect(self::cases())
            ->filter(fn(self $status) => $status->isActive())
            ->values()
            ->all();
    }

    /**
     * Get completed statuses only.
     * 
     * @return array<self>
     */
    public static function getCompletedStatuses(): array
    {
        return collect(self::cases())
            ->filter(fn(self $status) => $status->isCompleted())
            ->values()
            ->all();
    }
}
