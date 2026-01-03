{{--
/**
 * Booking Completed Email Template
 *
 * Sent to customers after their event is completed.
 * Includes a review request to gather feedback.
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
@endphp

@section('header-subtitle')
    {{ __('emails.booking.completed.subtitle') }}
@endsection

@section('content')
    {{-- Thank You Icon --}}
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #f472b6 0%, #ec4899 50%, #db2777 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
            <span style="font-size: 50px;">🎉</span>
        </div>
    </div>

    {{-- Greeting --}}
    <h2 style="text-align: center; color: #1f2937;">{{ __('emails.booking.completed.title') }}</h2>
    
    <p style="text-align: center; font-size: 18px;">
        {{ __('emails.booking.greeting', ['name' => $booking->customer_name]) }}
    </p>
    
    <p style="text-align: center; color: #6b7280;">
        {{ __('emails.booking.completed.intro', ['hall' => $hallName]) }}
    </p>

    {{-- Status Badge --}}
    <div style="text-align: center; margin: 24px 0;">
        <span class="status-badge status-completed">
            {{ __('emails.status.completed') }}
        </span>
    </div>

    {{-- Event Summary --}}
    <div class="info-box" style="background: linear-gradient(135deg, #fdf4ff 0%, #fae8ff 100%); border-color: #e879f9;">
        <div class="info-box-header" style="color: #86198f; border-bottom-color: #f0abfc;">
            {{ __('emails.booking.completed.event_summary') }}
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
            <span class="info-label">{{ __('emails.booking.booking_number') }}</span>
            <span class="info-value">{{ $booking->booking_number }}</span>
        </div>
    </div>

    {{-- Review Request --}}
    <div style="text-align: center; margin: 40px 0; padding: 30px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 16px;">
        <p style="font-size: 24px; margin: 0 0 16px 0;">⭐⭐⭐⭐⭐</p>
        <h3 style="color: #92400e; margin: 0 0 12px 0;">{{ __('emails.booking.completed.review_title') }}</h3>
        <p style="color: #78350f; margin: 0 0 20px 0; font-size: 14px;">
            {{ __('emails.booking.completed.review_desc') }}
        </p>
        <a href="{{ route('booking.review', $booking->id) }}" class="btn" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white !important;">
            {{ __('emails.booking.completed.leave_review') }}
        </a>
    </div>

    {{-- Benefits of Reviewing --}}
    <div class="info-box">
        <div class="info-box-header">
            {{ __('emails.booking.completed.why_review') }}
        </div>
        <ul style="margin: 0; padding-{{ $isRtl ? 'right' : 'left' }}: 20px; color: #4b5563;">
            <li style="margin-bottom: 10px;">{{ __('emails.booking.completed.why_review_1') }}</li>
            <li style="margin-bottom: 10px;">{{ __('emails.booking.completed.why_review_2') }}</li>
            <li>{{ __('emails.booking.completed.why_review_3') }}</li>
        </ul>
    </div>

    {{-- Book Again CTA --}}
    <div class="highlight-box info">
        <p style="margin: 0 0 12px 0; font-weight: 600; color: #1e40af;">
            {{ __('emails.booking.completed.book_again_title') }}
        </p>
        <p style="margin: 0 0 16px 0; color: #3b82f6; font-size: 14px;">
            {{ __('emails.booking.completed.book_again_desc') }}
        </p>
        <a href="{{ route('halls.show', $booking->hall->slug ?? $booking->hall->id) }}" style="color: #4f46e5; font-weight: 600;">
            {{ __('emails.booking.completed.book_again_btn') }} →
        </a>
    </div>

    <div class="divider"></div>

    {{-- Thank You Note --}}
    <p style="text-align: center; font-size: 14px; color: #6b7280;">
        {{ __('emails.booking.completed.thank_you') }}
    </p>

    <p style="text-align: center; margin-top: 20px;">
        {{ __('emails.booking.regards') }}<br>
        <strong>{{ __('emails.booking.team', ['app' => config('app.name', 'Majalis')]) }}</strong>
    </p>
@endsection
