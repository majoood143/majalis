
{{--
    Guest Booking - Step 1: Enter Guest Information

    This view displays the initial form where guests enter their name, email, and phone
    to start the booking process. Upon submission, an OTP is sent to verify the email.

    @var Hall $hall The hall being booked
--}}

@extends('layouts.customer')

@section('title', __('guest.page_title_book') . ' - ' . $hall->getTranslation('name', app()->getLocale()))

@section('content')
<div class="min-h-screen py-8 bg-gray-50" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="max-w-2xl px-4 mx-auto sm:px-6 lg:px-8">

        {{-- Progress Steps --}}
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-4 rtl:space-x-reverse">
                {{-- Step 1: Guest Info (Active) --}}
                <div class="flex items-center">
                    <span class="flex items-center justify-center w-8 h-8 text-sm font-medium text-white rounded-full bg-primary-600">
                        1
                    </span>
                    <span class="text-sm font-medium ms-2 text-primary-600">{{ __('guest.step_1_guest_info') }}</span>
                </div>

                <div class="w-12 h-0.5 bg-gray-300"></div>

                {{-- Step 2: Verify --}}
                <div class="flex items-center">
                    <span class="flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-600 bg-gray-300 rounded-full">
                        2
                    </span>
                    <span class="text-sm font-medium text-gray-500 ms-2">{{ __('guest.step_2_verify') }}</span>
                </div>

                <div class="w-12 h-0.5 bg-gray-300"></div>

                {{-- Step 3: Booking --}}
                <div class="flex items-center">
                    <span class="flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-600 bg-gray-300 rounded-full">
                        3
                    </span>
                    <span class="text-sm font-medium text-gray-500 ms-2">{{ __('guest.step_3_booking') }}</span>
                </div>
            </div>
        </div>

        {{-- Hall Info Card --}}
        <div class="flex items-center p-4 mb-6 space-x-4 bg-white rounded-lg shadow-sm rtl:space-x-reverse">
            @if($hall->featured_image)
                <img
                    src="{{ Storage::url($hall->featured_image) }}"
                    alt="{{ $hall->getTranslation('name', app()->getLocale()) }}"
                    class="object-cover w-20 h-20 rounded-lg"
                >
            @else
                <div class="flex items-center justify-center w-20 h-20 bg-gray-200 rounded-lg">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            @endif
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900">{{ $hall->getTranslation('name', app()->getLocale()) }}</h3>
                <p class="text-sm text-gray-500">{{ $hall->city?->getTranslation('name', app()->getLocale()) }}</p>
                <p class="text-sm font-medium text-primary-600">{{ number_format($hall->price_per_slot, 3) }}
                    
                     <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal" class="inline w-5 h-5 -mt-1">

                </p>
            </div>
        </div>

        {{-- Main Form Card --}}
        <div class="overflow-hidden bg-white shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('guest.step_1_guest_info') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('guest.guest_booking_note') }}</p>
            </div>

            <div class="p-6">
                {{-- Alert Messages --}}
                @if(session('error'))
                    <div class="px-4 py-3 mb-6 text-red-700 border border-red-200 rounded-lg bg-red-50">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('info'))
                    <div class="px-4 py-3 mb-6 text-blue-700 border border-blue-200 rounded-lg bg-blue-50">
                        {{ session('info') }}

                        @if(session('show_login_modal'))
                            <div class="flex flex-col gap-3 mt-4 sm:flex-row">
                                <a
                                    href="{{ route('login', ['redirect' => route('customer.book', $hall->slug)]) }}"
                                    class="inline-flex items-center justify-center px-4 py-2 text-white transition rounded-lg bg-primary-600 hover:bg-primary-700"
                                >
                                    {{ __('guest.btn_login_instead') }}
                                </a>
                                <button
                                    type="button"
                                    onclick="document.getElementById('guest-form').classList.remove('hidden')"
                                    class="inline-flex items-center justify-center px-4 py-2 text-gray-700 transition bg-gray-200 rounded-lg hover:bg-gray-300"
                                >
                                    {{ __('guest.btn_continue_as_guest') }}
                                </button>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Guest Form --}}
                <form
                    id="guest-form"
                    method="POST"
                    action="{{ route('guest.initiate', ['hall' => $hall->slug, 'lang' => app()->getLocale()]) }}"
                    class="{{ session('show_login_modal') ? 'hidden' : '' }}"
                >
                    @csrf

                    {{-- Name Field --}}
                    <div class="mb-5">
                        <label for="name" class="block mb-1 text-sm font-medium text-gray-700">
                            {{ __('guest.label_name') }} <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="{{ __('guest.placeholder_name') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror"
                            required
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email Field --}}
                    <div class="mb-5">
                        <label for="email" class="block mb-1 text-sm font-medium text-gray-700">
                            {{ __('guest.label_email') }} <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', session('registered_email')) }}"
                            placeholder="{{ __('guest.placeholder_email') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-500 @enderror"
                            required
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ __('guest.otp_info') }}</p>
                    </div>

                    {{-- Phone Field --}}
                    <div class="mb-6">
                        <label for="phone" class="block mb-1 text-sm font-medium text-gray-700">
                            {{ __('guest.label_phone') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 py-3 text-sm text-gray-500 border border-gray-300 border-e-0 bg-gray-50 rounded-s-lg">
                                +968
                            </span>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                value="{{ old('phone') }}"
                                placeholder="{{ __('guest.placeholder_phone') }}"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-e-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('phone') border-red-500 @enderror"
                                pattern="[0-9]{8}"
                                maxlength="8"
                                required
                            >
                        </div>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Terms & Conditions --}}
                    <div class="mb-6 text-xs text-gray-500">
                        {{ __('guest.terms_agree') }}
                    </div>

                    {{-- Submit Button --}}
                    <button
                        type="submit"
                        class="w-full px-4 py-3 font-semibold text-white transition rounded-lg bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    >
                        {{ __('guest.btn_continue') }}
                    </button>
                </form>

                {{-- Alternative: Login/Register --}}
                <div class="pt-6 mt-6 border-t border-gray-200">
                    <p class="mb-4 text-sm text-center text-gray-600">{{ __('guest.or') }}</p>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a
                            href="{{ route('login', ['redirect' => route('customer.book', $hall->slug)]) }}"
                            class="inline-flex items-center justify-center flex-1 px-4 py-2 text-gray-700 transition border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            {{ __('guest.modal_login_option') }}
                        </a>
                        <a
                            href="{{ route('register', ['redirect' => route('customer.book', $hall->slug)]) }}"
                            class="inline-flex items-center justify-center flex-1 px-4 py-2 text-gray-700 transition border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            {{ __('guest.modal_register_option') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Back Link --}}
        <div class="mt-8 text-center">
            <a
                href="{{ route('customer.halls.show', $hall->slug) }}"
                class="text-gray-500 hover:text-gray-700"
            >
                ← {{ __('guest.back') }}
            </a>
        </div>

        {{-- Footer Note --}}
        <div class="pt-6 mt-8 text-xs text-center text-gray-500 border-t border-gray-200">
            <p>{{ __('guest.rights_reserved') }}</p>
            <p class="mt-1">{{ __('guest.majalis') }}</p>
        </div>

    </div>
</div>
@endsection

