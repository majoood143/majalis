@extends('emails.layouts.base')

@php
    $locale   = app()->getLocale();
    $isRtl    = $locale === 'ar';
    $dir      = $isRtl ? 'rtl' : 'ltr';
    $align    = $isRtl ? 'right' : 'left';

    $hallName = is_array($booking->hall->name)
        ? ($booking->hall->name[$locale] ?? $booking->hall->name['en'] ?? 'N/A')
        : $booking->hall->name;

    $slotLabels = [
        'morning'   => __('slots.morning'),
        'afternoon' => __('slots.afternoon'),
        'evening'   => __('slots.evening'),
        'full_day'  => __('slots.full_day'),
    ];
    $timeSlot = $slotLabels[$booking->time_slot] ?? ucfirst(str_replace('_', ' ', $booking->time_slot));

    $daysUntil = now()->startOfDay()->diffInDays($booking->booking_date->startOfDay(), false);
@endphp

@section('header-subtitle')
    {{ __('emails.booking.confirmed.subtitle') }}
@endsection

@section('content')

{{-- ✅ Success Icon --}}
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom:24px;">
    <tr>
        <td align="center">
            <div style="width:80px;height:80px;background:#10b981;border-radius:50%;display:inline-block;line-height:80px;text-align:center;">
                <span style="font-size:40px;color:#ffffff;line-height:80px;">&#10003;</span>
            </div>
        </td>
    </tr>
</table>

{{-- ✅ Heading --}}
<h2 style="text-align:center;color:#059669;font-size:24px;font-weight:700;margin:0 0 12px 0;">
    {{ __('emails.booking.confirmed.title') }}
</h2>

<p style="text-align:center;color:#374151;font-size:16px;margin:0 0 8px 0;">
    {{ __('emails.booking.greeting', ['name' => $booking->customer_name]) }}
</p>

<p style="text-align:center;color:#6b7280;font-size:15px;margin:0 0 24px 0;">
    {{ __('emails.booking.confirmed.intro') }}
</p>

{{-- ✅ Countdown badge --}}
@if($daysUntil > 0)
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom:24px;">
    <tr>
        <td align="center">
            <div style="display:inline-block;background:#4f46e5;color:#ffffff;padding:20px 40px;border-radius:12px;text-align:center;">
                <div style="font-size:48px;font-weight:700;line-height:1;color:#ffffff;">{{ $daysUntil }}</div>
                <div style="font-size:13px;text-transform:uppercase;letter-spacing:1px;color:#ffffff;margin-top:4px;opacity:.9;">
                    {{ trans_choice('emails.booking.days_until', $daysUntil) }}
                </div>
            </div>
        </td>
    </tr>
</table>
@endif

{{-- ✅ Status badge --}}
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom:24px;">
    <tr>
        <td align="center">
            <span style="display:inline-block;background:#d1fae5;color:#065f46;padding:6px 20px;border-radius:20px;font-size:13px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;">
                {{ __('emails.status.confirmed') }}
            </span>
        </td>
    </tr>
</table>

