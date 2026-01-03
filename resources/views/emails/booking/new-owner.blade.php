{{--
/**
 * New Booking Notification Email for Hall Owners
 *
 * Sent to hall owners when a new booking is received.
 * Shows booking details and action buttons.
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
    
    // Calculate owner's earnings
    $ownerEarnings = $booking->owner_payout ?? ($booking->total_amount - ($booking->commission_amount ?? 0));
@endphp

@section('header-subtitle')
    {{ __('emails.owner.new_booking.subtitle') }}
@endsection

@section('content')
    {{-- New Booking Icon --}}
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
            <span style="font-size: 40px;">🎊</span>
        </div>
    </div>

    {{-- Title --}}
    <h2 style="text-align: center; color: #16a34a;">{{ __('emails.owner.new_booking.title') }}</h2>
    
    <p style="text-align: center;">
        {{ __('emails.owner.greeting', ['name' => $ownerName]) }}
    </p>
    
    <p style="text-align: center; font-size: 18px; color: #1f2937;">
        {{ __('emails.owner.new_booking.intro', ['hall' => $hallName]) }}
    </p>

    {{-- Earnings Highlight --}}
    <div style="text-align: center; margin: 30px 0;">
        <div style="display: inline-block; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; padding: 20px 40px; border-radius: 16px; box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);">
            <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9;">
                {{ __('emails.owner.new_booking.your_earnings') }}
            </div>
            <div style="font-size: 36px; font-weight: 700; margin-top: 4px;">
                {{ number_format($ownerEarnings, 3) }} <span style="font-size: 18px;">{{ __('currency.omr') }}</span>
            </div>
        </div>
    </div>

    {{-- Booking Details --}}
    <div class="info-box">
        <div class="info-box-header">
            {{ __('emails.owner.new_booking.booking_details') }}
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

    {{-- Customer Information --}}
    <div class="info-box" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-color: #93c5fd;">
        <div class="info-box-header" style="color: #1e40af; border-bottom-color: #93c5fd;">
            {{ __('emails.owner.new_booking.customer_info') }}
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.owner.new_booking.customer_name') }}</span>
            <span class="info-value">{{ $booking->customer_name }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.owner.new_booking.customer_email') }}</span>
            <span class="info-value">
                <a href="mailto:{{ $booking->customer_email }}" style="color: #4f46e5;">{{ $booking->customer_email }}</a>
            </span>
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.owner.new_booking.customer_phone') }}</span>
            <span class="info-value">
                <a href="tel:{{ $booking->customer_phone }}" style="color: #4f46e5;">{{ $booking->customer_phone }}</a>
            </span>
        </div>
        
        @if($booking->customer_notes)
        <div style="margin-top: 16px; padding-top: 16px; border-top: 1px dashed #93c5fd;">
            <span class="info-label">{{ __('emails.owner.new_booking.special_notes') }}</span>
            <p style="margin: 8px 0 0 0; color: #1f2937; background: white; padding: 12px; border-radius: 8px;">
                {{ $booking->customer_notes }}
            </p>
        </div>
        @endif
    </div>

    {{-- Financial Summary --}}
    <div class="info-box" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-color: #86efac;">
        <div class="info-box-header" style="color: #166534; border-bottom-color: #86efac;">
            {{ __('emails.owner.new_booking.financial_summary') }}
        </div>
        
        <div class="info-row">
            <span class="info-label">{{ __('emails.booking.total_amount') }}</span>
            <span class="info-value amount">{{ number_format($booking->total_amount, 3) }} {{ __('currency.omr') }}</span>
        </div>
        
        @if($booking->commission_amount)
        <div class="info-row">
            <span class="info-label">{{ __('emails.owner.new_booking.platform_commission') }}</span>
            <span class="info-value amount" style="color: #dc2626;">-{{ number_format($booking->commission_amount, 3) }} {{ __('currency.omr') }}</span>
        </div>
        @endif
        
        <div class="info-row" style="border-top: 2px solid #86efac; padding-top: 12px; margin-top: 8px;">
            <span class="info-label" style="font-weight: 700; color: #166534;">{{ __('emails.owner.new_booking.your_payout') }}</span>
            <span class="info-value amount" style="font-size: 20px; color: #16a34a;">{{ number_format($ownerEarnings, 3) }} {{ __('currency.omr') }}</span>
        </div>
    </div>

    {{-- Action Required --}}
    @if($booking->hall->requires_approval && $booking->status === 'pending')
    <div class="highlight-box warning">
        <p style="margin: 0 0 8px 0; font-weight: 600; color: #92400e;">
            <span style="font-size: 18px;">⚡</span> {{ __('emails.owner.new_booking.action_required') }}
        </p>
        <p style="margin: 0; color: #b45309;">
            {{ __('emails.owner.new_booking.action_desc') }}
        </p>
    </div>
    @endif

    {{-- CTA Buttons --}}
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('filament.owner.resources.bookings.view', $booking->id) }}" class="btn btn-primary">
            {{ __('emails.owner.new_booking.view_booking') }}
        </a>
        
        @if($booking->hall->requires_approval && $booking->status === 'pending')
        <br>
        <a href="{{ route('filament.owner.resources.bookings.index') }}" class="btn btn-success" style="margin-top: 12px;">
            {{ __('emails.owner.new_booking.approve_booking') }}
        </a>
        @endif
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        {{ __('emails.owner.new_booking.manage_note') }}
    </p>

    <p style="text-align: center; margin-top: 20px;">
        {{ __('emails.booking.regards') }}<br>
        <strong>{{ __('emails.booking.team', ['app' => config('app.name', 'Majalis')]) }}</strong>
    </p>
@endsection
