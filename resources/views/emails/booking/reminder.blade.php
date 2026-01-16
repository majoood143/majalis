{{--
    Booking Reminder Email Template
    
    Sent to customers before their booking date as a reminder.
    Includes booking details and any pending balance information.
    
    @var \App\Models\Booking $booking
    @var string $hallName
    @var string $hallAddress
    @var string $customerName
    @var string $bookingDate
    @var string $timeSlot
    @var int $daysUntil
    @var string|null $customMessage
    @var bool $hasBalanceDue
    @var float|null $balanceDue
--}}
<x-mail::message>
{{-- Greeting --}}
# {{ __('booking.email.reminder_greeting', ['name' => $customerName]) }}

{{-- Countdown --}}
@if($daysUntil === 0)
🎉 **{{ __('booking.email.reminder_today') }}**
@elseif($daysUntil === 1)
⏰ **{{ __('booking.email.reminder_tomorrow') }}**
@else
📅 **{{ __('booking.email.reminder_days', ['days' => $daysUntil]) }}**
@endif

{{-- Custom Message from Admin --}}
@if($customMessage)
<x-mail::panel>
{{ $customMessage }}
</x-mail::panel>
@endif

{{-- Booking Details --}}
<x-mail::panel>
**{{ __('booking.email.your_booking_details') }}**

| | |
|---|---|
| **{{ __('booking.email.booking_number') }}** | {{ $booking->booking_number }} |
| **{{ __('booking.email.hall') }}** | {{ $hallName }} |
| **{{ __('booking.email.location') }}** | {{ $hallAddress }} |
| **{{ __('booking.email.event_date') }}** | {{ $bookingDate }} |
| **{{ __('booking.email.time_slot') }}** | {{ $timeSlot }} |
| **{{ __('booking.email.guests') }}** | {{ $booking->number_of_guests }} {{ __('booking.email.persons') }} |
</x-mail::panel>

{{-- Balance Due Warning --}}
@if($hasBalanceDue)
<x-mail::panel>
⚠️ **{{ __('booking.email.balance_reminder_title') }}**

{{ __('booking.email.balance_reminder_message', ['amount' => number_format((float)$balanceDue, 3)]) }}

<x-mail::button :url="route('bookings.pay-balance', $booking)" color="primary">
{{ __('booking.email.pay_balance_button') }}
</x-mail::button>
</x-mail::panel>
@endif

{{-- Preparation Tips --}}
## {{ __('booking.email.preparation_tips_title') }}

- ✅ {{ __('booking.email.tip_arrive_early') }}
- ✅ {{ __('booking.email.tip_contact_hall') }}
- ✅ {{ __('booking.email.tip_bring_confirmation') }}

{{-- Contact Information --}}
{{ __('booking.email.questions_contact') }}

{{-- Closing --}}
{{ __('booking.email.we_look_forward') }}

{{ __('booking.email.regards') }}<br>
**{{ config('app.name') }}**
</x-mail::message>