{{-- ✅ Booking Details Box --}}
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
       style="background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:20px;">
    <tr>
        <td style="padding:20px 24px 8px 24px;">
            <p style="font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin:0 0 12px 0;padding-bottom:12px;border-bottom:2px solid #e5e7eb;">
                {{ __('emails.booking.your_booking') }}
            </p>
        </td>
    </tr>

    {{-- Booking Number --}}
    <tr>
        <td style="padding:8px 24px;border-bottom:1px dashed #e5e7eb;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td style="color:#6b7280;font-size:14px;text-align:{{ $align }};">{{ __('emails.booking.booking_number') }}</td>
                    <td style="color:#4f46e5;font-size:16px;font-weight:700;text-align:{{ $isRtl ? 'left' : 'right' }};">{{ $booking->booking_number }}</td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Hall --}}
    <tr>
        <td style="padding:8px 24px;border-bottom:1px dashed #e5e7eb;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td style="color:#6b7280;font-size:14px;text-align:{{ $align }};">{{ __('emails.booking.hall') }}</td>
                    <td style="color:#1f2937;font-size:14px;font-weight:600;text-align:{{ $isRtl ? 'left' : 'right' }};">{{ $hallName }}</td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Date --}}
    <tr>
        <td style="padding:8px 24px;border-bottom:1px dashed #e5e7eb;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td style="color:#6b7280;font-size:14px;text-align:{{ $align }};">{{ __('emails.booking.date') }}</td>
                    <td style="color:#1f2937;font-size:14px;font-weight:600;text-align:{{ $isRtl ? 'left' : 'right' }};">{{ $booking->booking_date->format('l, d F Y') }}</td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Time Slot --}}
    <tr>
        <td style="padding:8px 24px;{{ $booking->number_of_guests ? 'border-bottom:1px dashed #e5e7eb;' : '' }}">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td style="color:#6b7280;font-size:14px;text-align:{{ $align }};">{{ __('emails.booking.time_slot') }}</td>
                    <td style="color:#1f2937;font-size:14px;font-weight:600;text-align:{{ $isRtl ? 'left' : 'right' }};">{{ $timeSlot }}</td>
                </tr>
            </table>
        </td>
    </tr>

    @if($booking->number_of_guests)
    {{-- Guests --}}
    <tr>
        <td style="padding:8px 24px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td style="color:#6b7280;font-size:14px;text-align:{{ $align }};">{{ __('emails.booking.guests') }}</td>
                    <td style="color:#1f2937;font-size:14px;font-weight:600;text-align:{{ $isRtl ? 'left' : 'right' }};">{{ $booking->number_of_guests }} {{ __('emails.booking.persons') }}</td>
                </tr>
            </table>
        </td>
    </tr>
    @endif

    <tr><td style="padding:8px;"></td></tr>
</table>

{{-- ✅ Location --}}
@if($booking->hall->address || $booking->hall->city)
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
       style="background:#eff6ff;border-radius:12px;border-{{ $isRtl ? 'right' : 'left' }}:4px solid #3b82f6;margin-bottom:20px;">
    <tr>
        <td style="padding:20px 24px;">
            <p style="margin:0 0 8px 0;font-size:15px;font-weight:700;color:#1e40af;">
                &#128205; {{ __('emails.booking.location') }}
            </p>
            <p style="margin:0;font-size:14px;color:#1d4ed8;line-height:1.7;">
                <strong>{{ $hallName }}</strong><br>
                @if($booking->hall->address){{ $booking->hall->address }}<br>@endif
                @if($booking->hall->city)
                    {{ is_array($booking->hall->city->name) ? ($booking->hall->city->name[$locale] ?? $booking->hall->city->name['en']) : $booking->hall->city->name }}
                @endif
            </p>
            @if($booking->hall->google_maps_url)
            <p style="margin:12px 0 0 0;">
                <a href="{{ $booking->hall->google_maps_url }}"
                   style="color:#4f46e5;font-weight:600;font-size:14px;text-decoration:none;">
                    {{ __('emails.booking.view_map') }} &rarr;
                </a>
            </p>
            @endif
        </td>
    </tr>
</table>
@endif

{{-- ✅ Payment Status --}}
@if($booking->payment_status === 'paid')
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
       style="background:#ecfdf5;border-radius:12px;border-{{ $isRtl ? 'right' : 'left' }}:4px solid #10b981;margin-bottom:20px;">
    <tr>
        <td style="padding:20px 24px;">
            <p style="margin:0 0 6px 0;font-size:15px;font-weight:700;color:#065f46;">
                &#10003; {{ __('emails.booking.payment_complete') }}
            </p>
            <p style="margin:0;font-size:14px;color:#047857;">
                {{ __('emails.booking.amount_paid', ['amount' => number_format($booking->total_amount, 3)]) }}
            </p>
        </td>
    </tr>
