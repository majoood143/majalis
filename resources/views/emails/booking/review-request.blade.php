{{--
    Review Request Email Template
    
    Sent to customers after a completed booking to request a review.
    Uses Laravel's Markdown mail component for consistent styling.
    
    @var \App\Models\Booking $booking
    @var string $hallName
    @var string $reviewUrl
    @var string $customerName
    @var string $bookingDate
--}}
<x-mail::message>
{{-- Greeting --}}
# {{ __('booking.email.review_greeting', ['name' => $customerName]) }}

{{-- Introduction --}}
{{ __('booking.email.review_intro', ['hall' => $hallName, 'date' => $bookingDate]) }}

{{-- Main Message --}}
{{ __('booking.email.review_message') }}

{{-- Call to Action Button --}}
<x-mail::button :url="$reviewUrl" color="primary">
{{ __('booking.email.review_button') }}
</x-mail::button>

{{-- Booking Details Panel --}}
<x-mail::panel>
**{{ __('booking.email.booking_details') }}**

- **{{ __('booking.email.booking_number') }}:** {{ $booking->booking_number }}
- **{{ __('booking.email.hall') }}:** {{ $hallName }}
- **{{ __('booking.email.event_date') }}:** {{ $bookingDate }}
- **{{ __('booking.email.time_slot') }}:** {{ __('booking.time_slots.' . $booking->time_slot) }}
</x-mail::panel>

{{-- Why Review Matters --}}
{{ __('booking.email.review_importance') }}

{{-- Closing --}}
{{ __('booking.email.review_thanks') }}

{{-- Signature --}}
{{ __('booking.email.regards') }}<br>
**{{ config('app.name') }}**

{{-- Footer Note --}}
<x-mail::subcopy>
{{ __('booking.email.review_footer_note') }}
</x-mail::subcopy>
</x-mail::message>
