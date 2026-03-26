{{--
/**
 * Booking Cancelled Email Template
 *
 * Sent to customers when their booking is cancelled.
 * Shows cancellation details and refund information.
 *
 * @package Resources\Views\Emails\Booking
 *
 * Variables:
 * @var \App\Models\Booking $booking - The booking instance
 */
--}}
@extends('emails.layouts.base')

@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    
    // Get hall name based on locale
    $hallName = is_array($booking->hall->name) 
        ? ($booking->hall->name[$locale] ?? $booking->hall->name['en'] ?? 'N/A')
        : $booking->hall->name;
    
    // Time slot labels
    $slotLabels = [
        'morning' => __('slots.morning'),
        'afternoon' => __('slots.afternoon'),
        'evening' => __('slots.evening'),
        'full_day' => __('slots.full_day'),
    ];
    $timeSlot = $slotLabels[$booking->time_slot] ?? ucfirst(str_replace('_', ' ', $booking->time_slot));
    
    // Check if there's a refund
    $hasRefund = $booking->refund_amount && $booking->refund_amount > 0;
    $supportEmail = \App\Models\Setting::get('contact', 'support_email') ?? \App\Models\Setting::get('contact', 'email') ?? config('mail.support_email', 'support@majalis.om');
@endphp

@section('header-subtitle')
    {{ __('emails.booking.cancelled.subtitle') }}
@endsection

@section('content')
    {{-- Cancelled Icon --}}
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #f87171 0%, #ef4444 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
            <span style="font-size: 40px; color: white;">✕</span>
        </div>
    </div>

    {{-- Greeting --}}
    <h2 style="text-align: center; color: #dc2626;">{{ __('emails.booking.cancelled.title') }}</h2>
    
    <p style="text-align: center;">
        {{ __('emails.booking.greeting', ['name' => $booking->customer_name]) }}
    </p>
    
    <p style="text-align: center;">
        {{ __('emails.booking.cancelled.intro') }}
    </p>

    {{-- Status Badge --}}
    <div style="text-align: center; margin: 24px 0;">
        <span class="status-badge status-cancelled">
            {{ __('emails.status.cancelled') }}
        </span>
    </div>

    {{-- Cancelled Booking Details --}}
    <div class="info-box" style="opacity: 0.8;">
        <div class="info-box-header">
            {{ __('emails.booking.cancelled.details_title') }}
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.booking.booking_number') }}</span>
            <span class="info-value" style="text-decoration: line-through; color: #9ca3af;">{{ $booking->booking_number }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.booking.hall') }}</span>
            <span class="info-value">{{ $hallName }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.booking.date') }}</span>
            <span class="info-value">{{ $booking->booking_date->format('l, d F Y') }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.booking.time_slot') }}</span>
            <span class="info-value">{{ $timeSlot }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.booking.original_amount') }}</span>
            <span class="info-value amount" style="text-decoration: line-through; color: #9ca3af;">{{ number_format($booking->total_amount, 3) }} {{ __('currency.omr') }}</span>
        </div>
    </div>

    {{-- Cancellation Reason --}}
    @if($booking->cancellation_reason)
    <div class="highlight-box danger">
        <p style="margin: 0 0 8px 0; font-weight: 600; color: #991b1b;">
            {{ __('emails.booking.cancelled.reason_title') }}
        </p>
        <p style="margin: 0; color: #b91c1c;">
            {{ $booking->cancellation_reason }}
        </p>
    </div>
    @endif

    {{-- Refund Information --}}
    @if($hasRefund)
    <div class="highlight-box" style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border-{{ $isRtl ? 'right' : 'left' }}-color: #10b981;">
        <p style="margin: 0 0 8px 0; font-weight: 600; color: #065f46;">
            <span style="font-size: 18px;">💰</span> {{ __('emails.booking.cancelled.refund_title') }}
        </p>
        <p style="margin: 0;">
            <span class="amount-large" style="color: #059669;">{{ number_format($booking->refund_amount, 3) }} {{ __('currency.omr') }}</span>
        </p>
        <p style="margin: 12px 0 0 0; color: #047857; font-size: 14px;">
            {{ __('emails.booking.cancelled.refund_processing') }}
        </p>
    </div>
    @elseif($booking->payment_status === 'paid')
    <div class="highlight-box warning">
        <p style="margin: 0 0 8px 0; font-weight: 600; color: #92400e;">
            <span style="font-size: 18px;">ℹ️</span> {{ __('emails.booking.cancelled.no_refund_title') }}
        </p>
        <p style="margin: 0; color: #b45309; font-size: 14px;">
            {{ __('emails.booking.cancelled.no_refund_desc') }}
        </p>
    </div>
    @endif

    {{-- What's Next --}}
    <div class="info-box">
        <div class="info-box-header">
            {{ __('emails.booking.cancelled.what_next') }}
        </div>
        <p style="margin: 0; color: #4b5563;">
            {{ __('emails.booking.cancelled.what_next_desc') }}
        </p>
    </div>

    {{-- CTA Button --}}
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('halls.index') }}" class="btn btn-primary">
            {{ __('emails.booking.cancelled.browse_halls') }}
        </a>
    </div>

    <div class="divider"></div>

    {{-- Support --}}
    <p style="font-size: 14px; color: #6b7280; text-align: center;">
        {{ __('emails.booking.cancelled.questions') }}<br>
        <a href="mailto:{{ $supportEmail }}" style="color: #4f46e5;">
            {{ $supportEmail }}
        </a>
    </p>

    <p style="text-align: center; margin-top: 20px;">
        {{ __('emails.booking.regards') }}<br>
        <strong>{{ __('emails.booking.team', ['app' => config('app.name', 'Majalis')]) }}</strong>
    </p>
@endsection
