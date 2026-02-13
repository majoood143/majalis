{{--
    Guest Booking - View Booking Details

    Displays full booking details for guests accessing via their unique token.
    Shows status, payment info, and action buttons.

    @var Booking $booking The booking to display
--}}

@extends('layouts.customer')

@section('title', __('guest.page_title_details') . ' - ' . $booking->booking_number)

@section('content')
<div class="min-h-screen py-8 bg-gray-50" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="max-w-4xl px-4 mx-auto sm:px-6 lg:px-8">

        {{-- Page Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('guest.details_heading', ['booking_number' => $booking->booking_number]) }}</h1>
            <p class="mt-1 text-gray-600">{{ __('guest.details_subheading') }}</p>
        </div>

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="px-4 py-3 mb-6 text-green-700 border border-green-200 rounded-lg bg-green-50">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="px-4 py-3 mb-6 text-red-700 border border-red-200 rounded-lg bg-red-50">
                {{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="px-4 py-3 mb-6 text-yellow-700 border border-yellow-200 rounded-lg bg-yellow-50">
                {{ session('warning') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Main Content --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- Status Banner --}}
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 border-yellow-300 text-yellow-800',
                        'confirmed' => 'bg-green-100 border-green-300 text-green-800',
                        'cancelled' => 'bg-red-100 border-red-300 text-red-800',
                        'completed' => 'bg-blue-100 border-blue-300 text-blue-800',
                    ];
                    $statusColor = $statusColors[$booking->status] ?? 'bg-gray-100 border-gray-300 text-gray-800';

                    $paymentColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'paid' => 'bg-green-100 text-green-800',
                        'partial' => 'bg-blue-100 text-blue-800',
                        'refunded' => 'bg-purple-100 text-purple-800',
                    ];
                    $paymentColor = $paymentColors[$booking->payment_status] ?? 'bg-gray-100 text-gray-800';
                @endphp

                <div class="{{ $statusColor }} border rounded-lg p-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center">
                            @if($booking->status === 'confirmed')
                                <svg class="w-6 h-6 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @elseif($booking->status === 'pending')
                                <svg class="w-6 h-6 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @elseif($booking->status === 'cancelled')
                                <svg class="w-6 h-6 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @endif
                            <div>
                                <span class="text-lg font-semibold">{{ __('guest.details_label_booking_status') }}: {{ __('guest.status_' . $booking->status) }}</span>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $paymentColor }}">
                            {{ __('guest.details_label_payment') }}: {{ __('guest.payment_status_' . $booking->payment_status) }}
                        </span>
                    </div>

                    @if($booking->status === 'pending' && $booking->payment_status === 'pending')
                        <p class="mt-2 text-sm">
                            {{ __('guest.details_payment_pending_message') }}
                        </p>
                    @endif
                </div>

                {{-- Hall Information --}}
                <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('guest.details_section_hall_info') }}</h2>
                    </div>

                    <div class="p-6">
                        <div class="flex items-start space-x-4 rtl:space-x-reverse">
                            @if($booking->hall->featured_image)
                                <img
                                    src="{{ Storage::url($booking->hall->featured_image) }}"
                                    alt="{{ $booking->hall->getTranslation('name', app()->getLocale()) }}"
                                    class="object-cover w-24 h-24 rounded-lg"
                                >
                            @else
                                <div class="flex items-center justify-center w-24 h-24 bg-gray-200 rounded-lg">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $booking->hall->getTranslation('name', app()->getLocale()) }}
                                </h3>
                                <p class="text-gray-600">
                                    {{ $booking->hall->city?->getTranslation('name', app()->getLocale()) }},
                                    {{ $booking->hall->city?->region?->getTranslation('name', app()->getLocale()) }}
                                </p>
                                <p class="mt-1 text-sm text-gray-500">{{ $booking->hall->address }}</p>

                                @if($booking->hall->phone)
                                    <p class="mt-2 text-sm text-gray-600">
                                        <span class="font-medium">{{ __('guest.details_label_phone') }}:</span> {{ $booking->hall->phone }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Booking Details --}}
                <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('guest.details_section_booking_info') }}</h2>
                    </div>

                    <div class="p-6">
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm text-gray-500">{{ __('guest.details_label_date') }}</dt>
                                <dd class="font-medium text-gray-900">{{ $booking->booking_date->translatedFormat(__('guest.date_format')) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">{{ __('guest.details_label_time') }}</dt>
                                <dd class="font-medium text-gray-900">
                                    @php
                                        $timeSlotLabels = [
                                            'morning' => __('guest.time_slot_morning'),
                                            'afternoon' => __('guest.time_slot_afternoon'),
                                            'evening' => __('guest.time_slot_evening'),
                                            'full_day' => __('guest.time_slot_full_day'),
                                        ];
                                    @endphp
                                    {{ $timeSlotLabels[$booking->time_slot] ?? $booking->time_slot }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">{{ __('guest.details_label_guests') }}</dt>
                                <dd class="font-medium text-gray-900">{{ $booking->number_of_guests }}</dd>
                            </div>
                            @if($booking->event_type)
                                <div>
                                    <dt class="text-sm text-gray-500">{{ __('guest.details_label_event_type') }}</dt>
                                    <dd class="font-medium text-gray-900">{{ __('guest.event_type_' . $booking->event_type) }}</dd>
                                </div>
                            @endif
                        </dl>

                        @if($booking->customer_notes)
                            <div class="pt-4 mt-4 border-t border-gray-200">
                                <dt class="mb-1 text-sm text-gray-500">{{ __('guest.details_label_special_requests') }}</dt>
                                <dd class="text-gray-700">{{ $booking->customer_notes }}</dd>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Extra Services --}}
                @if($booking->extraServices->count() > 0)
                    <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h2 class="text-lg font-semibold text-gray-900">{{ __('guest.details_section_services') }}</h2>
                        </div>

                        <div class="p-6">
                            <ul class="divide-y divide-gray-200">
                                @foreach($booking->extraServices as $service)
                                    <li class="flex justify-between py-3">
                                        <div>
                                            {{-- FIX: BookingExtraService uses service_name (JSON snapshot), --}}
                                            {{-- not HasTranslations. Use the localized_name accessor. --}}
                                            <span class="font-medium text-gray-900">
                                                {{ $service->localized_name }}
                                            </span>
                                            {{-- FIX: extraServices() is HasMany, not BelongsToMany. --}}
                                            {{-- Access quantity directly, NOT via pivot. --}}
                                            @if($service->quantity > 1)
                                                <span class="text-sm text-gray-500">× {{ $service->quantity }}</span>
                                            @endif
                                        </div>
                                        {{-- FIX: Access total_price directly (HasMany, not pivot) --}}
                                        <span class="text-gray-600">
                                            {{ number_format((float) $service->total_price, 3) }} {{ __('guest.currency_omr') }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @else
                    <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                        <div class="p-6">
                            <p class="text-gray-500">{{ __('guest.details_no_services') }}</p>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Sidebar --}}
            <div class="space-y-6 lg:col-span-1">

                {{-- Price Summary --}}
                <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-semibold text-gray-900">{{ __('guest.details_section_price_summary') }}</h3>
                    </div>

                    <div class="p-4">
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('guest.price_hall_rental') }}</span>
                                <span>{{ number_format($booking->hall_price, 3) }} {{ __('guest.currency_omr') }}</span>
                            </div>

                            @if($booking->services_price > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('guest.price_services') }}</span>
                                    <span>{{ number_format($booking->services_price, 3) }} {{ __('guest.currency_omr') }}</span>
                                </div>
                            @endif

                            @if($booking->platform_fee > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('guest.price_platform_fee') }}</span>
                                    <span>{{ number_format($booking->platform_fee, 3) }} {{ __('guest.currency_omr') }}</span>
                                </div>
                            @endif

                            <hr class="my-2">

                            <div class="flex justify-between text-base font-semibold">
                                <span>{{ __('guest.price_total') }}</span>
                                <span class="text-primary-600">{{ number_format($booking->total_amount, 3) }} {{ __('guest.currency_omr') }}</span>
                            </div>

                            @if($booking->payment_type === 'advance')
                                <hr class="my-2">
                                <div class="flex justify-between text-green-600">
                                    <span>{{ __('guest.price_advance_paid') }}</span>
                                    <span>{{ number_format($booking->advance_amount, 3) }} {{ __('guest.currency_omr') }}</span>
                                </div>
                                @if($booking->balance_due > 0)
                                    <div class="flex justify-between text-orange-600">
                                        <span>{{ __('guest.price_balance_due') }}</span>
                                        <span>{{ number_format($booking->balance_due, 3) }} {{ __('guest.currency_omr') }}</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Customer Info --}}
                <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-semibold text-gray-900">{{ __('guest.details_section_your_info') }}</h3>
                    </div>

                    <div class="p-4 text-sm">
                        <p class="font-medium text-gray-900">{{ $booking->customer_name }}</p>
                        <p class="mt-1 text-gray-600">{{ $booking->customer_email }}</p>
                        <p class="text-gray-600">{{ $booking->customer_phone }}</p>

                        @if($booking->is_guest_booking)
                            <span class="inline-flex items-center px-2 py-1 mt-2 text-xs font-medium text-yellow-800 bg-yellow-100 rounded-full">
                                {{ __('guest.badge_guest') }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="space-y-3">
                    @if($booking->payment_status === 'pending')
                        <a
                            href="{{ route('guest.booking.payment', ['guest_token' => $booking->guest_token]) }}"
                            class="inline-flex items-center justify-center w-full px-4 py-3 font-medium text-white transition rounded-lg bg-primary-600 hover:bg-primary-700"
                        >
                            <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            {{ __('guest.btn_complete_payment') }}
                        </a>
                    @endif

                    <a
                        href="{{ route('guest.booking.download', ['guest_token' => $booking->guest_token]) }}"
                        class="inline-flex items-center justify-center w-full px-4 py-3 font-medium text-gray-700 transition border border-gray-300 rounded-lg hover:bg-gray-50"
                    >
                        <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ __('guest.btn_download_pdf') }}
                    </a>

                    {{-- Create Account if eligible --}}
                    @if($booking->canCreateAccount())
                        <a
                            href="{{ route('guest.booking.success', ['guest_token' => $booking->guest_token]) }}#create-account"
                            class="inline-flex items-center justify-center w-full px-4 py-3 font-medium transition border rounded-lg border-primary-300 text-primary-700 hover:bg-primary-50"
                        >
                            <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            {{ __('guest.btn_create_account') }}
                        </a>
                    @endif
                </div>

                {{-- Help Section --}}
                <div class="p-4 rounded-lg bg-blue-50">
                    <h4 class="mb-2 font-medium text-blue-900">{{ __('guest.details_need_help') }}</h4>
                    <p class="mb-3 text-sm text-blue-800">
                        {{ __('guest.details_help_message') }}
                    </p>
                    <a
                        href="mailto:support@majalis.om"
                        class="text-sm text-primary-600 hover:underline"
                    >
                        support@majalis.om
                    </a>
                </div>

            </div>
        </div>

        {{-- Thank You Message --}}
        <div class="p-6 mt-8 bg-white rounded-xl">
            <div class="text-center">
                <h3 class="mb-2 text-lg font-semibold text-gray-900">{{ __('guest.details_thank_you') }}</h3>
                <p class="text-gray-600">{{ __('guest.details_contact_info', ['support_email' => 'support@majalis.om']) }}</p>
            </div>
        </div>

        {{-- Back Link --}}
        <div class="mt-8 text-center">
            <a
                href="{{ route('customer.halls.index') }}"
                class="text-gray-500 hover:text-gray-700"
            >
                ← {{ __('guest.btn_browse_more_halls') }}
            </a>
        </div>

    </div>
</div>
@endsection
