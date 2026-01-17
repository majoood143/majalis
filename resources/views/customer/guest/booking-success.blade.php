{{--
    Guest Booking - Success Page
    
    Displayed after successful payment. Shows booking confirmation
    and offers option to create an account for easier management.
    
    @var Booking $booking The confirmed booking
    @var bool $canCreateAccount Whether guest can create account
--}}

@extends('layouts.customer')

@section('title', __('guest.page_title_success'))

@section('content')
<div class="min-h-screen bg-gray-50 py-8" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Success Icon & Message --}}
        <div class="text-center mb-8">
            <div class="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ __('guest.page_title_success') }}</h1>
            <p class="text-gray-600">{{ __('Your booking has been confirmed successfully!') }}</p>
        </div>

        {{-- Booking Details Card --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('Booking Details') }}</h2>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                        {{ __('Confirmed') }}
                    </span>
                </div>
            </div>

            <div class="p-6">
                {{-- Booking Number --}}
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600">{{ __('Booking Number') }}</span>
                    <span class="font-semibold text-gray-900">{{ $booking->booking_number }}</span>
                </div>

                {{-- Hall --}}
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600">{{ __('Hall') }}</span>
                    <span class="font-medium text-gray-900">
                        {{ $booking->hall->getTranslation('name', app()->getLocale()) }}
                    </span>
                </div>

                {{-- Location --}}
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600">{{ __('Location') }}</span>
                    <span class="text-gray-900">
                        {{ $booking->hall->city?->getTranslation('name', app()->getLocale()) }}
                    </span>
                </div>

                {{-- Date --}}
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600">{{ __('Date') }}</span>
                    <span class="text-gray-900">
                        {{ $booking->booking_date->format('l, F j, Y') }}
                    </span>
                </div>

                {{-- Time Slot --}}
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600">{{ __('Time Slot') }}</span>
                    <span class="text-gray-900">
                        @php
                            $timeSlotLabels = [
                                'morning' => __('halls.time_slot_morning'),
                                'afternoon' => __('halls.time_slot_afternoon'),
                                'evening' => __('halls.time_slot_evening'),
                                'full_day' => __('halls.time_slot_full_day'),
                            ];
                        @endphp
                        {{ $timeSlotLabels[$booking->time_slot] ?? $booking->time_slot }}
                    </span>
                </div>

                {{-- Number of Guests --}}
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600">{{ __('Guests') }}</span>
                    <span class="text-gray-900">{{ $booking->number_of_guests }}</span>
                </div>

                {{-- Extra Services --}}
                @if($booking->extraServices->count() > 0)
                    <div class="py-3 border-b border-gray-100">
                        <span class="text-gray-600 block mb-2">{{ __('Additional Services') }}</span>
                        <ul class="space-y-1">
                            @foreach($booking->extraServices as $service)
                                <li class="flex justify-between text-sm">
                                    <span>{{ $service->getTranslation('name', app()->getLocale()) }}</span>
                                    <span class="text-gray-600">{{ number_format($service->pivot->total_price, 3) }} {{ __('OMR') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Total Amount --}}
                <div class="flex items-center justify-between py-3 text-lg">
                    <span class="font-semibold text-gray-900">{{ __('Total Amount') }}</span>
                    <span class="font-bold text-primary-600">
                        {{ number_format($booking->total_amount, 3) }} {{ __('OMR') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Important Notice --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 me-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="font-medium text-blue-900 mb-1">{{ __('Save Your Booking Link') }}</h3>
                    <p class="text-sm text-blue-800">{{ __('guest.booking_access_info') }}</p>
                    <div class="mt-2 p-2 bg-white rounded border border-blue-200 text-sm break-all">
                        {{ route('guest.booking.show', ['guest_token' => $booking->guest_token]) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-8">
            <a 
                href="{{ route('guest.booking.download', ['guest_token' => $booking->guest_token]) }}"
                class="flex-1 inline-flex justify-center items-center px-4 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition"
            >
                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                {{ __('guest.btn_download_pdf') }}
            </a>
            <a 
                href="{{ route('guest.booking.show', ['guest_token' => $booking->guest_token]) }}"
                class="flex-1 inline-flex justify-center items-center px-4 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition"
            >
                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                {{ __('guest.btn_view_booking') }}
            </a>
        </div>

        {{-- Create Account Section --}}
        @if($canCreateAccount)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-blue-50">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('guest.create_account_title') }}</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ __('guest.create_account_description') }}</p>
                </div>

                <div class="p-6">
                    {{-- Benefits List --}}
                    <ul class="space-y-3 mb-6">
                        @foreach(__('guest.create_account_benefits') as $benefit)
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 me-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">{{ $benefit }}</span>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Account Creation Form --}}
                    <form 
                        method="POST" 
                        action="{{ route('guest.create-account', ['guest_token' => $booking->guest_token]) }}"
                        x-data="{ showForm: false, isSubmitting: false }"
                    >
                        @csrf

                        <div x-show="!showForm">
                            <button 
                                type="button"
                                @click="showForm = true"
                                class="w-full py-3 px-4 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition"
                            >
                                {{ __('guest.btn_create_account') }}
                            </button>
                        </div>

                        <div x-show="showForm" x-cloak>
                            {{-- Pre-filled Info (Read-only) --}}
                            <div class="mb-4 p-3 bg-gray-50 rounded-lg text-sm">
                                <p><span class="font-medium">{{ __('Name') }}:</span> {{ $booking->customer_name }}</p>
                                <p><span class="font-medium">{{ __('Email') }}:</span> {{ $booking->customer_email }}</p>
                            </div>

                            {{-- Password Field --}}
                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('guest.label_password') }} <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('password') border-red-500 @enderror"
                                    required
                                    minlength="8"
                                >
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Confirm Password Field --}}
                            <div class="mb-6">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('guest.label_password_confirm') }} <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    required
                                    minlength="8"
                                >
                            </div>

                            {{-- Submit Buttons --}}
                            <div class="flex gap-3">
                                <button 
                                    type="submit"
                                    :disabled="isSubmitting"
                                    @click="isSubmitting = true"
                                    class="flex-1 py-3 px-4 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition disabled:bg-gray-400"
                                >
                                    <span x-show="!isSubmitting">{{ __('guest.btn_create_account') }}</span>
                                    <span x-show="isSubmitting">{{ __('Processing...') }}</span>
                                </button>
                                <button 
                                    type="button"
                                    @click="showForm = false"
                                    class="px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition"
                                >
                                    {{ __('Cancel') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Skip Option --}}
                    <p class="text-center text-sm text-gray-500 mt-4">
                        <a href="{{ route('customer.halls.index') }}" class="hover:text-primary-600">
                            {{ __('guest.btn_skip_account') }} →
                        </a>
                    </p>
                </div>
            </div>
        @else
            {{-- Already has account or email exists --}}
            <div class="bg-gray-50 rounded-lg p-6 text-center">
                <p class="text-gray-600 mb-4">{{ __('guest.account_already_exists') }}</p>
                <a 
                    href="{{ route('login') }}"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition"
                >
                    {{ __('guest.btn_login_instead') }}
                </a>
            </div>
        @endif

        {{-- Back to Homepage --}}
        <div class="text-center mt-8">
            <a 
                href="{{ route('customer.halls.index') }}"
                class="text-gray-500 hover:text-gray-700"
            >
                ← {{ __('Back to Halls') }}
            </a>
        </div>

    </div>
</div>
@endsection
