<?php

namespace App\Models;

/**
 * Ticket Type Enum
 * 
 * Defines all available ticket types with display labels and color schemes for UI.
 * Used for categorizing different types of customer support tickets.
 * 
 * @package App\Models
 * @version 1.0.0
 */
enum TicketType: string
{
    /**
     * Customer claim for compensation or issue resolution
     */
    case CLAIM = 'claim';
    
    /**
     * General complaint about service or experience
     */
    case COMPLAINT = 'complaint';
    
    /**
     * Question or information request
     */
    case INQUIRY = 'inquiry';
    
    /**
     * Request for refund
     */
    case REFUND = 'refund';
    
    /**
     * Booking cancellation request
     */
    case CANCELLATION = 'cancellation';
    
    /**
     * Technical issue or bug report
     */
    case TECHNICAL = 'technical';
    
    /**
     * General feedback
     */
    case FEEDBACK = 'feedback';
    
    /**
     * Other types not covered above
     */
    case OTHER = 'other';

    /**
     * Get human-readable label for the ticket type.
     * 
     * @return string
     */
    public function getLabel(): string
    {
        return match($this) {
            self::CLAIM => 'Claim',
            self::COMPLAINT => 'Complaint',
            self::INQUIRY => 'Inquiry',
            self::REFUND => 'Refund Request',
            self::CANCELLATION => 'Cancellation Request',
            self::TECHNICAL => 'Technical Issue',
            self::FEEDBACK => 'Feedback',
            self::OTHER => 'Other',
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
            self::CLAIM => 'warning',
            self::COMPLAINT => 'danger',
            self::INQUIRY => 'info',
            self::REFUND => 'warning',
            self::CANCELLATION => 'gray',
            self::TECHNICAL => 'danger',
            self::FEEDBACK => 'success',
            self::OTHER => 'gray',
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
            self::CLAIM => 'heroicon-o-shield-exclamation',
            self::COMPLAINT => 'heroicon-o-exclamation-triangle',
            self::INQUIRY => 'heroicon-o-question-mark-circle',
            self::REFUND => 'heroicon-o-arrow-path',
            self::CANCELLATION => 'heroicon-o-x-circle',
            self::TECHNICAL => 'heroicon-o-wrench-screwdriver',
            self::FEEDBACK => 'heroicon-o-chat-bubble-left-right',
            self::OTHER => 'heroicon-o-ellipsis-horizontal-circle',
        };
    }

    /**
     * Get description for the ticket type.
     * 
     * @return string
     */
    public function getDescription(): string
    {
        return match($this) {
            self::CLAIM => 'Submit a claim for compensation or issue resolution',
            self::COMPLAINT => 'Report a complaint about service or experience',
            self::INQUIRY => 'Ask a question or request information',
            self::REFUND => 'Request a refund for a booking',
            self::CANCELLATION => 'Request to cancel a booking',
            self::TECHNICAL => 'Report a technical issue or bug',
            self::FEEDBACK => 'Provide feedback about our service',
            self::OTHER => 'Other inquiries not covered above',
        };
    }

    /**
     * Get all ticket types as an array for select inputs.
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
