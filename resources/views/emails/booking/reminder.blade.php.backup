{{--
/**
 * Booking Reminder Email Template
 *
 * Sent to customers 24 hours before their booking.
 * Includes all essential details for the event.
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
    
    // Time slot labels with approximate times
    $slotInfo = [
        'morning' => ['label' => __('slots.morning'), 'time' => '8:00 AM - 12:00 PM'],
        'afternoon' => ['label' => __('slots.afternoon'), 'time' => '1:00 PM - 5:00 PM'],
        'evening' => ['label' => __('slots.evening'), 'time' => '6:00 PM - 11:00 PM'],
        'full_day' => ['label' => __('slots.full_day'), 'time' => '8:00 AM - 11:00 PM'],
    ];
    $slotData = $slotInfo[$booking->time_slot] ?? ['label' => ucfirst($booking->time_slot), 'time' => ''];
@endphp

@section('header-subtitle')
    {{ __('emails.booking.reminder.subtitle') }}
@endsection

@section('content')
    {{-- Reminder Icon --}}
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
            <span style="font-size: 40px;">⏰</span>
        </div>
    </div>

    {{-- Title --}}
    <h2 style="text-align: center; color: #92400e;">{{ __('emails.booking.reminder.title') }}</h2>
    
    <p style="text-align: center;">
        {{ __('emails.booking.greeting', ['name' => $booking->customer_name]) }}
    </p>
    
    <p style="text-align: center; font-size: 18px; color: #1f2937;">
        {{ __('emails.booking.reminder.intro') }}
    </p>

    {{-- Tomorrow Countdown --}}
    <div style="text-align: center; margin: 30px 0;">
        <div style="display: inline-block; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: white; padding: 24px 48px; border-radius: 16px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
            <div style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9;">
                {{ __('emails.booking.reminder.tomorrow') }}
            </div>
            <div style="font-size: 28px; font-weight: 700; margin-top: 8px;">
                {{ $booking->booking_date->format('l, d F') }}
            </div>
        </div>
    </div>

    {{-- Booking Details --}}
    <div class="info-box" style="border: 2px solid #fbbf24; background: #fffbeb;">
        <div class="info-box-header" style="color: #92400e; border-bottom-color: #fde68a;">
            {{ __('emails.booking.reminder.event_details') }}
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
            <span class="info-value">
                {{ $slotData['label'] }}
                @if($slotData['time'])
                    <br><span style="font-size: 12px; color: #6b7280;">({{ $slotData['time'] }})</span>
                @endif
            </span>
        </div>
        
        @if($booking->number_of_guests)
        <div class="info-row">
            <span class="info-label">{{ __('emails.booking.guests') }}</span>
            <span class="info-value">{{ $booking->number_of_guests }} {{ __('emails.booking.persons') }}</span>
        </div>
        @endif
    </div>

    {{-- Hall Location --}}
    <div class="highlight-box info">
        <p style="margin: 0 0 12px 0; font-weight: 600; color: #1e40af; font-size: 16px;">
            <span style="font-size: 20px;">📍</span> {{ __('emails.booking.reminder.location') }}
        </p>
        <p style="margin: 0; color: #3b82f6; font-size: 15px;">
            <strong>{{ $hallName }}</strong><br>
            @if($booking->hall->address)
                {{ $booking->hall->address }}<br>
            @endif
            @if($booking->hall->city)
                {{ is_array($booking->hall->city->name) ? ($booking->hall->city->name[$locale] ?? $booking->hall->city->name['en']) : $booking->hall->city->name }}
            @endif
        </p>
        @if($booking->hall->google_maps_url)
        <p style="margin: 16px 0 0 0;">
            <a href="{{ $booking->hall->google_maps_url }}" class="btn btn-outline" style="padding: 10px 20px; font-size: 14px;">
                {{ __('emails.booking.view_map') }}
            </a>
        </p>
        @endif
    </div>

    {{-- Checklist --}}
    <div class="info-box">
        <div class="info-box-header">
            {{ __('emails.booking.reminder.checklist') }}
        </div>
        <table style="width: 100%;">
            <tr>
                <td style="padding: 8px 0; color: #4b5563;">
                    <span style="color: #10b981; margin-{{ $isRtl ? 'left' : 'right' }}: 8px;">☐</span>
                    {{ __('emails.booking.reminder.check_1') }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #4b5563;">
                    <span style="color: #10b981; margin-{{ $isRtl ? 'left' : 'right' }}: 8px;">☐</span>
                    {{ __('emails.booking.reminder.check_2') }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #4b5563;">
                    <span style="color: #10b981; margin-{{ $isRtl ? 'left' : 'right' }}: 8px;">☐</span>
                    {{ __('emails.booking.reminder.check_3') }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #4b5563;">
                    <span style="color: #10b981; margin-{{ $isRtl ? 'left' : 'right' }}: 8px;">☐</span>
                    {{ __('emails.booking.reminder.check_4') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Contact Numbers --}}
    @if($booking->hall->contact_phone || $booking->hall->owner->phone)
    <div class="highlight-box" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-{{ $isRtl ? 'right' : 'left' }}-color: #22c55e;">
        <p style="margin: 0 0 8px 0; font-weight: 600; color: #166534;">
            <span style="font-size: 18px;">📞</span> {{ __('emails.booking.reminder.contact') }}
        </p>
        <p style="margin: 0; color: #15803d;">
            @if($booking->hall->contact_phone)
                {{ __('emails.booking.reminder.hall_phone') }}: <strong>{{ $booking->hall->contact_phone }}</strong>
            @elseif($booking->hall->owner->phone)
                {{ __('emails.booking.reminder.contact_phone') }}: <strong>{{ $booking->hall->owner->phone }}</strong>
            @endif
        </p>
    </div>
    @endif

    {{-- CTA --}}
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('booking.show', $booking->id) }}" class="btn btn-primary">
            {{ __('emails.booking.view_details') }}
        </a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        {{ __('emails.booking.reminder.look_forward') }}
    </p>

    <p style="text-align: center; margin-top: 20px;">
        {{ __('emails.booking.regards') }}<br>
        <strong>{{ __('emails.booking.team', ['app' => config('app.name', 'Majalis')]) }}</strong>
    </p>
@endsection
