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
# {{ __('booking.email.invoice_greeting', ['name' => $booking->customer_name], 'Dear :name') }}

{{-- Custom Message from Admin (if provided) --}}
@if($customMessage)
<x-mail::panel>
{{ $customMessage }}
</x-mail::panel>
@endif

{{-- Introduction --}}
{{ __('booking.email.invoice_intro', [], 'Please find attached your invoice for your recent booking with us.') }}

{{-- Booking Summary Panel --}}
<x-mail::panel>
**{{ __('booking.email.booking_summary', [], 'Booking Summary') }}**

@php
    // Get hall name with locale support
    $hallName = is_array($booking->hall?->name) 
        ? ($booking->hall->name[app()->getLocale()] ?? $booking->hall->name['en'] ?? 'N/A')
        : ($booking->hall?->name ?? 'N/A');
    
    // Time slot label
    $timeSlotLabel = __('booking.time_slots.' . $booking->time_slot, [], ucfirst(str_replace('_', ' ', $booking->time_slot ?? 'N/A')));
@endphp

| | |
|---|---|
| **{{ __('booking.email.booking_number', [], 'Booking Number') }}** | {{ $booking->booking_number }} |
| **{{ __('booking.email.hall', [], 'Hall') }}** | {{ $hallName }} |
| **{{ __('booking.email.event_date', [], 'Event Date') }}** | {{ $booking->booking_date?->format('l, d M Y') ?? 'N/A' }} |
| **{{ __('booking.email.time_slot', [], 'Time Slot') }}** | {{ $timeSlotLabel }} |
| **{{ __('booking.email.guests', [], 'Number of Guests') }}** | {{ $booking->number_of_guests ?? 'N/A' }} {{ __('booking.email.persons', [], 'persons') }} |
@if($booking->event_type)
| **{{ __('booking.email.event_type', [], 'Event Type') }}** | {{ __('booking.event_types.' . $booking->event_type, [], ucfirst($booking->event_type)) }} |
@endif
</x-mail::panel>

{{-- Financial Summary Panel --}}
<x-mail::panel>
**{{ __('booking.email.financial_summary', [], 'Financial Summary') }}**

| | |
|---|---|
| **{{ __('booking.email.hall_price', [], 'Hall Price') }}** | {{ number_format((float)$booking->hall_price, 3) }} {{ __('currency.omr', [], 'OMR') }} |
@if($booking->services_price > 0)
| **{{ __('booking.email.services_price', [], 'Extra Services') }}** | {{ number_format((float)$booking->services_price, 3) }} {{ __('currency.omr', [], 'OMR') }} |
@endif
| **{{ __('booking.email.subtotal', [], 'Subtotal') }}** | {{ number_format((float)$booking->subtotal, 3) }} {{ __('currency.omr', [], 'OMR') }} |
@if($booking->commission_amount > 0)
| **{{ __('booking.email.platform_fee', [], 'Platform Fee') }}** | {{ number_format((float)$booking->commission_amount, 3) }} {{ __('currency.omr', [], 'OMR') }} |
@endif
| **{{ __('booking.email.total_amount', [], 'Total Amount') }}** | **{{ number_format((float)$booking->total_amount, 3) }} {{ __('currency.omr', [], 'OMR') }}** |
</x-mail::panel>

{{-- Payment Information (if advance payment) --}}
@if($booking->payment_type === 'advance')
<x-mail::panel>
**{{ __('booking.email.payment_info', [], 'Payment Information') }}**

| | |
|---|---|
| **{{ __('booking.email.payment_type', [], 'Payment Type') }}** | {{ __('advance_payment.payment_type_advance', [], 'Advance Payment') }} |
| **{{ __('booking.email.advance_paid', [], 'Advance Paid') }}** | {{ number_format((float)$booking->advance_amount, 3) }} {{ __('currency.omr', [], 'OMR') }} |
| **{{ __('booking.email.balance_due', [], 'Balance Due') }}** | {{ number_format((float)$booking->balance_due, 3) }} {{ __('currency.omr', [], 'OMR') }} |
@if($booking->balance_paid_at)
| **{{ __('booking.email.balance_paid', [], 'Balance Paid') }}** | ✅ {{ $booking->balance_paid_at->format('d M Y H:i') }} |
@endif
</x-mail::panel>
@endif

{{-- Status Badges --}}
<x-mail::panel>
| | |
|---|---|
| **{{ __('booking.email.booking_status', [], 'Booking Status') }}** | {{ __('booking.statuses.' . $booking->status, [], ucfirst($booking->status)) }} |
| **{{ __('booking.email.payment_status', [], 'Payment Status') }}** | {{ __('booking.payment_statuses.' . $booking->payment_status, [], ucfirst(str_replace('_', ' ', $booking->payment_status))) }} |
</x-mail::panel>

{{-- Extra Services (if any) --}}
@if($booking->extraServices && $booking->extraServices->count() > 0)
<x-mail::panel>
**{{ __('booking.email.extra_services', [], 'Extra Services') }}**

@foreach($booking->extraServices as $service)
@php
    $serviceName = is_array($service->name) 
        ? ($service->name[app()->getLocale()] ?? $service->name['en'] ?? 'Service')
        : $service->name;
@endphp
- {{ $serviceName }}: {{ $service->pivot->quantity }} × {{ number_format((float)$service->pivot->unit_price, 3) }} {{ __('currency.omr', [], 'OMR') }} = **{{ number_format((float)$service->pivot->total_price, 3) }} {{ __('currency.omr', [], 'OMR') }}**
@endforeach
</x-mail::panel>
@endif

{{-- Note about PDF attachment --}}
📄 {{ __('booking.email.invoice_pdf_attached', [], 'A PDF copy of your invoice is attached to this email for your records.') }}

{{-- Contact Information --}}
{{ __('booking.email.invoice_questions', [], 'If you have any questions about this invoice, please don\'t hesitate to contact us.') }}

{{-- Closing --}}
{{ __('booking.email.regards', [], 'Best Regards') }},<br>
**{{ config('app.name', 'Majalis') }}**

{{-- Footer Subcopy --}}
<x-mail::subcopy>
{{ __('booking.email.invoice_footer', [], 'This is an automated email. Please do not reply directly to this message.') }}
</x-mail::subcopy>
</x-mail::message>
