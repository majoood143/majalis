{{--
/**
 * Booking Confirmed Email Template
 *
 * Sent to customers when their booking is confirmed by owner/admin.
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
    
    // Days until booking
    $daysUntil = now()->startOfDay()->diffInDays($booking->booking_date->startOfDay(), false);
@endphp

@section('header-subtitle')
    {{ __('emails.booking.confirmed.subtitle') }}
@endsection

@section('content')
    {{-- Success Icon --}}
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
            <span style="font-size: 40px; color: white;">✓</span>
        </div>
    </div>

    {{-- Greeting --}}
    <h2 style="text-align: center; color: #059669;">{{ __('emails.booking.confirmed.title') }}</h2>
    
    <p style="text-align: center;">
        {{ __('emails.booking.greeting', ['name' => $booking->customer_name]) }}
    </p>
    
    <p style="text-align: center;">
        {{ __('emails.booking.confirmed.intro') }}
    </p>

    {{-- Countdown --}}
    @if($daysUntil > 0)
    <div style="text-align: center; margin: 30px 0;">
        <div style="display: inline-block; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; padding: 20px 40px; border-radius: 12px;">
            <div style="font-size: 48px; font-weight: 700; line-height: 1;">{{ $daysUntil }}</div>
            <div style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9;">
                {{ trans_choice('emails.booking.days_until', $daysUntil) }}
            </div>
        </div>
    </div>
    @endif

    {{-- Confirmation Badge --}}
    <div style="text-align: center; margin: 24px 0;">
        <span class="status-badge status-confirmed">
            {{ __('emails.status.confirmed') }}
        </span>
    </div>

    {{-- Booking Details --}}
    <div class="info-box">
        <div class="info-box-header">
            {{ __('emails.booking.your_booking') }}
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
    </div>

    {{-- Hall Location --}}
    @if($booking->hall->address || $booking->hall->city)
    <div class="highlight-box info">
        <p style="margin: 0 0 8px 0; font-weight: 600; color: #1e40af;">
            <span style="font-size: 18px;">📍</span> {{ __('emails.booking.location') }}
        </p>
        <p style="margin: 0; color: #3b82f6;">
            {{ $hallName }}<br>
            @if($booking->hall->address)
                {{ $booking->hall->address }}<br>
            @endif
            @if($booking->hall->city)
                {{ is_array($booking->hall->city->name) ? ($booking->hall->city->name[$locale] ?? $booking->hall->city->name['en']) : $booking->hall->city->name }}
            @endif
        </p>
        @if($booking->hall->google_maps_url)
        <p style="margin: 12px 0 0 0;">
            <a href="{{ $booking->hall->google_maps_url }}" style="color: #4f46e5; font-weight: 500;">
                {{ __('emails.booking.view_map') }} →
            </a>
        </p>
        @endif
    </div>
    @endif

    {{-- Payment Status --}}
    @if($booking->payment_status === 'paid')
    <div class="highlight-box" style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);">
        <p style="margin: 0; font-weight: 600; color: #065f46;">
            <span style="font-size: 18px;">✓</span> {{ __('emails.booking.payment_complete') }}
        </p>
        <p style="margin: 8px 0 0 0; color: #047857;">
            {{ __('emails.booking.amount_paid', ['amount' => number_format($booking->total_amount, 3)]) }}
        </p>
    </div>
    @elseif($booking->payment_status === 'pending')
    <div class="highlight-box warning">
        <p style="margin: 0; font-weight: 600; color: #92400e;">
            <span style="font-size: 18px;">⚠️</span> {{ __('emails.booking.payment_pending') }}
        </p>
        <p style="margin: 8px 0 0 0; color: #b45309;">
            {{ __('emails.booking.payment_pending_desc', ['amount' => number_format($booking->total_amount, 3)]) }}
        </p>
    </div>
    @endif

    {{-- Important Notes --}}
    <div class="info-box" style="border-color: #fbbf24; background: #fffbeb;">
        <div class="info-box-header" style="color: #92400e; border-bottom-color: #fde68a;">
            {{ __('emails.booking.important_notes') }}
        </div>
        <ul style="margin: 0; padding-{{ $isRtl ? 'right' : 'left' }}: 20px; color: #78350f;">
            <li style="margin-bottom: 8px;">{{ __('emails.booking.note_arrive_early') }}</li>
            <li style="margin-bottom: 8px;">{{ __('emails.booking.note_bring_id') }}</li>
            <li>{{ __('emails.booking.note_contact_changes') }}</li>
        </ul>
    </div>

    {{-- CTA Buttons --}}
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('booking.show', $booking->id) }}" class="btn btn-primary">
            {{ __('emails.booking.view_details') }}
        </a>
        
        @if($booking->confirmation_pdf_path)
        <br>
        <a href="{{ Storage::url($booking->confirmation_pdf_path) }}" class="btn btn-outline" style="margin-top: 12px;">
            {{ __('emails.booking.download_confirmation') }}
        </a>
        @endif
    </div>

    <div class="divider"></div>

    {{-- Contact --}}
    <p style="font-size: 14px; color: #6b7280; text-align: center;">
        {{ __('emails.booking.confirmed.questions') }}
    </p>

    <p style="text-align: center; margin-top: 20px;">
        {{ __('emails.booking.regards') }}<br>
        <strong>{{ __('emails.booking.team', ['app' => config('app.name', 'Majalis')]) }}</strong>
    </p>
@endsection
