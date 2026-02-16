<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('halls.book_hall') }} -
        {{ is_array($hall->name) ? $hall->name[app()->getLocale()] ?? $hall->name['en'] : $hall->name }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tajawal Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap"
        rel="stylesheet">

    <style>
        * {
            font-family: 'Tajawal', 'system-ui', '-apple-system', sans-serif;
        }

        .glass-morphism {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .step-item {
            position: relative;
        }

        .step-item::after {
            content: '';
            position: absolute;
            top: 20px;
            {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 50%;
            width: 100%;
            height: 2px;
            background: #e5e7eb;
            z-index: -1;
        }

        .step-item:last-child::after {
            display: none;
        }

        .step-active .step-circle {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
            border-color: transparent;
        }

        .step-completed .step-circle {
            background: #10b981;
            color: white;
            border-color: transparent;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX({{ app()->getLocale() === 'ar' ? '-' : '' }}20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .slide-in {
            animation: slideInRight 0.3s ease-out;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
        }

        .safe-area-top {
            padding-top: env(safe-area-inset-top);
        }

        .safe-area-bottom {
            padding-bottom: env(safe-area-inset-bottom);
        }

        /* Loading spinner */
        .spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #0284c7;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50" x-data="bookingWizard()" x-init="init()">

    <!-- Top Navigation -->
    <nav class="sticky top-0 z-50 border-b border-gray-200 glass-morphism safe-area-top">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('customer.halls.show', $hall->slug) }}?lang={{ app()->getLocale() }}"
                    class="flex items-center gap-2 text-gray-700 transition hover:text-gray-900">
                    <svg class="w-6 h-6 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    <span class="font-medium">{{ __('halls.hall_details') }}</span>
                </a>

                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-10 h-10 shadow-lg bg-gradient-to-br rounded-xl">
                        <img src="{{ asset('images/logo.webp') }}" alt="Majalis Logo" class="w-8 h-8 rounded-xl">
                    </div>
                    <span class="hidden text-xl font-bold text-gray-800 sm:inline">
                        {{ app()->getLocale() === 'ar' ? 'مجالس' : 'Majalis' }}</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Progress Steps -->
    <div class="py-6 bg-white border-b border-gray-200">
        <div class="container px-4 mx-auto">
            <div class="max-w-3xl mx-auto">
                <div class="flex items-center justify-between">
                    <template x-for="(stepName, index) in steps" :key="index">
                        <div class="flex flex-col items-center flex-1 step-item"
                            :class="{ 'step-active': currentStep === index, 'step-completed': index < currentStep }">
                            <div
                                class="flex items-center justify-center w-10 h-10 mb-2 text-sm font-bold transition-all duration-300 border-2 border-gray-300 rounded-full step-circle md:w-12 md:h-12 md:text-base">
                                <template x-if="index < currentStep">
                                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </template>
                                <template x-if="index >= currentStep">
                                    <span x-text="index + 1"></span>
                                </template>
                            </div>
                            <span class="text-xs font-medium text-center text-gray-600 md:text-sm"
                                x-text="stepName"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ NEW: Advance Payment Notice -->
    @if ($hall->requiresAdvancePayment())
        <div class="container px-4 mx-auto mt-4">
            <div class="max-w-5xl p-4 mx-auto border-2 border-blue-200 bg-blue-50 rounded-xl">
                <div class="flex items-start gap-3">
                    <svg class="flex-shrink-0 w-6 h-6 text-blue-600 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <h4 class="mb-1 font-bold text-blue-900">{{ __('advance_payment.advance_required') }}</h4>
                        <p class="mb-2 text-sm text-blue-800">
                            @if ($hall->advance_payment_type === 'fixed')
                                {{ __('advance_payment.advance_payment_info', [
                                    'amount' => number_format($hall->advance_payment_amount, 3),
                                    'balance' => number_format(max(0, $hall->price_per_slot - $hall->advance_payment_amount), 3),
                                ]) }}
                            @else
                                {{ __('advance_payment.advance_payment_info', [
                                    'amount' => number_format(($hall->price_per_slot * $hall->advance_payment_percentage) / 100, 3),
                                    'balance' => number_format(
                                        $hall->price_per_slot - ($hall->price_per_slot * $hall->advance_payment_percentage) / 100,
                                        3,
                                    ),
                                ]) }}
                            @endif
                        </p>
                        <p class="text-xs text-blue-700">💡 {{ __('advance_payment.advance_includes_services') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Error Display -->
    @if (session('error'))
        <div class="container px-4 mx-auto mt-4">
            <div class="flex items-center max-w-5xl gap-3 p-4 mx-auto border-2 border-red-200 bg-red-50 rounded-xl">
                <svg class="flex-shrink-0 w-6 h-6 text-red-600" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium text-red-800">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="container px-4 mx-auto mt-4">
            <div class="max-w-5xl p-4 mx-auto border-2 border-red-200 bg-red-50 rounded-xl">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="flex-shrink-0 w-6 h-6 text-red-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-semibold text-red-800">{{ __('halls.please_fix_errors') }}</span>
                </div>
                <ul
                    class="space-y-1 text-sm list-disc list-inside text-red-700 {{ app()->getLocale() === 'ar' ? 'mr-6' : 'ml-6' }}">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <div class="container px-4 py-6 mx-auto md:py-8 safe-area-bottom">
        <form action="{{ route('customer.booking.store', $hall->slug) }}" method="POST" id="bookingForm">
            @csrf
            <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
            <template x-for="serviceId in selectedServices">
                <input type="hidden" name="services[]" :value="serviceId">
            </template>

            <div class="grid max-w-5xl grid-cols-1 gap-6 mx-auto lg:grid-cols-3">

                <!-- Main Form (Left Side) -->
                <div class="space-y-6 lg:col-span-2">

                    <!-- Step 1: Booking Details -->
                    <div x-show="currentStep === 0" x-transition
                        class="p-6 bg-white border border-gray-200 shadow-sm slide-in rounded-2xl md:p-8">
                        <h2 class="flex items-center gap-2 mb-6 text-2xl font-bold text-gray-900">
                            <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            {{ __('halls.booking_details') }}
                        </h2>

                        <!-- Date Selection -->
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-semibold text-gray-700">{{ __('halls.event_date') }}
                                <span class="text-red-500">*</span></label>
                            <input type="date" name="booking_date" x-model="formData.booking_date"
                                @change="checkAvailability()" :min="minDate"
                                class="w-full px-4 py-3 transition border-2 border-gray-300 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                                required>
                        </div>

                        <!-- Time Slot Selection -->
                        <div class="mb-6">
                            <label class="block mb-3 text-sm font-semibold text-gray-700">{{ __('halls.time_slot') }}
                                <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative">
                                    <input type="radio" name="time_slot" value="morning"
                                        x-model="formData.time_slot" @change="checkAvailability()"
                                        class="sr-only peer" required>
                                    <div
                                        class="p-4 text-center transition border-2 border-gray-300 cursor-pointer rounded-xl peer-checked:border-primary-500 peer-checked:bg-primary-50 hover:border-primary-300">
                                        <div class="mb-1 text-2xl">🌅</div>
                                        <div class="font-semibold text-gray-900">{{ __('halls.morning') }}</div>
                                        <div class="text-xs text-gray-500">8AM - 12PM</div>
                                    </div>
                                </label>
                                <label class="relative">
                                    <input type="radio" name="time_slot" value="afternoon"
                                        x-model="formData.time_slot" @change="checkAvailability()"
                                        class="sr-only peer">
                                    <div
                                        class="p-4 text-center transition border-2 border-gray-300 cursor-pointer rounded-xl peer-checked:border-primary-500 peer-checked:bg-primary-50 hover:border-primary-300">
                                        <div class="mb-1 text-2xl">☀️</div>
                                        <div class="font-semibold text-gray-900">{{ __('halls.afternoon') }}</div>
                                        <div class="text-xs text-gray-500">12PM - 5PM</div>
                                    </div>
                                </label>
                                <label class="relative">
                                    <input type="radio" name="time_slot" value="evening"
                                        x-model="formData.time_slot" @change="checkAvailability()"
                                        class="sr-only peer">
                                    <div
                                        class="p-4 text-center transition border-2 border-gray-300 cursor-pointer rounded-xl peer-checked:border-primary-500 peer-checked:bg-primary-50 hover:border-primary-300">
                                        <div class="mb-1 text-2xl">🌙</div>
                                        <div class="font-semibold text-gray-900">{{ __('halls.evening') }}</div>
                                        <div class="text-xs text-gray-500">5PM - 11PM</div>
                                    </div>
                                </label>
                                <label class="relative">
                                    <input type="radio" name="time_slot" value="full_day"
                                        x-model="formData.time_slot" @change="checkAvailability()"
                                        class="sr-only peer">
                                    <div
                                        class="p-4 text-center transition border-2 border-gray-300 cursor-pointer rounded-xl peer-checked:border-primary-500 peer-checked:bg-primary-50 hover:border-primary-300">
                                        <div class="mb-1 text-2xl">⏰</div>
                                        <div class="font-semibold text-gray-900">{{ __('halls.full_day') }}</div>
                                        <div class="text-xs text-gray-500">8AM - 11PM</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Availability Status -->
                        <div x-show="availabilityMessage" class="mb-6">
                            <div :class="{
                                'border-green-200 bg-green-50': isAvailable,
                                'border-red-200 bg-red-50': !isAvailable && !availabilityChecking,
                                'border-blue-200 bg-blue-50': availabilityChecking
                            }"
                                class="flex items-center gap-3 p-4 border-2 rounded-xl">
                                <template x-if="availabilityChecking">
                                    <div class="spinner"></div>
                                </template>
                                <template x-if="!availabilityChecking">
                                    <svg class="w-5 h-5"
                                        :class="{
                                            'text-green-600': isAvailable,
                                            'text-red-600': !isAvailable
                                        }"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            x-bind:d="isAvailable ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' :
                                                'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'">
                                        </path>
                                    </svg>
                                </template>
                                <span class="font-medium"
                                    :class="{
                                        'text-green-800': isAvailable,
                                        'text-red-800': !isAvailable && !availabilityChecking,
                                        'text-blue-800': availabilityChecking
                                    }"
                                    x-text="availabilityMessage"></span>
                            </div>
                        </div>

                        <!-- Number of Guests -->
                        <div class="mb-6">
                            <label
                                class="block mb-2 text-sm font-semibold text-gray-700">{{ __('halls.number_of_guests') }}
                                <span class="text-red-500">*</span></label>
                            <input type="number" name="number_of_guests" x-model="formData.number_of_guests"
                                min="{{ $hall->capacity_min }}" max="{{ $hall->capacity_max }}"
                                class="w-full px-4 py-3 transition border-2 border-gray-300 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                                required>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ __('halls.capacity_range', ['min' => $hall->capacity_min, 'max' => $hall->capacity_max]) }}
                            </p>
                        </div>

                        <!-- Event Type -->
                        <div class="mb-6">
                            <label
                                class="block mb-2 text-sm font-semibold text-gray-700">{{ __('halls.event_type') }}</label>
                            <select name="event_type" x-model="formData.event_type"
                                class="w-full px-4 py-3 transition border-2 border-gray-300 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200">
                                <option value="">{{ __('halls.select_event_type') }}</option>
                                <option value="wedding">{{ __('halls.wedding') }}</option>
                                <option value="corporate">{{ __('halls.corporate') }}</option>
                                <option value="birthday">{{ __('halls.birthday') }}</option>
                                <option value="conference">{{ __('halls.conference') }}</option>
                                <option value="graduation">{{ __('halls.graduation') }}</option>
                                <option value="other">{{ __('halls.other') }}</option>
                            </select>
                        </div>

                        <!-- Extra Services -->
                        @if ($hall->activeExtraServices->count() > 0)
                            <div>
                                <label
                                    class="block mb-3 text-sm font-semibold text-gray-700">{{ __('halls.extra_services') }}</label>
                                <div class="space-y-3">
                                    @foreach ($hall->activeExtraServices as $service)
                                        <label
                                            class="flex items-start gap-3 p-4 transition border-2 border-gray-200 cursor-pointer rounded-xl hover:border-primary-300">
                                            <input type="checkbox"
                                                @change="toggleService({{ $service->id }}, {{ $service->price }})"
                                                class="w-5 h-5 mt-1 border-gray-300 rounded text-primary-600 focus:ring-primary-500">
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="font-semibold text-gray-900">
                                                        {{ is_array($service->name) ? $service->name[app()->getLocale()] ?? $service->name['en'] : $service->name }}
                                                    </span>
                                                    <span
                                                        class="font-bold text-primary-600">{{ number_format($service->price, 3) }}
                                                        <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                                            class="inline w-5 h-5 -mt-1">
                                                    </span>

                                                </div>
                                                @if ($service->description)
                                                    <p class="text-sm text-gray-600">
                                                        {{ is_array($service->description) ? $service->description[app()->getLocale()] ?? $service->description['en'] : $service->description }}
                                                    </p>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Navigation -->
                        <div class="flex justify-end mt-8">
                            <button type="button" @click="nextStep()" :disabled="!canProceed()"
                                class="flex items-center gap-2 px-8 py-3 font-bold text-white transition rounded-xl"
                                :class="canProceed() ? 'bg-primary-600 hover:bg-primary-700' : 'bg-gray-400 cursor-not-allowed'">
                                {{ __('halls.next') }}
                                <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Your Information (Keep existing) -->
                    <div x-show="currentStep === 1" x-transition
                        class="p-6 bg-white border border-gray-200 shadow-sm slide-in rounded-2xl md:p-8">
                        <h2 class="flex items-center gap-2 mb-6 text-2xl font-bold text-gray-900">
                            <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ __('halls.your_information') }}
                        </h2>

                        <!-- Name -->
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-semibold text-gray-700">{{ __('halls.full_name') }}
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" x-model="formData.customer_name"
                                class="w-full px-4 py-3 transition border-2 border-gray-300 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                                required>
                        </div>

                        <!-- Email -->
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-semibold text-gray-700">{{ __('halls.email') }}
                                <span class="text-red-500">*</span></label>
                            <input type="email" name="customer_email" x-model="formData.customer_email"
                                class="w-full px-4 py-3 transition border-2 border-gray-300 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                                required>
                        </div>

                        <!-- Phone -->
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-semibold text-gray-700">{{ __('halls.phone') }}
                                <span class="text-red-500">*</span></label>
                            <input type="tel" name="customer_phone" x-model="formData.customer_phone"
                                class="w-full px-4 py-3 transition border-2 border-gray-300 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                                required>
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <label
                                class="block mb-2 text-sm font-semibold text-gray-700">{{ __('halls.additional_notes') }}</label>
                            <textarea name="customer_notes" x-model="formData.customer_notes" rows="4"
                                class="w-full px-4 py-3 transition border-2 border-gray-300 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                                placeholder="{{ __('halls.notes_placeholder') }}"></textarea>
                        </div>

                        <!-- Navigation -->
                        <div class="flex justify-between mt-8">
                            <button type="button" @click="previousStep()"
                                class="flex items-center gap-2 px-8 py-3 font-bold text-gray-700 transition bg-gray-200 rounded-xl hover:bg-gray-300">
                                <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                                </svg>
                                {{ __('halls.back') }}
                            </button>
                            <button type="button" @click="nextStep()" :disabled="!canProceed()"
                                class="flex items-center gap-2 px-8 py-3 font-bold text-white transition rounded-xl"
                                :class="canProceed() ? 'bg-primary-600 hover:bg-primary-700' : 'bg-gray-400 cursor-not-allowed'">
                                {{ __('halls.next') }}
                                <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </button>
                        </div>
                    </div>


                    <!-- Step 3: Review & Confirm -->
                    <div x-show="currentStep === 2" x-transition
                        class="p-6 bg-white border border-gray-200 shadow-sm slide-in rounded-2xl md:p-8">
                        <h2 class="flex items-center gap-2 mb-6 text-2xl font-bold text-gray-900">
                            <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('halls.review_confirm') }}
                        </h2>

                        <!-- Booking Summary Review -->
                        <div class="p-6 mb-6 space-y-4 border-2 border-gray-200 bg-gray-50 rounded-xl">
                            <h3 class="text-lg font-bold text-gray-900">{{ __('halls.booking_details') }}</h3>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label
                                        class="block mb-1 text-sm font-medium text-gray-600">{{ __('halls.event_date') }}</label>
                                    <div class="font-semibold text-gray-900" x-text="formData.booking_date"></div>
                                </div>

                                <div>
                                    <label
                                        class="block mb-1 text-sm font-medium text-gray-600">{{ __('halls.time_slot') }}</label>
                                    <div class="font-semibold text-gray-900"
                                        x-text="getTimeSlotLabel(formData.time_slot)"></div>
                                </div>

                                <div>
                                    <label
                                        class="block mb-1 text-sm font-medium text-gray-600">{{ __('halls.number_of_guests') }}</label>
                                    <div class="font-semibold text-gray-900" x-text="formData.number_of_guests"></div>
                                </div>

                                <div>
                                    <label
                                        class="block mb-1 text-sm font-medium text-gray-600">{{ __('halls.event_type') }}</label>
                                    <div class="font-semibold text-gray-900"
                                        x-text="formData.event_type ? getEventTypeLabel(formData.event_type) : '-'">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information Review -->
                        <div class="p-6 mb-6 space-y-4 border-2 border-gray-200 bg-gray-50 rounded-xl">
                            <h3 class="text-lg font-bold text-gray-900">{{ __('halls.your_information') }}</h3>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label
                                        class="block mb-1 text-sm font-medium text-gray-600">{{ __('halls.full_name') }}</label>
                                    <div class="font-semibold text-gray-900" x-text="formData.customer_name"></div>
                                </div>

                                <div>
                                    <label
                                        class="block mb-1 text-sm font-medium text-gray-600">{{ __('halls.email') }}</label>
                                    <div class="font-semibold text-gray-900" x-text="formData.customer_email"></div>
                                </div>

                                <div>
                                    <label
                                        class="block mb-1 text-sm font-medium text-gray-600">{{ __('halls.phone') }}</label>
                                    <div class="font-semibold text-gray-900" x-text="formData.customer_phone"></div>
                                </div>

                                <div x-show="formData.customer_notes" class="md:col-span-2">
                                    <label
                                        class="block mb-1 text-sm font-medium text-gray-600">{{ __('halls.additional_notes') }}</label>
                                    <div class="font-semibold text-gray-900" x-text="formData.customer_notes || '-'">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Selected Services Review -->
                        <div x-show="selectedServices.length > 0"
                            class="p-6 mb-6 space-y-4 border-2 border-gray-200 bg-gray-50 rounded-xl">
                            <h3 class="text-lg font-bold text-gray-900">{{ __('halls.extra_services') }}</h3>
                            <div class="text-sm text-gray-600">
                                <span x-text="selectedServices.length"></span> {{ __('halls.services_selected') }}
                            </div>
                        </div>

                        <!-- Terms & Conditions -->
                        <div class="p-6 mb-6 border-2 border-primary-200 bg-primary-50 rounded-xl">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" x-model="agreeToTerms"
                                    class="w-5 h-5 mt-1 border-gray-300 rounded text-primary-600 focus:ring-primary-500">
                                <div class="flex-1">
                                    <span class="font-semibold text-gray-900">{{ __('halls.agree_terms') }}</span>
                                    <p class="mt-1 text-sm text-gray-600">{{ __('halls.terms_description') }}</p>
                                </div>
                            </label>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-8">
                            <button type="button" @click="previousStep()"
                                class="flex items-center gap-2 px-8 py-3 font-bold text-gray-700 transition bg-gray-200 rounded-xl hover:bg-gray-300">
                                <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                                </svg>
                                {{ __('halls.back') }}
                            </button>

                            <button type="button" @click="submitForm()" :disabled="!agreeToTerms || submitting"
                                class="flex items-center gap-2 px-8 py-3 font-bold text-white transition rounded-xl"
                                :class="agreeToTerms && !submitting ? 'bg-green-600 hover:bg-green-700' :
                                    'bg-gray-400 cursor-not-allowed'">
                                <template x-if="submitting">
                                    <div class="spinner"></div>
                                </template>
                                <template x-if="!submitting">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </template>
                                <span
                                    x-text="submitting ? '{{ __('halls.processing') }}' : '{{ __('halls.confirm_book') }}'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Booking Summary Sidebar (Right Side) -->
                <div class="lg:col-span-1">
                    <div class="sticky p-6 bg-white border border-gray-200 shadow-sm top-24 rounded-2xl">
                        <h3 class="mb-4 text-lg font-bold text-gray-900">{{ __('halls.booking_summary') }}</h3>

                        <!-- Hall Info -->
                        <div class="mb-6">
                            <div class="h-32 mb-3 overflow-hidden bg-gray-200 rounded-xl">
                                @if ($hall->featured_image)
                                    <img src="{{ asset('storage/' . $hall->featured_image) }}"
                                        class="object-cover w-full h-full">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-primary-100 to-primary-200"></div>
                                @endif
                            </div>
                            <h4 class="font-semibold text-gray-900">
                                {{ is_array($hall->name) ? $hall->name[app()->getLocale()] ?? $hall->name['en'] : $hall->name }}
                            </h4>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ is_array($hall->city->name) ? $hall->city->name[app()->getLocale()] ?? $hall->city->name['en'] : $hall->city->name }}
                            </p>
                        </div>

                        <!-- Pricing -->
                        <div class="py-4 space-y-3 border-t border-gray-200">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ __('halls.hall_price') }}</span>
                                <span class="font-medium text-gray-900" x-text="hallPrice.toFixed(3) ">

                                </span>  <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                        class="inline w-5 h-5 -mt-1">
                            </div>
                            <div x-show="servicesTotal > 0" class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ __('halls.extra_services_total') }}</span>
                                <span class="font-medium text-gray-900" x-text="servicesTotal.toFixed(3)">

                                </span> <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                        class="inline w-5 h-5 -mt-1">
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="pt-4 border-t-2 border-gray-300">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-lg font-bold text-gray-900">{{ __('halls.total') }}</span>
                                <span class="text-2xl font-bold text-primary-600" x-text="total.toFixed(3)">
                                </span>
                                 <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                        class="inline w-5 h-5 -mt-1">
                            </div>
                        </div>


                        <!-- ✅ NEW: Advance Payment Preview -->
                        @if ($hall->requiresAdvancePayment())
                            <div class="p-4 mt-4 border-2 border-blue-200 bg-blue-50 rounded-xl">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    <span
                                        class="text-sm font-bold text-blue-900">{{ __('advance_payment.payment_type') }}</span>
                                </div>
                                @if ($hall->advance_payment_type === 'fixed')
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span
                                                class="text-blue-700">{{ __('advance_payment.customer_pays_advance') }}:</span>
                                            <span
                                                class="font-bold text-blue-900">{{ number_format($hall->advance_payment_amount, 3) }}
                                                </span>
                                                <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                                    class="inline w-5 h-5 -mt-1">
                                        </div>
                                        <div class="flex justify-between">
                                            <span
                                                class="text-blue-700">{{ __('advance_payment.balance_due') }}:</span>
                                            <span class="font-semibold text-blue-800"
                                                x-text="(total - {{ $hall->advance_payment_amount }}).toFixed(3)">

                                            </span> <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                                    class="inline w-5 h-5 -mt-1">
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span
                                                class="text-blue-700">{{ __('advance_payment.customer_pays_advance') }}
                                                ({{ $hall->advance_payment_percentage }}%):</span>
                                            <span class="font-bold text-blue-900"
                                                x-text="(total * {{ $hall->advance_payment_percentage }} / 100).toFixed(3)">
                                               </span> <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                                    class="inline w-5 h-5 -mt-1">
                                        </div>
                                        <div class="flex justify-between">
                                            <span
                                                class="text-blue-700">{{ __('advance_payment.balance_due') }}:</span>
                                            <span class="font-semibold text-blue-800"
                                                x-text="(total - (total * {{ $hall->advance_payment_percentage }} / 100)).toFixed(3)">

                                            </span> <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                                    class="inline w-5 h-5 -mt-1">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Contact Info -->
                        <div class="pt-6 mt-6 border-t border-gray-200">
                            <p class="flex items-center gap-2 text-xs text-gray-500">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ __('halls.terms_agree') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @include('layouts.footer')
    <script>
        function bookingWizard() {
            return {
                currentStep: 0,
                submitting: false,
                steps: @json([__('halls.booking_details'), __('halls.your_information'), __('halls.review_confirm')]),
                formData: {
                    booking_date: '{{ old('booking_date') }}',
                    time_slot: '{{ old('time_slot') }}',
                    number_of_guests: {{ old('number_of_guests', $hall->capacity_min) }},
                    event_type: '{{ old('event_type') }}',
                    customer_name: '{{ old('customer_name', Auth::user()->name ?? '') }}',
                    customer_email: '{{ old('customer_email', Auth::user()->email ?? '') }}',
                    customer_phone: '{{ old('customer_phone', Auth::user()->phone ?? '') }}',
                    customer_notes: '{{ old('customer_notes') }}',
                },
                selectedServices: [],
                servicesTotal: 0,
                hallPrice: {{ $hall->price_per_slot }},
                total: {{ $hall->price_per_slot }},
                isAvailable: false,
                availabilityMessage: '',
                availabilityChecking: false,
                agreeToTerms: false,
                minDate: new Date(Date.now() + 86400000).toISOString().split('T')[0],

                init() {
                    console.log('Booking wizard initialized');
                    if (this.formData.booking_date && this.formData.time_slot) {
                        this.checkAvailability();
                    }
                },

                async checkAvailability() {
                    if (!this.formData.booking_date || !this.formData.time_slot) {
                        this.availabilityMessage = '';
                        return;
                    }

                    this.availabilityChecking = true;
                    this.availabilityMessage = '';

                    try {
                        const response = await fetch('{{ route('customer.check-availability') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                hall_id: {{ $hall->id }},
                                booking_date: this.formData.booking_date,
                                time_slot: this.formData.time_slot
                            })
                        });

                        const data = await response.json();
                        this.isAvailable = data.available;
                        this.availabilityMessage = data.message;

                        console.log('Availability:', data);
                    } catch (error) {
                        console.error('Availability check error:', error);
                        this.availabilityMessage = '{{ __('halls.checking_availability') }}';
                        this.isAvailable = false;
                    } finally {
                        this.availabilityChecking = false;
                    }
                },

                toggleService(serviceId, price) {
                    const index = this.selectedServices.indexOf(serviceId);
                    if (index > -1) {
                        this.selectedServices.splice(index, 1);
                        this.servicesTotal -= price;
                    } else {
                        this.selectedServices.push(serviceId);
                        this.servicesTotal += price;
                    }
                    this.calculateTotal();
                },

                calculateTotal() {
                    this.total = this.hallPrice + this.servicesTotal;
                },

                canProceed() {
                    if (this.currentStep === 0) {
                        return this.formData.booking_date &&
                            this.formData.time_slot &&
                            this.formData.number_of_guests &&
                            this.isAvailable;
                    }
                    if (this.currentStep === 1) {
                        return this.formData.customer_name &&
                            this.formData.customer_email &&
                            this.formData.customer_phone;
                    }
                    return true;
                },

                nextStep() {
                    if (this.canProceed() && this.currentStep < 2) {
                        this.currentStep++;
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                },

                previousStep() {
                    if (this.currentStep > 0) {
                        this.currentStep--;
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                },

                getTimeSlotLabel(slot) {
                    const labels = {
                        'morning': '{{ __('halls.morning') }}',
                        'afternoon': '{{ __('halls.afternoon') }}',
                        'evening': '{{ __('halls.evening') }}',
                        'full_day': '{{ __('halls.full_day') }}'
                    };
                    return labels[slot] || '-';
                },

                getEventTypeLabel(type) {
                    const labels = {
                        'wedding': '{{ __('halls.wedding') }}',
                        'corporate': '{{ __('halls.corporate') }}',
                        'birthday': '{{ __('halls.birthday') }}',
                        'conference': '{{ __('halls.conference') }}',
                        'graduation': '{{ __('halls.graduation') }}',
                        'other': '{{ __('halls.other') }}'
                    };
                    return labels[type] || '-';
                },

                submitForm() {
                    if (this.submitting) {
                        console.log('Already submitting...');
                        return;
                    }

                    if (!this.agreeToTerms) {
                        alert('{{ __('halls.terms_agree') }}');
                        return;
                    }

                    if (!this.isAvailable) {
                        alert('{{ __('halls.date_not_available') }}');
                        return;
                    }

                    console.log('Submitting form...');
                    this.submitting = true;

                    document.getElementById('bookingForm').submit();
                }
            }
        }
    </script>


</body>

</html>
