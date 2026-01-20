{{--
    Booking Reminder Email - Plain Text Template
    Location: resources/views/emails/booking/reminder-text.blade.php
    
    This template provides a plain text version of the booking reminder email.
    Used by email clients that don't support HTML or for accessibility.
    
    Variables available:
    - $booking: Booking model instance
    - $hallName: Hall name (localized)
    - $hallAddress: Hall address/city
    - $customerName: Customer's name
    - $bookingDate: Formatted booking date
    - $timeSlot: Localized time slot
    - $daysUntil: Number of days until booking
    - $customMessage: Optional custom message from admin
    - $hasBalanceDue: Boolean indicating if payment is pending
    - $balanceDue: Outstanding balance amount
--}}
{{ __('Booking Reminder') }}

Hello {{ $customerName }},

{{ __('This is a friendly reminder about your upcoming booking at') }} {{ $hallName }}.
{{ __('Please find the booking details below.') }}

================================
{{ __('BOOKING DETAILS') }}
================================

{{ __('Hall Name:') }} {{ $hallName }}
{{ __('Location:') }} {{ $hallAddress ?: __('Not specified') }}
{{ __('Booking Date:') }} {{ $bookingDate }}
{{ __('Time:') }} {{ $timeSlot }}
{{ __('Reference Number:') }} #{{ $booking->reference_number ?? 'N/A' }}
{{ __('Status:') }} {{ ucfirst($booking->status ?? 'pending') }}

@if($hasBalanceDue)
{{ __('Balance Due:') }} O.R. {{ number_format($balanceDue, 2) }}
@endif

================================
{{ __('VIEW YOUR BOOKING') }}
================================

{{ route('customer.bookings.show', $booking->id) }}

@if($customMessage)
================================
{{ __('SPECIAL NOTICE') }}
================================

{{ $customMessage }}
@endif

================================
{{ __('IMPORTANT INFORMATION') }}
================================

- {{ __('Please arrive 15 minutes before your scheduled time') }}
- {{ __('Bring your booking reference number with you') }}
- {{ __('If you need to reschedule, please contact us at least 24 hours in advance') }}
- {{ __('Cancellations made less than 24 hours before may incur charges') }}

================================
{{ __('NEED HELP?') }}
================================

{{ __('If you have any questions or need to make changes to your booking, please contact our support team.') }}

Email: support@majalis.om
Phone: +968 1234 5678

================================

{{ __('Thank you for choosing Majalis!') }}
{{ __('We look forward to hosting your event.') }}

---

{{ __('If you did not make this booking or have any concerns, please reply to this email immediately.') }}
