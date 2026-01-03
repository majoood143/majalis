{{--
/**
 * Payment Success Email Template
 *
 * Sent to customers when payment is successfully processed.
 * Shows payment details and receipt information.
 *
 * @package Resources\Views\Emails\Payment
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
    
    // Payment info
    $payment = $booking->payments()->latest()->first();
    $transactionId = $payment->transaction_id ?? $booking->transaction_id ?? 'N/A';
@endphp

@section('header-subtitle')
    {{ __('emails.payment.success.subtitle') }}
@endsection

@section('content')
    {{-- Success Icon --}}
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(34, 197, 94, 0.3);">
            <span style="font-size: 50px; color: white;">✓</span>
        </div>
    </div>

    {{-- Title --}}
    <h2 style="text-align: center; color: #16a34a;">{{ __('emails.payment.success.title') }}</h2>
    
    <p style="text-align: center;">
        {{ __('emails.booking.greeting', ['name' => $booking->customer_name]) }}
    </p>
    
    <p style="text-align: center; color: #6b7280;">
        {{ __('emails.payment.success.intro') }}
    </p>

    {{-- Amount Paid --}}
    <div style="text-align: center; margin: 30px 0;">
        <div style="display: inline-block; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); padding: 24px 48px; border-radius: 16px; border: 2px solid #86efac;">
            <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #166534;">
                {{ __('emails.payment.success.amount_paid') }}
            </div>
            <div style="font-size: 42px; font-weight: 700; color: #16a34a; margin-top: 4px;">
                {{ number_format($booking->total_amount, 3) }} <span style="font-size: 18px;">{{ __('currency.omr') }}</span>
            </div>
        </div>
    </div>

    {{-- Status Badge --}}
    <div style="text-align: center; margin: 24px 0;">
        <span class="status-badge status-paid">
            {{ __('emails.status.paid') }}
        </span>
    </div>

    {{-- Payment Details --}}
    <div class="info-box" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-color: #86efac;">
        <div class="info-box-header" style="color: #166534; border-bottom-color: #86efac;">
            {{ __('emails.payment.success.payment_details') }}
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.payment.transaction_id') }}</span>
            <span class="info-value" style="font-family: monospace; color: #4f46e5;">{{ $transactionId }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.payment.date') }}</span>
            <span class="info-value">{{ now()->format('d F Y, h:i A') }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.payment.method') }}</span>
            <span class="info-value">{{ ucfirst($payment->payment_method ?? 'Thawani') }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.payment.amount') }}</span>
            <span class="info-value amount" style="color: #16a34a; font-size: 18px;">{{ number_format($booking->total_amount, 3) }} {{ __('currency.omr') }}</span>
        </div>
    </div>

    {{-- Booking Summary --}}
    <div class="info-box">
        <div class="info-box-header">
            {{ __('emails.payment.success.booking_summary') }}
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.booking.booking_number') }}</span>
            <span class="info-value" style="color: #4f46e5; font-weight: 700;">{{ $booking->booking_number }}</span>
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
    </div>

    {{-- Confirmation Note --}}
    <div class="highlight-box" style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);">
        <p style="margin: 0 0 8px 0; font-weight: 600; color: #065f46;">
            <span style="font-size: 18px;">✅</span> {{ __('emails.payment.success.confirmed') }}
        </p>
        <p style="margin: 0; color: #047857; font-size: 14px;">
            {{ __('emails.payment.success.confirmed_desc') }}
        </p>
    </div>

    {{-- Receipt Note --}}
    <div class="highlight-box info">
        <p style="margin: 0; color: #3b82f6; font-size: 14px;">
            <span style="font-size: 16px;">📄</span> {{ __('emails.payment.success.receipt_note') }}
        </p>
    </div>

    {{-- CTA Buttons --}}
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('booking.show', $booking->id) }}" class="btn btn-primary">
            {{ __('emails.payment.success.view_booking') }}
        </a>
        
        @if($booking->invoice_path)
        <br>
        <a href="{{ Storage::url($booking->invoice_path) }}" class="btn btn-outline" style="margin-top: 12px;">
            {{ __('emails.payment.success.download_receipt') }}
        </a>
        @endif
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        {{ __('emails.payment.success.questions') }}
    </p>

    <p style="text-align: center; margin-top: 20px;">
        {{ __('emails.booking.regards') }}<br>
        <strong>{{ __('emails.booking.team', ['app' => config('app.name', 'Majalis')]) }}</strong>
    </p>
@endsection
