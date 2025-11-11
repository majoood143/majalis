<?php

namespace App\Models;

/**
 * Ticket Priority Enum
 * 
 * Defines priority levels for tickets with associated SLA (Service Level Agreement) timeframes.
 * Higher priority tickets have shorter response and resolution times.
 * 
 * @package App\Models
 * @version 1.0.0
 */
enum TicketPriority: string
{
    /**
     * Low priority - non-urgent inquiries
     */
    case LOW = 'low';
    
    /**
     * Medium priority - standard requests
     */
    case MEDIUM = 'medium';
    
    /**
     * High priority - important issues
     */
    case HIGH = 'high';
    
    /**
     * Urgent - critical issues requiring immediate attention
     */
    case URGENT = 'urgent';

    /**
     * Get human-readable label for the priority.
     * 
     * @return string
     */
    public function getLabel(): string
    {
        return match($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            self::URGENT => 'Urgent',
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
            self::LOW => 'gray',
            self::MEDIUM => 'info',
            self::HIGH => 'warning',
            self::URGENT => 'danger',
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
            self::LOW => 'heroicon-o-arrow-down',
            self::MEDIUM => 'heroicon-o-minus',
            self::HIGH => 'heroicon-o-arrow-up',
            self::URGENT => 'heroicon-o-fire',
        };
    }

    /**
     * Get SLA response time in hours.
     * This is the target time for first response to the ticket.
     * 
     * @return int Hours
     */
    public function getResponseSlaHours(): int
    {
        return match($this) {
            self::LOW => 48,    // 2 days
            self::MEDIUM => 24, // 1 day
            self::HIGH => 8,    // 8 hours
            self::URGENT => 2,  // 2 hours
        };
    }

    /**
     * Get SLA resolution time in hours.
     * This is the target time for resolving the ticket.
     * 
     * @return int Hours
     */
    public function getResolutionSlaHours(): int
    {
        return match($this) {
            self::LOW => 72,    // 3 days
            self::MEDIUM => 48, // 2 days
            self::HIGH => 24,   // 1 day
            self::URGENT => 4,  // 4 hours
        };
    }

    /**
     * Get numeric value for sorting (higher number = higher priority).
     * 
     * @return int
     */
    public function getNumericValue(): int
    {
        return match($this) {
            self::LOW => 1,
            self::MEDIUM => 2,
            self::HIGH => 3,
            self::URGENT => 4,
        };
    }

    /**
     * Get description for the priority level.
     * 
     * @return string
     */
    public function getDescription(): string
    {
        return match($this) {
            self::LOW => sprintf('Non-urgent inquiry (Response: %dh, Resolution: %dh)', 
                $this->getResponseSlaHours(), 
                $this->getResolutionSlaHours()
            ),
            self::MEDIUM => sprintf('Standard request (Response: %dh, Resolution: %dh)', 
                $this->getResponseSlaHours(), 
                $this->getResolutionSlaHours()
            ),
            self::HIGH => sprintf('Important issue (Response: %dh, Resolution: %dh)', 
                $this->getResponseSlaHours(), 
                $this->getResolutionSlaHours()
            ),
            self::URGENT => sprintf('Critical issue requiring immediate attention (Response: %dh, Resolution: %dh)', 
                $this->getResponseSlaHours(), 
                $this->getResolutionSlaHours()
            ),
        };
    }

    /**
     * Get all priorities as an array for select inputs.
     * 
     * @return array<string, string>
     */
    public static function toSelectArray(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn(self $priority) => [$priority->value => $priority->getLabel()])
            ->all();
    }

    /**
     * Sort priorities by numeric value.
     * 
     * @param self $a
     * @param self $b
     * @return int
     */
    public static function compare(self $a, self $b): int
    {
        return $b->getNumericValue() <=> $a->getNumericValue(); // Descending order
    }
}
