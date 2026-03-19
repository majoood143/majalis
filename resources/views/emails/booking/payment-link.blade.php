{{--
 * Payment Link Email
 *
 * Sent when admin creates a booking and shares the Thawani payment link.
 *
 * @var \App\Models\Booking $booking
 * @var string $paymentUrl
--}}
@extends('emails.layouts.base')

@php
    $locale   = app()->getLocale();
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

    $paymentAmount = $booking->isAdvancePayment() && $booking->advance_amount
        ? (float) $booking->advance_amount
        : (float) $booking->total_amount;
@endphp

@section('content')
    {{-- Icon --}}
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
            <span style="font-size: 36px;">💳</span>
        </div>
    </div>

    <h2 style="text-align: center; color: #4f46e5;">Complete Your Booking Payment</h2>

    <p style="text-align: center;">
        Dear {{ $booking->customer_name }},
    </p>

    <p style="text-align: center; color: #4b5563;">
        Your booking <strong>{{ $booking->booking_number }}</strong> is reserved.
        Please complete your payment to confirm it.
    </p>

    {{-- Booking Details --}}
    <div class="info-box">
        <div class="info-box-header">Booking Details</div>

        <div class="info-row">
            <span class="info-label">Booking #</span>
            <span class="info-value" style="color: #4f46e5; font-size: 16px;">{{ $booking->booking_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Hall</span>
            <span class="info-value">{{ $hallName }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date</span>
            <span class="info-value">{{ $booking->booking_date->format('l, d F Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Time Slot</span>
            <span class="info-value">{{ $timeSlot }}</span>
        </div>
        @if($booking->number_of_guests)
        <div class="info-row">
            <span class="info-label">Guests</span>
            <span class="info-value">{{ $booking->number_of_guests }}</span>
        </div>
        @endif
    </div>

    {{-- Amount Due --}}
    <div class="highlight-box" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-left: 4px solid #3b82f6;">
        <p style="margin: 0 0 4px 0; font-weight: 600; color: #1e40af; font-size: 15px;">
            Amount Due
        </p>
        <p style="margin: 0; font-size: 28px; font-weight: 700; color: #1d4ed8;">
            {{ number_format($paymentAmount, 3) }} OMR
        </p>
        @if($booking->isAdvancePayment())
        <p style="margin: 8px 0 0 0; font-size: 13px; color: #3b82f6;">
            Advance payment — remaining balance: {{ number_format((float)$booking->balance_due, 3) }} OMR
        </p>
        @endif
    </div>

    {{-- Pay Now Button --}}
    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ $paymentUrl }}"
           style="display: inline-block; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
                  color: white; text-decoration: none; padding: 16px 48px; border-radius: 12px;
                  font-size: 18px; font-weight: 700; letter-spacing: 0.5px;">
            Pay Now →
        </a>
    </div>

    <p style="text-align: center; font-size: 13px; color: #9ca3af;">
        This payment link will expire. If you have trouble clicking the button, copy and paste this URL into your browser:
    </p>
    <p style="text-align: center; font-size: 12px; color: #6b7280; word-break: break-all;">
        {{ $paymentUrl }}
    </p>

    <div class="divider"></div>

    <p style="text-align: center; font-size: 14px; color: #6b7280;">
        If you did not make this booking or have any questions, please contact us.
    </p>

    <p style="text-align: center; margin-top: 20px;">
        Best regards,<br>
        <strong>{{ config('app.name', 'Majalis') }} Team</strong>
    </p>
@endsection
