
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
<div class="min-h-screen py-8 bg-gray-50" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="max-w-2xl px-4 mx-auto sm:px-6 lg:px-8">

        {{-- Success Icon & Message --}}
        <div class="mb-8 text-center">
            <div class="flex items-center justify-center w-20 h-20 mx-auto mb-4 bg-green-100 rounded-full">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="mb-2 text-2xl font-bold text-gray-900">{{ __('guest.success_title') }}</h1>
            <p class="text-gray-600">{{ __('guest.success_subtitle') }}</p>
        </div>

        {{-- Booking Details Card --}}
        <div class="mb-6 overflow-hidden bg-white shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('guest.success_booking_details') }}</h2>
                    <span class="px-3 py-1 text-sm font-medium text-green-800 bg-green-100 rounded-full">
                        {{ __('guest.status_confirmed') }}
                    </span>
                </div>
            </div>

            <div class="p-6">
                {{-- Booking Number --}}
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600">{{ __('guest.details_label_booking_number') }}</span>
                    <span class="font-semibold text-gray-900">{{ $booking->booking_number }}</span>
                </div>

                {{-- Hall --}}
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600">{{ __('guest.details_label_hall') }}</span>
                    <span class="font-medium text-gray-900">
                        {{ $booking->hall->getTranslation('name', app()->getLocale()) }}
                    </span>
                </div>

                {{-- Location --}}
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600">{{ __('guest.success_label_location') }}</span>
                    <span class="text-gray-900">
                        {{ $booking->hall->city?->getTranslation('name', app()->getLocale()) }}
                    </span>
                </div>

                {{-- Date --}}
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600">{{ __('guest.details_label_date') }}</span>
                    <span class="text-gray-900">
                        {{ $booking->booking_date->translatedFormat(__('guest.date_format')) }}
                    </span>
                </div>

                {{-- Time Slot --}}
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600">{{ __('guest.details_label_time') }}</span>
                    <span class="text-gray-900">
                        @php
                            $timeSlotLabels = [
                                'morning' => __('guest.time_slot_morning'),
                                'afternoon' => __('guest.time_slot_afternoon'),
                                'evening' => __('guest.time_slot_evening'),
                                'full_day' => __('guest.time_slot_full_day'),
                            ];
                        @endphp
                        {{ $timeSlotLabels[$booking->time_slot] ?? $booking->time_slot }}
                    </span>
                </div>

                {{-- Number of Guests --}}
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600">{{ __('guest.details_label_guests') }}</span>
                    <span class="text-gray-900">{{ $booking->number_of_guests }}</span>
                </div>

                {{-- Extra Services --}}
                @if($booking->extraServices->count() > 0)
                    <div class="py-3 border-b border-gray-100">
                        <span class="block mb-2 text-gray-600">{{ __('guest.success_label_additional_services') }}</span>
                        <ul class="space-y-1">
                            @foreach($booking->extraServices as $service)
                                <li class="flex justify-between text-sm">
                                    <span>{{ $service->getTranslation('name', app()->getLocale()) }}</span>
                                    <span class="text-gray-600">{{ number_format($service->pivot->total_price, 3) }} {{ __('guest.currency_omr') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Total Amount --}}
                <div class="flex items-center justify-between py-3 text-lg">
                    <span class="font-semibold text-gray-900">{{ __('guest.price_total') }}</span>
                    <span class="font-bold text-primary-600">
                        {{ number_format($booking->total_amount, 3) }} {{ __('guest.currency_omr') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Important Notice --}}
        <div class="p-4 mb-6 border border-blue-200 rounded-lg bg-blue-50">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 me-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="mb-1 font-medium text-blue-900">{{ __('guest.success_save_link_title') }}</h3>
                    <p class="text-sm text-blue-800">{{ __('guest.booking_access_info') }}</p>
                    <div class="p-2 mt-2 text-sm break-all bg-white border border-blue-200 rounded">
                        {{ route('guest.booking.show', ['guest_token' => $booking->guest_token]) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col gap-3 mb-8 sm:flex-row">
            <a
                href="{{ route('guest.booking.download', ['guest_token' => $booking->guest_token]) }}"
                class="inline-flex items-center justify-center flex-1 px-4 py-3 font-medium text-white transition rounded-lg bg-primary-600 hover:bg-primary-700"
            >
                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                {{ __('guest.btn_download_pdf') }}
            </a>
            <a
                href="{{ route('guest.booking.show', ['guest_token' => $booking->guest_token]) }}"
                class="inline-flex items-center justify-center flex-1 px-4 py-3 font-medium text-gray-700 transition border border-gray-300 rounded-lg hover:bg-gray-50"
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
            <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-blue-50">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('guest.create_account_title') }}</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ __('guest.create_account_description') }}</p>
                </div>

                <div class="p-6">
                    {{-- Benefits List --}}
                    <ul class="mb-6 space-y-3">
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
                                class="w-full px-4 py-3 font-semibold text-white transition rounded-lg bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                            >
                                {{ __('guest.btn_create_account') }}
                            </button>
                        </div>

                        <div x-show="showForm" x-cloak>
                            {{-- Pre-filled Info (Read-only) --}}
                            <div class="p-3 mb-4 text-sm rounded-lg bg-gray-50">
                                <p><span class="font-medium">{{ __('guest.details_label_guest_name') }}:</span> {{ $booking->customer_name }}</p>
                                <p><span class="font-medium">{{ __('guest.details_label_guest_email') }}:</span> {{ $booking->customer_email }}</p>
                            </div>

                            {{-- Password Field --}}
                            <div class="mb-4">
                                <label for="password" class="block mb-1 text-sm font-medium text-gray-700">
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
                                <label for="password_confirmation" class="block mb-1 text-sm font-medium text-gray-700">
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
                                    class="flex-1 px-4 py-3 font-semibold text-white transition rounded-lg bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:bg-gray-400"
                                >
                                    <span x-show="!isSubmitting">{{ __('guest.btn_create_account') }}</span>
                                    <span x-show="isSubmitting">{{ __('guest.btn_processing') }}</span>
                                </button>
                                <button
                                    type="button"
                                    @click="showForm = false"
                                    class="px-4 py-3 text-gray-700 transition border border-gray-300 rounded-lg hover:bg-gray-50"
                                >
                                    {{ __('guest.btn_cancel') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Skip Option --}}
                    <p class="mt-4 text-sm text-center text-gray-500">
                        <a href="{{ route('customer.halls.index') }}" class="hover:text-primary-600">
                            {{ __('guest.btn_skip_account') }} →
                        </a>
                    </p>
                </div>
            </div>
        @else
            {{-- Already has account or email exists --}}
            <div class="p-6 text-center rounded-lg bg-gray-50">
                <p class="mb-4 text-gray-600">{{ __('guest.account_already_exists') }}</p>
                <a
                    href="{{ route('login') }}"
                    class="inline-flex items-center px-4 py-2 text-white transition rounded-lg bg-primary-600 hover:bg-primary-700"
                >
                    {{ __('guest.btn_login_instead') }}
                </a>
            </div>
        @endif

        {{-- Back to Homepage --}}
        <div class="mt-8 text-center">
            <a
                href="{{ route('customer.halls.index') }}"
                class="text-gray-500 hover:text-gray-700"
            >
                ← {{ __('guest.btn_back_to_halls') }}
            </a>
        </div>

    </div>
</div>
@endsection