</table>
@elseif($booking->payment_status === 'partial')
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
       style="background:#fffbeb;border-radius:12px;border-{{ $isRtl ? 'right' : 'left' }}:4px solid #f59e0b;margin-bottom:20px;">
    <tr>
        <td style="padding:20px 24px;">
            <p style="margin:0 0 6px 0;font-size:15px;font-weight:700;color:#92400e;">
                &#9888; Advance Payment Received
            </p>
            <p style="margin:0;font-size:14px;color:#b45309;">
                Advance paid: {{ number_format($booking->advance_amount, 3) }} OMR &mdash;
                Balance due: <strong>{{ number_format($booking->balance_due, 3) }} OMR</strong>
            </p>
        </td>
    </tr>
</table>
@elseif($booking->payment_status === 'pending')
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
       style="background:#fffbeb;border-radius:12px;border-{{ $isRtl ? 'right' : 'left' }}:4px solid #f59e0b;margin-bottom:20px;">
    <tr>
        <td style="padding:20px 24px;">
            <p style="margin:0 0 6px 0;font-size:15px;font-weight:700;color:#92400e;">
                &#9888; {{ __('emails.booking.payment_pending') }}
            </p>
            <p style="margin:0;font-size:14px;color:#b45309;">
                {{ __('emails.booking.payment_pending_desc', ['amount' => number_format($booking->total_amount, 3)]) }}
            </p>
        </td>
    </tr>
</table>
@endif

{{-- ✅ Important Notes --}}
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
       style="background:#fffbeb;border-radius:12px;border:1px solid #fde68a;margin-bottom:24px;">
    <tr>
        <td style="padding:20px 24px;">
            <p style="font-size:12px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.5px;margin:0 0 12px 0;padding-bottom:10px;border-bottom:2px solid #fde68a;">
                {{ __('emails.booking.important_notes') }}
            </p>
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td style="padding:5px 0;color:#78350f;font-size:14px;line-height:1.6;">
                        &bull;&nbsp; {{ __('emails.booking.note_arrive_early') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:5px 0;color:#78350f;font-size:14px;line-height:1.6;">
                        &bull;&nbsp; {{ __('emails.booking.note_bring_id') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:5px 0;color:#78350f;font-size:14px;line-height:1.6;">
                        &bull;&nbsp; {{ __('emails.booking.note_contact_changes') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ✅ CTA Button --}}
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:28px 0;">
    <tr>
        <td align="center">
            <a href="{{ route('customer.booking.details', $booking->id) }}"
               style="display:inline-block;background:#4f46e5;color:#ffffff;text-decoration:none;
                      padding:14px 36px;border-radius:8px;font-size:16px;font-weight:700;
                      letter-spacing:.3px;">
                {{ __('emails.booking.view_details') }}
            </a>
        </td>
    </tr>
    @if($booking->confirmation_pdf_path)
    <tr>
        <td align="center" style="padding-top:12px;">
            <a href="{{ Storage::url($booking->confirmation_pdf_path) }}"
               style="display:inline-block;background:#ffffff;color:#4f46e5;text-decoration:none;
                      padding:12px 32px;border-radius:8px;font-size:15px;font-weight:600;
                      border:2px solid #4f46e5;">
                {{ __('emails.booking.download_confirmation') }}
            </a>
        </td>
    </tr>
    @endif
</table>

{{-- Divider --}}
<div style="height:1px;background:#e5e7eb;margin:28px 0;"></div>

{{-- Contact / Sign-off --}}
<p style="font-size:14px;color:#6b7280;text-align:center;margin:0 0 16px 0;">
    {{ __('emails.booking.confirmed.questions') }}
</p>

<p style="text-align:center;color:#374151;font-size:15px;margin:0;">
    {{ __('emails.booking.regards') }}<br>
    <strong style="color:#1f2937;">{{ __('emails.booking.team', ['app' => config('app.name', 'Majalis')]) }}</strong>
</p>

@endsection
