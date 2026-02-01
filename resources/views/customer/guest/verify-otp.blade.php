{{--
    Guest Booking - Step 2: Verify OTP

    This view displays the OTP verification form where guests enter the 6-digit code
    sent to their email. Also includes option to resend OTP.

    @var Hall $hall The hall being booked
    @var GuestSession $guestSession The guest session being verified
--}}

@extends('layouts.customer')

@section('title', __('guest.page_title_verify') . ' - ' . $hall->getTranslation('name', app()->getLocale()))

@section('content')
<div class="min-h-screen py-8 bg-gray-50" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="max-w-md px-4 mx-auto sm:px-6 lg:px-8">

        {{-- Progress Steps --}}
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-4 rtl:space-x-reverse">
                {{-- Step 1: Guest Info (Completed) --}}
                <div class="flex items-center">
                    <span class="flex items-center justify-center w-8 h-8 text-white bg-green-500 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </span>
                    <span class="text-sm font-medium text-green-600 ms-2">{{ __('guest.step_1_guest_info') }}</span>
                </div>

                <div class="w-12 h-0.5 bg-green-500"></div>

                {{-- Step 2: Verify (Active) --}}
                <div class="flex items-center">
                    <span class="flex items-center justify-center w-8 h-8 text-sm font-medium text-white rounded-full bg-primary-600">
                        2
                    </span>
                    <span class="text-sm font-medium ms-2 text-primary-600">{{ __('guest.step_2_verify') }}</span>
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

        {{-- Main Card --}}
        <div class="overflow-hidden bg-white shadow-sm rounded-xl">
            <div class="px-6 py-4 text-center border-b border-gray-200 bg-gray-50">
                {{-- Email Icon --}}
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-primary-100">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('guest.page_title_verify') }}</h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('guest.otp_info') }}
                </p>
                <p class="mt-2 text-sm font-medium text-gray-700">
                    {{ $guestSession->masked_email }}
                </p>
            </div>

            <div class="p-6">
                {{-- Alert Messages --}}
                @if(session('success'))
                    <div class="px-4 py-3 mb-6 text-green-700 border border-green-200 rounded-lg bg-green-50">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="px-4 py-3 mb-6 text-red-700 border border-red-200 rounded-lg bg-red-50">
                        {{ session('error') }}

                        @if(session('can_resend'))
                            <form
                                method="POST"
                                action="{{ route('guest.resend-otp', ['hall' => $hall->slug, 'lang' => app()->getLocale()]) }}"
                                class="mt-3"
                            >
                                @csrf
                                <button
                                    type="submit"
                                    class="text-sm underline text-primary-600 hover:text-primary-800"
                                >
                                    {{ __('guest.btn_resend_otp') }}
                                </button>
                            </form>
                        @endif
                    </div>
                @endif

                @if(session('warning'))
                    <div class="px-4 py-3 mb-6 text-yellow-700 border border-yellow-200 rounded-lg bg-yellow-50">
                        {{ session('warning') }}
                    </div>
                @endif

                {{-- OTP Form --}}
                <form
                    method="POST"
                    action="{{ route('guest.verify-otp.submit', ['hall' => $hall->slug, 'lang' => app()->getLocale()]) }}"
                    x-data="{ otp: '' }"
                >
                    @csrf

                    {{-- OTP Input --}}
                    <div class="mb-6">
                        <label for="otp" class="block mb-2 text-sm font-medium text-center text-gray-700">
                            {{ __('guest.label_otp') }}
                        </label>

                        {{-- 6-Digit OTP Input --}}
                        <div class="flex justify-center gap-2" dir="ltr">
                            @for($i = 0; $i < 6; $i++)
                            <input
                                type="text"
                                maxlength="1"
                                pattern="[0-9]"
                                inputmode="numeric"
                                class="w-12 text-2xl font-semibold text-center border border-gray-300 rounded-lg otp-digit h-14 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                data-index="{{ $i }}"
                            >
                            @endfor
                        </div>

                        {{-- Hidden field to store complete OTP --}}
                        <input type="hidden" name="otp" id="otp-hidden" x-model="otp">

                        @error('otp')
                            <p class="mt-2 text-sm text-center text-red-600">{{ $message }}</p>
                        @enderror

                        <p class="mt-3 text-xs text-center text-gray-500">
                            {{ __('guest.otp_expires_info') }}
                        </p>
                    </div>

                    {{-- Verify Button --}}
                    <button
                        type="submit"
                        id="verify-btn"
                        class="w-full px-4 py-3 font-semibold text-white transition rounded-lg bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:bg-gray-400 disabled:cursor-not-allowed"
                        disabled
                    >
                        {{ __('guest.btn_verify') }}
                    </button>
                </form>

                {{-- Resend OTP --}}
                <div class="mt-6 text-center">
                    <p class="mb-2 text-sm text-gray-500">{{ __('guest.receive_the_code') }}</p>
                    <form
                        method="POST"
                        action="{{ route('guest.resend-otp', ['hall' => $hall->slug, 'lang' => app()->getLocale()]) }}"
                        id="resend-form"
                    >
                        @csrf
                        <button
                            type="submit"
                            id="resend-btn"
                            class="text-sm font-medium text-primary-600 hover:text-primary-800 disabled:text-gray-400 disabled:cursor-not-allowed"
                        >
                            {{ __('guest.btn_resend_otp') }}
                        </button>
                    </form>
                    <p id="resend-countdown" class="hidden mt-1 text-xs text-gray-400"></p>
                </div>

                {{-- Back Link --}}
                <div class="mt-6 text-center">
                    <a
                        href="{{ route('guest.book', ['hall' => $hall->slug, 'lang' => app()->getLocale()]) }}"
                        class="text-sm text-gray-500 hover:text-gray-700"
                    >
                        ← {{ __('guest.btn_back_to_previous') }}
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const otpDigits = document.querySelectorAll('.otp-digit');
    const otpHidden = document.getElementById('otp-hidden');
    const verifyBtn = document.getElementById('verify-btn');
    const resendBtn = document.getElementById('resend-btn');
    const resendCountdown = document.getElementById('resend-countdown');

    // Function to update hidden OTP value
    function updateOtpValue() {
        let otp = '';
        otpDigits.forEach(digit => {
            otp += digit.value;
        });
        otpHidden.value = otp;

        // Enable/disable verify button
        verifyBtn.disabled = otp.length !== 6;
    }

    // Handle input on OTP digits
    otpDigits.forEach((digit, index) => {
        // Only allow numbers
        digit.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');

            if (this.value.length === 1 && index < 5) {
                otpDigits[index + 1].focus();
            }

            updateOtpValue();
        });

        // Handle backspace
        digit.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && this.value === '' && index > 0) {
                otpDigits[index - 1].focus();
            }
        });

        // Handle paste
        digit.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);

            for (let i = 0; i < pastedData.length && i < 6; i++) {
                otpDigits[i].value = pastedData[i];
            }

            if (pastedData.length > 0) {
                otpDigits[Math.min(pastedData.length, 5)].focus();
            }

            updateOtpValue();
        });
    });

    // Focus first digit on load
    otpDigits[0].focus();

    // Resend countdown (60 seconds)
    let countdown = 60;

    function updateCountdown() {
        if (countdown > 0) {
            resendBtn.disabled = true;
            resendCountdown.classList.remove('hidden');
            resendCountdown.textContent = `{{ __("Wait :seconds seconds") }}`.replace(':seconds', countdown);
            countdown--;
            setTimeout(updateCountdown, 1000);
        } else {
            resendBtn.disabled = false;
            resendCountdown.classList.add('hidden');
        }
    }

    // Start countdown on page load (simulating just sent)
    @if(session('success') && str_contains(session('success'), 'sent'))
        updateCountdown();
    @endif

    // Reset countdown on form submit
    document.getElementById('resend-form').addEventListener('submit', function() {
        countdown = 60;
        updateCountdown();
    });
});
</script>
@endpush
@endsection
