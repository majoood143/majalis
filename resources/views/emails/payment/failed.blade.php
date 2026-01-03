{{--
/**
 * Payment Failed Email Template
 *
 * Sent to customers when payment fails.
 * Shows failure details and retry options.
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
    
    // Payment info
    $payment = $booking->payments()->latest()->first();
    $errorMessage = $payment->error_message ?? null;
@endphp

@section('header-subtitle')
    {{ __('emails.payment.failed.subtitle') }}
@endsection

@section('content')
    {{-- Failed Icon --}}
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #f87171 0%, #ef4444 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
            <span style="font-size: 40px; color: white;">!</span>
        </div>
    </div>

    {{-- Title --}}
    <h2 style="text-align: center; color: #dc2626;">{{ __('emails.payment.failed.title') }}</h2>
    
    <p style="text-align: center;">
        {{ __('emails.booking.greeting', ['name' => $booking->customer_name]) }}
    </p>
    
    <p style="text-align: center; color: #6b7280;">
        {{ __('emails.payment.failed.intro') }}
    </p>

    {{-- Amount --}}
    <div style="text-align: center; margin: 30px 0;">
        <div style="display: inline-block; background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); padding: 20px 40px; border-radius: 16px; border: 2px solid #fca5a5;">
            <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #991b1b;">
                {{ __('emails.payment.failed.amount_due') }}
            </div>
            <div style="font-size: 36px; font-weight: 700; color: #dc2626; margin-top: 4px;">
                {{ number_format($booking->total_amount, 3) }} <span style="font-size: 16px;">{{ __('currency.omr') }}</span>
            </div>
        </div>
    </div>

    {{-- Error Details --}}
    @if($errorMessage)
    <div class="highlight-box danger">
        <p style="margin: 0 0 8px 0; font-weight: 600; color: #991b1b;">
            {{ __('emails.payment.failed.error_title') }}
        </p>
        <p style="margin: 0; color: #b91c1c; font-family: monospace; font-size: 14px;">
            {{ $errorMessage }}
        </p>
    </div>
    @endif

    {{-- Booking Details --}}
    <div class="info-box">
        <div class="info-box-header">
            {{ __('emails.payment.failed.booking_details') }}
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.booking.booking_number') }}</span>
            <span class="info-value">{{ $booking->booking_number }}</span>
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
            <span class="info-label">{{ __('emails.payment.status') }}</span>
            <span class="info-value" style="color: #dc2626;">{{ __('emails.status.failed') }}</span>
        </div>
    </div>

    {{-- Common Reasons --}}
    <div class="info-box" style="background: #fffbeb; border-color: #fde68a;">
        <div class="info-box-header" style="color: #92400e; border-bottom-color: #fde68a;">
            {{ __('emails.payment.failed.common_reasons') }}
        </div>
        <ul style="margin: 0; padding-{{ $isRtl ? 'right' : 'left' }}: 20px; color: #78350f;">
            <li style="margin-bottom: 8px;">{{ __('emails.payment.failed.reason_1') }}</li>
            <li style="margin-bottom: 8px;">{{ __('emails.payment.failed.reason_2') }}</li>
            <li style="margin-bottom: 8px;">{{ __('emails.payment.failed.reason_3') }}</li>
            <li>{{ __('emails.payment.failed.reason_4') }}</li>
        </ul>
    </div>

    {{-- What to do --}}
    <div class="highlight-box info">
        <p style="margin: 0 0 8px 0; font-weight: 600; color: #1e40af;">
            {{ __('emails.payment.failed.what_to_do') }}
        </p>
        <p style="margin: 0; color: #3b82f6; font-size: 14px;">
            {{ __('emails.payment.failed.what_to_do_desc') }}
        </p>
    </div>

    {{-- Warning --}}
    <div class="highlight-box warning">
        <p style="margin: 0; color: #92400e; font-size: 14px;">
            <span style="font-size: 16px;">⏰</span> {{ __('emails.payment.failed.time_warning') }}
        </p>
    </div>

    {{-- CTA Buttons --}}
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('booking.payment', $booking->id) }}" class="btn btn-primary">
            {{ __('emails.payment.failed.retry_payment') }}
        </a>
        
        <br>
        <a href="mailto:{{ config('mail.support_email', 'support@majalis.om') }}" class="btn btn-outline" style="margin-top: 12px;">
            {{ __('emails.payment.failed.contact_support') }}
        </a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        {{ __('emails.payment.failed.support_note') }}
    </p>

    <p style="text-align: center; margin-top: 20px;">
        {{ __('emails.booking.regards') }}<br>
        <strong>{{ __('emails.booking.team', ['app' => config('app.name', 'Majalis')]) }}</strong>
    </p>
@endsection
