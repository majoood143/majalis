{{--
/**
 * Booking Created Email Template
 *
 * Sent to customers when a new booking is created.
 * Shows booking details and next steps.
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
@endphp

@section('header-subtitle')
    {{ __('emails.booking.created.subtitle') }}
@endsection

@section('content')
    {{-- Greeting --}}
    <h2>{{ __('emails.booking.greeting', ['name' => $booking->customer_name]) }}</h2>
    
    <p>{{ __('emails.booking.created.intro') }}</p>

    {{-- Status Badge --}}
    <div style="text-align: center; margin: 24px 0;">
        <span class="status-badge status-pending">
            {{ __('emails.status.pending') }}
        </span>
    </div>

    {{-- Booking Details --}}
    <div class="info-box">
        <div class="info-box-header">
            {{ __('emails.booking.details_title') }}
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.booking.booking_number') }}</span>
            <span class="info-value" style="color: #4f46e5; font-size: 16px;">{{ $booking->booking_number }}</span>
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
        
        @if($booking->number_of_guests)
        <div class="info-row">
            <span class="info-label">{{ __('emails.booking.guests') }}</span>
            <span class="info-value">{{ $booking->number_of_guests }} {{ __('emails.booking.persons') }}</span>
        </div>
        @endif
        
        @if($booking->event_type)
        <div class="info-row">
            <span class="info-label">{{ __('emails.booking.event_type') }}</span>
            <span class="info-value">{{ ucfirst($booking->event_type) }}</span>
        </div>
        @endif
    </div>

    {{-- Extra Services --}}
    @if($booking->extraServices && $booking->extraServices->count() > 0)
    <div class="info-box">
        <div class="info-box-header">
            {{ __('emails.booking.services_title') }}
        </div>
        
        @foreach($booking->extraServices as $service)
        <div class="info-row">
            <span class="info-label">
                {{ is_array($service->name) ? ($service->name[$locale] ?? $service->name['en'] ?? 'Service') : $service->name }}
            </span>
            <span class="info-value amount">{{ number_format($service->pivot->price ?? $service->price, 3) }} {{ __('currency.omr') }}</span>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Payment Summary --}}
    <div class="info-box" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
        <div class="info-box-header" style="border-bottom-color: #86efac;">
            {{ __('emails.booking.payment_summary') }}
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.booking.hall_price') }}</span>
            <span class="info-value amount">{{ number_format($booking->hall_price, 3) }} {{ __('currency.omr') }}</span>
        </div>
        
        @if($booking->services_price > 0)
        <div class="info-row">
            <span class="info-label">{{ __('emails.booking.services_price') }}</span>
            <span class="info-value amount">{{ number_format($booking->services_price, 3) }} {{ __('currency.omr') }}</span>
        </div>
        @endif
        
        @if($booking->discount_amount > 0)
        <div class="info-row">
            <span class="info-label">{{ __('emails.booking.discount') }}</span>
            <span class="info-value amount" style="color: #059669;">-{{ number_format($booking->discount_amount, 3) }} {{ __('currency.omr') }}</span>
        </div>
        @endif
        
        <div class="info-row" style="border-top: 2px solid #86efac; padding-top: 16px; margin-top: 8px;">
            <span class="info-label" style="font-weight: 700; font-size: 16px; color: #1f2937;">{{ __('emails.booking.total_amount') }}</span>
            <span class="info-value amount-large">{{ number_format($booking->total_amount, 3) }} {{ __('currency.omr') }}</span>
        </div>
    </div>

    {{-- Next Steps --}}
    <div class="highlight-box info">
        <p style="margin: 0; font-weight: 600; color: #1e40af;">
            <span style="font-size: 20px;">📋</span> {{ __('emails.booking.created.next_steps_title') }}
        </p>
        <p style="margin: 10px 0 0 0; color: #3b82f6;">
            {{ __('emails.booking.created.next_steps_desc') }}
        </p>
    </div>

    {{-- CTA Button --}}
    <div style="text-align: center; margin: 30px 0;">
        @if($booking->hall->requires_approval)
            <p style="color: #6b7280; font-size: 14px;">{{ __('emails.booking.created.awaiting_approval') }}</p>
        @else
            <a href="{{ route('booking.show', $booking->id) }}" class="btn btn-primary">
                {{ __('emails.booking.created.view_booking') }}
            </a>
        @endif
    </div>

    <div class="divider"></div>

    {{-- Contact Info --}}
    <p style="font-size: 14px; color: #6b7280;">
        {{ __('emails.booking.created.questions') }}
        <a href="mailto:{{ config('mail.support_email', 'support@majalis.om') }}" style="color: #4f46e5;">
            {{ config('mail.support_email', 'support@majalis.om') }}
        </a>
    </p>

    <p style="margin-top: 20px;">
        {{ __('emails.booking.regards') }}<br>
        <strong>{{ __('emails.booking.team', ['app' => config('app.name', 'Majalis')]) }}</strong>
    </p>
@endsection
