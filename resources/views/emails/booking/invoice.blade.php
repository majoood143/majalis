{{--
/**
 * Booking Invoice Email Template
 *
 * Sent to customers when admin sends an invoice via email.
 * Uses Laravel's Markdown mail component for consistent styling.
 * Supports bilingual (English/Arabic) with proper RTL.
 *
 * @package Resources\Views\Emails\Booking
 *
 * Variables:
 * @var \App\Models\Booking $booking - The booking instance
 * @var string|null $customMessage - Optional custom message from admin
 */
--}}
<x-mail::message>
{{-- Greeting --}}
# {{ __('booking.email.invoice_greeting', ['name' => $booking->customer_name]) }}

{{-- Custom Message from Admin (if provided) --}}
@if($customMessage)
<x-mail::panel>
{{ $customMessage }}
</x-mail::panel>
@endif

{{-- Introduction --}}
{{ __('booking.email.invoice_intro') }}

{{-- Booking Summary Panel --}}
<x-mail::panel>
**{{ __('booking.email.booking_summary') }}**

@php
    // Get hall name with locale support
    $hallName = is_array($booking->hall?->name)
        ? ($booking->hall->name[app()->getLocale()] ?? $booking->hall->name['en'] ?? 'N/A')
        : ($booking->hall?->name ?? 'N/A');

    // Time slot label
    $timeSlotLabel = __('booking.time_slots.' . $booking->time_slot);
@endphp

| | |
|---|---|
| **{{ __('booking.email.booking_number') }}** | {{ $booking->booking_number }} |
| **{{ __('booking.email.hall') }}** | {{ $hallName }} |
| **{{ __('booking.email.event_date') }}** | {{ $booking->booking_date?->format('l, d M Y') ?? 'N/A' }} |
| **{{ __('booking.email.time_slot') }}** | {{ $timeSlotLabel }} |
| **{{ __('booking.email.guests') }}** | {{ $booking->number_of_guests ?? 'N/A' }} {{ __('booking.email.persons') }} |
@if($booking->event_type)
| **{{ __('booking.email.event_type') }}** | {{ __('booking.event_types.' . $booking->event_type) }} |
@endif
</x-mail::panel>

{{-- Financial Summary Panel --}}
<x-mail::panel>
**{{ __('booking.email.financial_summary') }}**

| | |
|---|---|
| **{{ __('booking.email.hall_price') }}** | {{ number_format((float)$booking->hall_price, 3) }} {{ __('currency.omr') }} |
@if($booking->services_price > 0)
| **{{ __('booking.email.services_price') }}** | {{ number_format((float)$booking->services_price, 3) }} {{ __('currency.omr') }} |
@endif
| **{{ __('booking.email.subtotal') }}** | {{ number_format((float)$booking->subtotal, 3) }} {{ __('currency.omr') }} |
@if($booking->promoCode && (float)($booking->discount_amount ?? 0) > 0)
| **{{ __('booking.email.promo_code') }} ({{ $booking->promoCode->code }})** | - {{ number_format((float)$booking->discount_amount, 3) }} {{ __('currency.omr') }} |
@endif
@if($booking->commission_amount > 0)
| **{{ __('booking.email.platform_fee') }}** | {{ number_format((float)$booking->commission_amount, 3) }} {{ __('currency.omr') }} |
@endif
| **{{ __('booking.email.total_amount') }}** | **{{ number_format((float)$booking->total_amount, 3) }} {{ __('currency.omr') }}** |
</x-mail::panel>

{{-- Payment Information (if advance payment) --}}
@if($booking->payment_type === 'advance')
<x-mail::panel>
**{{ __('booking.email.payment_info') }}**

| | |
|---|---|
| **{{ __('booking.email.payment_type') }}** | {{ __('advance_payment.payment_type_advance') }} |
| **{{ __('booking.email.advance_paid') }}** | {{ number_format((float)$booking->advance_amount, 3) }} {{ __('currency.omr') }} |
| **{{ __('booking.email.balance_due') }}** | {{ number_format((float)$booking->balance_due, 3) }} {{ __('currency.omr') }} |
@if($booking->balance_paid_at)
| **{{ __('booking.email.balance_paid') }}** | ✅ {{ $booking->balance_paid_at->format('d M Y H:i') }} |
@endif
</x-mail::panel>
@endif

{{-- Status Badges --}}
<x-mail::panel>
| | |
|---|---|
| **{{ __('booking.email.booking_status') }}** | {{ __('booking.statuses.' . $booking->status) }} |
| **{{ __('booking.email.payment_status') }}** | {{ __('booking.payment_statuses.' . $booking->payment_status) }} |
</x-mail::panel>

{{-- Extra Services (if any) --}}
@if($booking->extraServices && $booking->extraServices->count() > 0)
<x-mail::panel>
**{{ __('booking.email.extra_services') }}**

@foreach($booking->extraServices as $service)
@php
    $serviceName = is_array($service->name)
        ? ($service->name[app()->getLocale()] ?? $service->name['en'] ?? 'Service')
        : $service->name;
@endphp
- {{ $serviceName }}: {{ $service->pivot->quantity }} × {{ number_format((float)$service->pivot->unit_price, 3) }} {{ __('currency.omr') }} = **{{ number_format((float)$service->pivot->total_price, 3) }} {{ __('currency.omr') }}**
@endforeach
</x-mail::panel>
@endif

{{-- Note about PDF attachment --}}
📄 {{ __('booking.email.invoice_pdf_attached') }}

{{-- Contact Information --}}
{{ __('booking.email.invoice_questions') }}

{{-- Closing --}}
{{ __('booking.email.regards') }},<br>
**{{ config('app.name', 'Majalis') }}**

{{-- Footer Subcopy --}}
<x-mail::subcopy>
{{ __('booking.email.invoice_footer') }}
</x-mail::subcopy>
</x-mail::message>
