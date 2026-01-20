{{-- 
    Booking Reminder Email - HTML Template (English)
    Location: resources/views/emails/booking/reminder.blade.php
    
    This template is used to render the HTML version of the booking reminder email.
    Supports Filament and Laravel mail component styling.
    
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

<x-mail::message>
    {{-- Header Section --}}
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #1f2937; margin: 0; font-size: 28px; font-weight: bold;">
            {{ __('Booking Reminder') }}
        </h1>
        <p style="color: #6b7280; margin: 10px 0 0 0; font-size: 14px;">
            @if($daysUntil > 0)
                {{ __('Your booking is coming up in :days days', ['days' => $daysUntil]) }}
            @else
                {{ __('Your booking is today!') }}
            @endif
        </p>
    </div>

    {{-- Greeting --}}
    <p style="color: #374151; margin: 20px 0; font-size: 16px;">
        {{ __('Hello') }} {{ $customerName }},
    </p>

    {{-- Main Message --}}
    <p style="color: #374151; margin: 20px 0; line-height: 1.6; font-size: 16px;">
        {{ __('This is a friendly reminder about your upcoming booking at') }} <strong>{{ $hallName }}</strong>. 
        {{ __('Please find the booking details below.') }}
    </p>

    {{-- Booking Details Section --}}
    <x-mail::panel>
        <div style="padding: 20px; background-color: #f9fafb; border-radius: 8px; border-left: 4px solid #3b82f6;">
            <h2 style="color: #1f2937; margin: 0 0 20px 0; font-size: 18px; font-weight: bold;">
                {{ __('Booking Details') }}
            </h2>

            {{-- Booking Information Grid --}}
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px 0; color: #6b7280; font-weight: 600; width: 40%;">
                        {{ __('Hall Name:') }}
                    </td>
                    <td style="padding: 12px 0; color: #1f2937; font-weight: 500;">
                        {{ $hallName }}
                    </td>
                </tr>

                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px 0; color: #6b7280; font-weight: 600;">
                        {{ __('Location:') }}
                    </td>
                    <td style="padding: 12px 0; color: #1f2937; font-weight: 500;">
                        {{ $hallAddress ?: __('Not specified') }}
                    </td>
                </tr>

                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px 0; color: #6b7280; font-weight: 600;">
                        {{ __('Booking Date:') }}
                    </td>
                    <td style="padding: 12px 0; color: #1f2937; font-weight: 500;">
                        {{ $bookingDate }}
                    </td>
                </tr>

                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px 0; color: #6b7280; font-weight: 600;">
                        {{ __('Time:') }}
                    </td>
                    <td style="padding: 12px 0; color: #1f2937; font-weight: 500;">
                        {{ $timeSlot }}
                    </td>
                </tr>

                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px 0; color: #6b7280; font-weight: 600;">
                        {{ __('Reference Number:') }}
                    </td>
                    <td style="padding: 12px 0; color: #1f2937; font-weight: 500; font-family: monospace;">
                        #{{ $booking->reference_number ?? 'N/A' }}
                    </td>
                </tr>

                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px 0; color: #6b7280; font-weight: 600;">
                        {{ __('Status:') }}
                    </td>
                    <td style="padding: 12px 0; color: #1f2937; font-weight: 500;">
                        <span style="display: inline-block; padding: 4px 12px; background-color: #dbeafe; color: #0c4a6e; border-radius: 20px; font-size: 12px; font-weight: 600;">
                            {{ ucfirst($booking->status ?? 'pending') }}
                        </span>
                    </td>
                </tr>

                {{-- Payment Status --}}
                @if($hasBalanceDue)
                    <tr>
                        <td style="padding: 12px 0; color: #dc2626; font-weight: 600;">
                            {{ __('Balance Due:') }}
                        </td>
                        <td style="padding: 12px 0; color: #dc2626; font-weight: bold; font-size: 16px;">
                            {{ __('O.R. :amount', ['amount' => number_format($balanceDue, 2)]) }}
                        </td>
                    </tr>
                @endif
            </table>
        </div>
    </x-mail::panel>

    {{-- Call to Action Button --}}
    <div style="text-align: center; margin: 30px 0;">
        <x-mail::button :url="route('customer.bookings.show', $booking->id)" color="primary">
            {{ __('View Full Booking Details') }}
        </x-mail::button>
    </div>

    {{-- Custom Message Section --}}
    @if($customMessage)
        <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; margin: 20px 0; border-radius: 4px;">
            <p style="color: #92400e; margin: 0; font-size: 14px;">
                <strong>{{ __('Special Notice:') }}</strong><br>
                {{ $customMessage }}
            </p>
        </div>
    @endif

    {{-- Important Information --}}
    <div style="background-color: #f0fdf4; border-left: 4px solid #16a34a; padding: 16px; margin: 20px 0; border-radius: 4px;">
        <h3 style="color: #15803d; margin: 0 0 12px 0; font-size: 14px; font-weight: bold;">
            {{ __('Important Information') }}
        </h3>
        <ul style="color: #166534; margin: 0; padding-left: 20px; font-size: 13px; line-height: 1.8;">
            <li>{{ __('Please arrive 15 minutes before your scheduled time') }}</li>
            <li>{{ __('Bring your booking reference number with you') }}</li>
            <li>{{ __('If you need to reschedule, please contact us at least 24 hours in advance') }}</li>
            <li>{{ __('Cancellations made less than 24 hours before may incur charges') }}</li>
        </ul>
    </div>

    {{-- Contact Information --}}
    <div style="background-color: #eff6ff; border-left: 4px solid #0284c7; padding: 16px; margin: 20px 0; border-radius: 4px;">
        <h3 style="color: #0c4a6e; margin: 0 0 12px 0; font-size: 14px; font-weight: bold;">
            {{ __('Need Help?') }}
        </h3>
        <p style="color: #164e63; margin: 0; font-size: 13px; line-height: 1.6;">
            {{ __('If you have any questions or need to make changes to your booking, please contact our support team.') }}<br>
            <strong>{{ __('Email:') }}</strong> support@majalis.om<br>
            <strong>{{ __('Phone:') }}</strong> +968 1234 5678
        </p>
    </div>

    {{-- Footer Message --}}
    <p style="color: #6b7280; margin: 30px 0 0 0; font-size: 14px; line-height: 1.6;">
        {{ __('Thank you for choosing Majalis!') }}<br>
        {{ __('We look forward to hosting your event.') }}
    </p>

    {{-- Support Footer --}}
    <x-mail::subcopy>
        {{ __('If you did not make this booking or have any concerns, please reply to this email immediately.') }}
    </x-mail::subcopy>
</x-mail::message>
