{{--
    Guest Booking - View Booking Details
    
    Displays full booking details for guests accessing via their unique token.
    Shows status, payment info, and action buttons.
    
    @var Booking $booking The booking to display
--}}

@extends('layouts.customer')

@section('title', __('guest.page_title_details') . ' - ' . $booking->booking_number)

@section('content')
<div class="min-h-screen bg-gray-50 py-8" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Page Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('guest.page_title_details') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('Booking Number') }}: <span class="font-semibold">{{ $booking->booking_number }}</span></p>
        </div>

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg">
                {{ session('warning') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                
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
                                <span class="font-semibold text-lg">{{ __('Booking Status') }}: {{ ucfirst($booking->status) }}</span>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $paymentColor }}">
                            {{ __('Payment') }}: {{ ucfirst($booking->payment_status) }}
                        </span>
                    </div>
                    
                    @if($booking->status === 'pending' && $booking->payment_status === 'pending')
                        <p class="mt-2 text-sm">
                            {{ __('Your booking is awaiting payment. Please complete payment to confirm.') }}
                        </p>
                    @endif
                </div>

                {{-- Hall Information --}}
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Hall Information') }}</h2>
                    </div>

                    <div class="p-6">
                        <div class="flex items-start space-x-4 rtl:space-x-reverse">
                            @if($booking->hall->featured_image)
                                <img 
                                    src="{{ Storage::url($booking->hall->featured_image) }}" 
                                    alt="{{ $booking->hall->getTranslation('name', app()->getLocale()) }}"
                                    class="w-24 h-24 rounded-lg object-cover"
                                >
                            @else
                                <div class="w-24 h-24 rounded-lg bg-gray-200 flex items-center justify-center">
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
                                <p class="text-sm text-gray-500 mt-1">{{ $booking->hall->address }}</p>
                                
                                @if($booking->hall->phone)
                                    <p class="text-sm text-gray-600 mt-2">
                                        <span class="font-medium">{{ __('Phone') }}:</span> {{ $booking->hall->phone }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Booking Details --}}
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Booking Details') }}</h2>
                    </div>

                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm text-gray-500">{{ __('Booking Date') }}</dt>
                                <dd class="text-gray-900 font-medium">{{ $booking->booking_date->format('l, F j, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">{{ __('Time Slot') }}</dt>
                                <dd class="text-gray-900 font-medium">
                                    @php
                                        $timeSlotLabels = [
                                            'morning' => __('Morning (8:00 AM - 12:00 PM)'),
                                            'afternoon' => __('Afternoon (1:00 PM - 5:00 PM)'),
                                            'evening' => __('Evening (6:00 PM - 10:00 PM)'),
                                            'full_day' => __('Full Day (8:00 AM - 10:00 PM)'),
                                        ];
                                    @endphp
                                    {{ $timeSlotLabels[$booking->time_slot] ?? $booking->time_slot }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">{{ __('Number of Guests') }}</dt>
                                <dd class="text-gray-900 font-medium">{{ $booking->number_of_guests }}</dd>
                            </div>
                            @if($booking->event_type)
                                <div>
                                    <dt class="text-sm text-gray-500">{{ __('Event Type') }}</dt>
                                    <dd class="text-gray-900 font-medium">{{ ucfirst($booking->event_type) }}</dd>
                                </div>
                            @endif
                        </dl>

                        @if($booking->customer_notes)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <dt class="text-sm text-gray-500 mb-1">{{ __('Special Requests') }}</dt>
                                <dd class="text-gray-700">{{ $booking->customer_notes }}</dd>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Extra Services --}}
                @if($booking->extraServices->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h2 class="text-lg font-semibold text-gray-900">{{ __('Additional Services') }}</h2>
                        </div>

                        <div class="p-6">
                            <ul class="divide-y divide-gray-200">
                                @foreach($booking->extraServices as $service)
                                    <li class="py-3 flex justify-between">
                                        <div>
                                            <span class="font-medium text-gray-900">
                                                {{ $service->getTranslation('name', app()->getLocale()) }}
                                            </span>
                                            @if($service->pivot->quantity > 1)
                                                <span class="text-gray-500 text-sm">× {{ $service->pivot->quantity }}</span>
                                            @endif
                                        </div>
                                        <span class="text-gray-600">
                                            {{ number_format($service->pivot->total_price, 3) }} {{ __('OMR') }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- Price Summary --}}
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-semibold text-gray-900">{{ __('Price Summary') }}</h3>
                    </div>

                    <div class="p-4">
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Hall Rental') }}</span>
                                <span>{{ number_format($booking->hall_price, 3) }} {{ __('OMR') }}</span>
                            </div>
                            
                            @if($booking->services_price > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('Services') }}</span>
                                    <span>{{ number_format($booking->services_price, 3) }} {{ __('OMR') }}</span>
                                </div>
                            @endif

                            @if($booking->platform_fee > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('Platform Fee') }}</span>
                                    <span>{{ number_format($booking->platform_fee, 3) }} {{ __('OMR') }}</span>
                                </div>
                            @endif

                            <hr class="my-2">

                            <div class="flex justify-between text-base font-semibold">
                                <span>{{ __('Total') }}</span>
                                <span class="text-primary-600">{{ number_format($booking->total_amount, 3) }} {{ __('OMR') }}</span>
                            </div>

                            @if($booking->payment_type === 'advance')
                                <hr class="my-2">
                                <div class="flex justify-between text-green-600">
                                    <span>{{ __('Advance Paid') }}</span>
                                    <span>{{ number_format($booking->advance_amount, 3) }} {{ __('OMR') }}</span>
                                </div>
                                @if($booking->balance_due > 0)
                                    <div class="flex justify-between text-orange-600">
                                        <span>{{ __('Balance Due') }}</span>
                                        <span>{{ number_format($booking->balance_due, 3) }} {{ __('OMR') }}</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Customer Info --}}
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-semibold text-gray-900">{{ __('Your Information') }}</h3>
                    </div>

                    <div class="p-4 text-sm">
                        <p class="font-medium text-gray-900">{{ $booking->customer_name }}</p>
                        <p class="text-gray-600 mt-1">{{ $booking->customer_email }}</p>
                        <p class="text-gray-600">{{ $booking->customer_phone }}</p>
                        
                        @if($booking->is_guest_booking)
                            <span class="inline-flex items-center mt-2 px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
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
                            class="w-full inline-flex justify-center items-center px-4 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition"
                        >
                            <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            {{ __('Complete Payment') }}
                        </a>
                    @endif

                    <a 
                        href="{{ route('guest.booking.download', ['guest_token' => $booking->guest_token]) }}"
                        class="w-full inline-flex justify-center items-center px-4 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition"
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
                            class="w-full inline-flex justify-center items-center px-4 py-3 border border-primary-300 text-primary-700 font-medium rounded-lg hover:bg-primary-50 transition"
                        >
                            <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            {{ __('guest.btn_create_account') }}
                        </a>
                    @endif
                </div>

                {{-- Help Section --}}
                <div class="bg-blue-50 rounded-lg p-4">
                    <h4 class="font-medium text-blue-900 mb-2">{{ __('Need Help?') }}</h4>
                    <p class="text-sm text-blue-800 mb-3">
                        {{ __('If you have any questions about your booking, please contact us.') }}
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

        {{-- Back Link --}}
        <div class="text-center mt-8">
            <a 
                href="{{ route('customer.halls.index') }}"
                class="text-gray-500 hover:text-gray-700"
            >
                ← {{ __('Browse More Halls') }}
            </a>
        </div>

    </div>
</div>
@endsection
