{{--
    Review Request Email Template

    Sent to customers +2 hours after their event completes.
    Uses Laravel's Markdown mail component for consistent styling.

    Variables:
    @var \App\Models\Booking $booking
    @var string $hallName
    @var string $reviewUrl
    @var string $customerName
    @var string $bookingDate
    @var string $timeSlot
    @var int    $daysRemaining   Number of days left in the primary review window
--}}
<x-mail::message>
# {{ __('booking.email.review_greeting', ['name' => $customerName]) }}

{{ __('booking.email.review_intro', ['hall' => $hallName, 'date' => $bookingDate]) }}

{{ __('booking.email.review_message') }}

<x-mail::button :url="$reviewUrl" color="primary">
{{ __('booking.email.review_button') }}
</x-mail::button>

<x-mail::panel>
**{{ __('booking.email.booking_details') }}**

- **{{ __('booking.email.booking_number') }}:** {{ $booking->booking_number }}
- **{{ __('booking.email.hall') }}:** {{ $hallName }}
- **{{ __('booking.email.event_date') }}:** {{ $bookingDate }}
- **{{ __('booking.email.time_slot') }}:** {{ $timeSlot }}
- **{{ __('booking.email.event_type') }}:** {{ $booking->event_type ? ucfirst($booking->event_type) : __('booking.email.not_specified') }}
</x-mail::panel>

> ⏰ {{ __('booking.email.review_window_notice', ['days' => $daysRemaining]) }}

{{ __('booking.email.review_importance') }}

{{ __('booking.email.review_thanks') }}

{{ __('booking.email.regards') }}<br>
**{{ config('app.name') }}**

<x-mail::subcopy>
{{ __('booking.email.review_footer_note') }}
</x-mail::subcopy>
</x-mail::message>
