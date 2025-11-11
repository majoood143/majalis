<?php

namespace App\Models;

/**
 * Ticket Message Type Enum
 * 
 * Defines different types of messages that can be added to a ticket.
 * Used to distinguish between customer replies, staff responses, and system messages.
 * 
 * @package App\Models
 * @version 1.0.0
 */
enum TicketMessageType: string
{
    /**
     * Response from the customer
     */
    case CUSTOMER_REPLY = 'customer_reply';
    
    /**
     * Response from support staff
     */
    case STAFF_REPLY = 'staff_reply';
    
    /**
     * Internal note (not visible to customer)
     */
    case INTERNAL_NOTE = 'internal_note';
    
    /**
     * Automated status change notification
     */
    case STATUS_CHANGE = 'status_change';
    
    /**
     * System-generated message
     */
    case SYSTEM_MESSAGE = 'system_message';

    /**
     * Get human-readable label for the message type.
     * 
     * @return string
     */
    public function getLabel(): string
    {
        return match($this) {
            self::CUSTOMER_REPLY => 'Customer Reply',
            self::STAFF_REPLY => 'Staff Response',
            self::INTERNAL_NOTE => 'Internal Note',
            self::STATUS_CHANGE => 'Status Change',
            self::SYSTEM_MESSAGE => 'System Message',
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
            self::CUSTOMER_REPLY => 'info',
            self::STAFF_REPLY => 'success',
            self::INTERNAL_NOTE => 'warning',
            self::STATUS_CHANGE => 'gray',
            self::SYSTEM_MESSAGE => 'gray',
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
            self::CUSTOMER_REPLY => 'heroicon-o-user',
            self::STAFF_REPLY => 'heroicon-o-user-circle',
            self::INTERNAL_NOTE => 'heroicon-o-document-text',
            self::STATUS_CHANGE => 'heroicon-o-arrow-path',
            self::SYSTEM_MESSAGE => 'heroicon-o-computer-desktop',
        };
    }

    /**
     * Check if this message type is visible to customers.
     * 
     * @return bool
     */
    public function isVisibleToCustomer(): bool
    {
        return in_array($this, [
            self::CUSTOMER_REPLY,
            self::STAFF_REPLY,
            self::STATUS_CHANGE,
        ]);
    }

    /**
     * Check if this message type requires staff permissions.
     * 
     * @return bool
     */
    public function requiresStaffPermission(): bool
    {
        return in_array($this, [
            self::STAFF_REPLY,
            self::INTERNAL_NOTE,
        ]);
    }

    /**
     * Get all message types as an array for select inputs.
     * 
     * @return array<string, string>
     */
    public static function toSelectArray(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn(self $type) => [$type->value => $type->getLabel()])
            ->all();
    }
}
