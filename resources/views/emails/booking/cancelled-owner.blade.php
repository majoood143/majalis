{{--
/**
 * Booking Cancelled Notification Email for Hall Owners
 *
 * Sent to hall owners when a booking is cancelled.
 * Shows cancellation details and impact on earnings.
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
    
    // Owner name
    $ownerName = $booking->hall->owner->name ?? 'Hall Owner';
    
    // Calculate lost earnings
    $lostEarnings = $booking->owner_payout ?? ($booking->total_amount - ($booking->commission_amount ?? 0));
@endphp

@section('header-subtitle')
    {{ __('emails.owner.cancelled.subtitle') }}
@endsection

@section('content')
    {{-- Cancelled Icon --}}
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #f87171 0%, #ef4444 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
            <span style="font-size: 40px;">📋</span>
        </div>
    </div>

    {{-- Title --}}
    <h2 style="text-align: center; color: #dc2626;">{{ __('emails.owner.cancelled.title') }}</h2>
    
    <p style="text-align: center;">
        {{ __('emails.owner.greeting', ['name' => $ownerName]) }}
    </p>
    
    <p style="text-align: center; color: #6b7280;">
        {{ __('emails.owner.cancelled.intro', ['hall' => $hallName]) }}
    </p>

    {{-- Status Badge --}}
    <div style="text-align: center; margin: 24px 0;">
        <span class="status-badge status-cancelled">
            {{ __('emails.status.cancelled') }}
        </span>
    </div>

    {{-- Cancelled Booking Details --}}
    <div class="info-box" style="opacity: 0.9;">
        <div class="info-box-header">
            {{ __('emails.owner.cancelled.booking_details') }}
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
            <span class="info-label">{{ __('emails.owner.cancelled.customer') }}</span>
            <span class="info-value">{{ $booking->customer_name }}</span>
        </div>
    </div>

    {{-- Cancellation Reason --}}
    @if($booking->cancellation_reason)
    <div class="highlight-box danger">
        <p style="margin: 0 0 8px 0; font-weight: 600; color: #991b1b;">
            {{ __('emails.owner.cancelled.reason_title') }}
        </p>
        <p style="margin: 0; color: #b91c1c;">
            {{ $booking->cancellation_reason }}
        </p>
    </div>
    @endif

    {{-- Financial Impact --}}
    <div class="info-box" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-color: #fca5a5;">
        <div class="info-box-header" style="color: #991b1b; border-bottom-color: #fca5a5;">
            {{ __('emails.owner.cancelled.financial_impact') }}
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.owner.cancelled.original_booking') }}</span>
            <span class="info-value amount" style="text-decoration: line-through; color: #9ca3af;">{{ number_format($booking->total_amount, 3) }} {{ __('currency.omr') }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.owner.cancelled.lost_earnings') }}</span>
            <span class="info-value amount" style="color: #dc2626;">-{{ number_format($lostEarnings, 3) }} {{ __('currency.omr') }}</span>
        </div>
    </div>

    {{-- Slot Now Available --}}
    <div class="highlight-box info">
        <p style="margin: 0 0 8px 0; font-weight: 600; color: #1e40af;">
            <span style="font-size: 18px;">📅</span> {{ __('emails.owner.cancelled.slot_available') }}
        </p>
        <p style="margin: 0; color: #3b82f6;">
            {{ __('emails.owner.cancelled.slot_available_desc', [
                'date' => $booking->booking_date->format('d F Y'),
                'slot' => $timeSlot
            ]) }}
        </p>
    </div>

    {{-- CTA --}}
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('filament.owner.resources.bookings.index') }}" class="btn btn-primary">
            {{ __('emails.owner.cancelled.view_bookings') }}
        </a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        {{ __('emails.owner.cancelled.support_note') }}
    </p>

    <p style="text-align: center; margin-top: 20px;">
        {{ __('emails.booking.regards') }}<br>
        <strong>{{ __('emails.booking.team', ['app' => config('app.name', 'Majalis')]) }}</strong>
    </p>
@endsection
