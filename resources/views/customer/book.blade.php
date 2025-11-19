<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('halls.book_hall') }} - {{ is_array($hall->name) ? ($hall->name[app()->getLocale()] ?? $hall->name['en']) : $hall->name }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tajawal Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">

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
<body class="bg-gray-50" x-data="bookingWizard()">

    <!-- Top Navigation -->
    <nav class="sticky top-0 z-50 border-b border-gray-200 glass-morphism safe-area-top">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('customer.halls.show', $hall->slug) }}?lang={{ app()->getLocale() }}"
                   class="flex items-center gap-2 text-gray-700 transition hover:text-gray-900">
                    <svg class="w-6 h-6 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span class="font-medium">{{ __('halls.hall_details') }}</span>
                </a>

                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-10 h-10 shadow-lg bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl">
                        <span class="text-xl font-bold text-white">م</span>
                    </div>
                    <span class="hidden text-xl font-bold text-gray-800 sm:inline">Majalis</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Progress Steps (Desktop & Mobile) -->
    <div class="py-6 bg-white border-b border-gray-200">
        <div class="container px-4 mx-auto">
            <div class="max-w-3xl mx-auto">
                <div class="flex items-center justify-between">
                    <template x-for="(stepName, index) in steps" :key="index">
                        <div class="flex flex-col items-center flex-1 step-item"
                             :class="{'step-active': currentStep === index, 'step-completed': index < currentStep}">
                            <div class="flex items-center justify-center w-10 h-10 mb-2 text-sm font-bold transition-all duration-300 border-2 border-gray-300 rounded-full step-circle md:w-12 md:h-12 md:text-base">
                                <template x-if="index < currentStep">
                                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </template>
                                <template x-if="index >= currentStep">
                                    <span x-text="index + 1"></span>
                                </template>
                            </div>
                            <span class="text-xs font-medium text-center text-gray-600 md:text-sm" x-text="stepName"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <div class="container px-4 py-6 mx-auto md:py-8">
        <form @submit.prevent="submitBooking" class="max-w-5xl mx-auto">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                <!-- Main Form Area -->
                <div class="lg:col-span-2">

                    <!-- Step 1: Booking Details -->
                    <div x-show="currentStep === 0" class="slide-in">
                        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl md:p-8">
                            <h2 class="flex items-center gap-3 mb-6 text-2xl font-bold text-gray-900 md:text-3xl">
                                <div class="flex items-center justify-center w-10 h-10 bg-primary-100 rounded-xl">
                                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                {{ __('halls.booking_details') }}
                            </h2>

                            <div class="space-y-6">
                                <!-- Date Selection -->
                                <div>
                                    <label class="block mb-3 text-sm font-bold text-gray-700">
                                        {{ __('halls.event_date') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        x-model="formData.booking_date"
                                        @change="checkAvailability()"
                                        :min="minDate"
                                        required
                                        class="w-full px-4 text-base transition border-2 border-gray-200 h-14 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">

                                    <!-- Availability Status -->
                                    <div x-show="availabilityChecking" class="flex items-center gap-2 mt-3 text-sm text-gray-600">
                                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ __('halls.checking_availability') }}
                                    </div>

                                    <div x-show="availabilityMessage && !availabilityChecking"
                                         class="flex items-center gap-2 p-3 mt-3 text-sm rounded-lg"
                                         :class="isAvailable ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path x-show="isAvailable" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            <path x-show="!isAvailable" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span x-text="availabilityMessage"></span>
                                    </div>
                                </div>

                                <!-- Time Slot -->
                                <div>
                                    <label class="block mb-3 text-sm font-bold text-gray-700">
                                        {{ __('halls.time_slot') }} <span class="text-red-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <label class="relative cursor-pointer">
                                            <input type="radio" x-model="formData.time_slot" value="morning" @change="checkAvailability(); calculateTotal()" class="sr-only peer" required>
                                            <div class="h-full p-4 transition border-2 border-gray-200 rounded-xl hover:border-primary-300 peer-checked:border-primary-500 peer-checked:bg-primary-50">
                                                <div class="flex items-center gap-3">
                                                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                    </svg>
                                                    <div>
                                                        <div class="font-semibold text-gray-900">{{ __('halls.morning') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>

                                        <label class="relative cursor-pointer">
                                            <input type="radio" x-model="formData.time_slot" value="afternoon" @change="checkAvailability(); calculateTotal()" class="sr-only peer">
                                            <div class="h-full p-4 transition border-2 border-gray-200 rounded-xl hover:border-primary-300 peer-checked:border-primary-500 peer-checked:bg-primary-50">
                                                <div class="flex items-center gap-3">
                                                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                    </svg>
                                                    <div>
                                                        <div class="font-semibold text-gray-900">{{ __('halls.afternoon') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>

                                        <label class="relative cursor-pointer">
                                            <input type="radio" x-model="formData.time_slot" value="evening" @change="checkAvailability(); calculateTotal()" class="sr-only peer">
                                            <div class="h-full p-4 transition border-2 border-gray-200 rounded-xl hover:border-primary-300 peer-checked:border-primary-500 peer-checked:bg-primary-50">
                                                <div class="flex items-center gap-3">
                                                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                                                    </svg>
                                                    <div>
                                                        <div class="font-semibold text-gray-900">{{ __('halls.evening') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>

                                        <label class="relative cursor-pointer">
                                            <input type="radio" x-model="formData.time_slot" value="full_day" @change="checkAvailability(); calculateTotal()" class="sr-only peer">
                                            <div class="h-full p-4 transition border-2 border-gray-200 rounded-xl hover:border-primary-300 peer-checked:border-primary-500 peer-checked:bg-primary-50">
                                                <div class="flex items-center gap-3">
                                                    <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <div>
                                                        <div class="font-semibold text-gray-900">{{ __('halls.full_day') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Number of Guests -->
                                <div>
                                    <label class="block mb-3 text-sm font-bold text-gray-700">
                                        {{ __('halls.number_of_guests') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        x-model="formData.number_of_guests"
                                        min="{{ $hall->capacity_min }}"
                                        max="{{ $hall->capacity_max }}"
                                        required
                                        class="w-full px-4 text-base transition border-2 border-gray-200 h-14 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <p class="flex items-center gap-2 mt-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('halls.capacity_range') }}: {{ $hall->capacity_min }} - {{ $hall->capacity_max }} {{ __('halls.guests_count') }}
                                    </p>
                                </div>

                                <!-- Event Type -->
                                <div>
                                    <label class="block mb-3 text-sm font-bold text-gray-700">
                                        {{ __('halls.event_type') }}
                                    </label>
                                    <select
                                        x-model="formData.event_type"
                                        class="w-full px-4 text-base transition border-2 border-gray-200 h-14 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        <option value="">{{ __('halls.select_event_type') }}</option>
                                        <option value="wedding">{{ __('halls.wedding') }}</option>
                                        <option value="corporate">{{ __('halls.corporate') }}</option>
                                        <option value="birthday">{{ __('halls.birthday') }}</option>
                                        <option value="conference">{{ __('halls.conference') }}</option>
                                        <option value="graduation">{{ __('halls.graduation') }}</option>
                                        <option value="other">{{ __('halls.other') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Your Information -->
                    <div x-show="currentStep === 1" class="slide-in">
                        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl md:p-8">
                            <h2 class="flex items-center gap-3 mb-6 text-2xl font-bold text-gray-900 md:text-3xl">
                                <div class="flex items-center justify-center w-10 h-10 bg-primary-100 rounded-xl">
                                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                {{ __('halls.your_information') }}
                            </h2>

                            <div class="space-y-6">
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <!-- Full Name -->
                                    <div>
                                        <label class="block mb-3 text-sm font-bold text-gray-700">
                                            {{ __('halls.full_name') }} <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            x-model="formData.customer_name"
                                            required
                                            class="w-full px-4 text-base transition border-2 border-gray-200 h-14 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                            placeholder="{{ __('halls.full_name') }}">
                                    </div>

                                    <!-- Phone -->
                                    <div>
                                        <label class="block mb-3 text-sm font-bold text-gray-700">
                                            {{ __('halls.phone') }} <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="tel"
                                            x-model="formData.customer_phone"
                                            required
                                            class="w-full px-4 text-base transition border-2 border-gray-200 h-14 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                            placeholder="+968 XXXX XXXX">
                                    </div>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block mb-3 text-sm font-bold text-gray-700">
                                        {{ __('halls.email') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="email"
                                        x-model="formData.customer_email"
                                        required
                                        class="w-full px-4 text-base transition border-2 border-gray-200 h-14 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="example@email.com">
                                </div>

                                <!-- Additional Notes -->
                                <div>
                                    <label class="block mb-3 text-sm font-bold text-gray-700">
                                        {{ __('halls.additional_notes') }}
                                    </label>
                                    <textarea
                                        x-model="formData.customer_notes"
                                        rows="4"
                                        class="w-full px-4 py-3 text-base transition border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="{{ __('halls.special_requests') }}"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Extra Services -->
                        @if($hall->activeExtraServices->count() > 0)
                        <div class="p-6 mt-6 bg-white border border-gray-200 shadow-sm rounded-2xl md:p-8">
                            <h3 class="flex items-center gap-2 mb-4 text-xl font-bold text-gray-900">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                {{ __('halls.available_services') }}
                            </h3>

                            <div class="space-y-3">
                                @foreach($hall->activeExtraServices as $service)
                                <label class="flex items-start gap-4 p-4 transition border-2 border-gray-200 cursor-pointer rounded-xl hover:border-primary-300">
                                    <input
                                        type="checkbox"
                                        value="{{ $service->id }}"
                                        @change="toggleService({{ $service->id }}, {{ $service->price }})"
                                        class="w-5 h-5 mt-1 border-gray-300 rounded text-primary-600 focus:ring-primary-500">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">
                                            {{ is_array($service->name) ? ($service->name[app()->getLocale()] ?? $service->name['en']) : $service->name }}
                                        </div>
                                        @php
                                            $serviceDesc = is_array($service->description) ? ($service->description[app()->getLocale()] ?? $service->description['en'] ?? '') : $service->description;
                                        @endphp
                                        @if($serviceDesc)
                                            <div class="mt-1 text-sm text-gray-600">{{ $serviceDesc }}</div>
                                        @endif
                                        <div class="mt-1 text-xs text-gray-500">{{ $service->unit }}</div>
                                    </div>
                                    <div class="font-bold text-primary-600 whitespace-nowrap">
                                        {{ number_format($service->price, 3) }} OMR
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Step 3: Review & Confirm -->
                    <div x-show="currentStep === 2" class="slide-in">
                        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl md:p-8">
                            <h2 class="flex items-center gap-3 mb-6 text-2xl font-bold text-gray-900 md:text-3xl">
                                <div class="flex items-center justify-center w-10 h-10 bg-primary-100 rounded-xl">
                                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                {{ __('halls.review_confirm') }}
                            </h2>

                            <div class="space-y-6">
                                <!-- Booking Summary -->
                                <div class="p-6 bg-gray-50 rounded-xl">
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                                            <span class="text-sm text-gray-600">{{ __('halls.hall_details') }}</span>
                                            <span class="font-semibold text-gray-900">{{ is_array($hall->name) ? ($hall->name[app()->getLocale()] ?? $hall->name['en']) : $hall->name }}</span>
                                        </div>
                                        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                                            <span class="text-sm text-gray-600">{{ __('halls.event_date') }}</span>
                                            <span class="font-semibold text-gray-900" x-text="formData.booking_date || '-'"></span>
                                        </div>
                                        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                                            <span class="text-sm text-gray-600">{{ __('halls.time_slot') }}</span>
                                            <span class="font-semibold text-gray-900" x-text="getTimeSlotLabel(formData.time_slot)"></span>
                                        </div>
                                        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                                            <span class="text-sm text-gray-600">{{ __('halls.number_of_guests') }}</span>
                                            <span class="font-semibold text-gray-900" x-text="formData.number_of_guests"></span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">{{ __('halls.event_type') }}</span>
                                            <span class="font-semibold text-gray-900" x-text="getEventTypeLabel(formData.event_type)"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Customer Info -->
                                <div class="p-6 bg-gray-50 rounded-xl">
                                    <h4 class="mb-4 font-bold text-gray-900">{{ __('halls.your_information') }}</h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span class="text-gray-900" x-text="formData.customer_name"></span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-gray-900" x-text="formData.customer_email"></span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                            <span class="text-gray-900" x-text="formData.customer_phone"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Terms -->
                                <div class="flex items-start gap-3 p-4 border border-blue-200 bg-blue-50 rounded-xl">
                                    <input
                                        type="checkbox"
                                        x-model="agreeToTerms"
                                        required
                                        class="w-5 h-5 mt-1 border-gray-300 rounded text-primary-600 focus:ring-primary-500">
                                    <label class="text-sm text-gray-700">
                                        {{ __('halls.terms_agree') }}
                                        <a href="#" class="font-medium text-primary-600 hover:text-primary-700">{{ __('halls.terms_conditions') }}</a>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex items-center gap-3 mt-6">
                        <button
                            type="button"
                            @click="previousStep"
                            x-show="currentStep > 0"
                            class="flex items-center justify-center flex-1 gap-2 px-6 font-semibold text-gray-700 transition border-2 border-gray-300 h-14 rounded-xl hover:bg-gray-50">
                            <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            {{ __('halls.previous') }}
                        </button>

                        <button
                            type="button"
                            @click="nextStep"
                            x-show="currentStep < 2"
                            :disabled="!canProceed()"
                            :class="canProceed() ? 'bg-primary-600 hover:bg-primary-700' : 'bg-gray-300 cursor-not-allowed'"
                            class="flex items-center justify-center flex-1 gap-2 px-6 font-bold text-white transition shadow-lg h-14 rounded-xl">
                            {{ __('halls.next') }}
                            <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>

                        <button
                            type="submit"
                            x-show="currentStep === 2"
                            :disabled="!agreeToTerms || !isAvailable"
                            :class="(agreeToTerms && isAvailable) ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-300 cursor-not-allowed'"
                            class="flex items-center justify-center flex-1 gap-2 px-6 font-bold text-white transition shadow-lg h-14 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('halls.confirm_booking') }}
                        </button>
                    </div>
                </div>

                <!-- Sticky Summary Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky p-6 bg-white border border-gray-200 shadow-lg top-24 rounded-2xl">
                        <h3 class="mb-4 text-lg font-bold text-gray-900">{{ __('halls.booking_summary') }}</h3>

                        <!-- Hall Thumbnail -->
                        <div class="mb-4">
                            <div class="h-32 overflow-hidden bg-gray-200 rounded-xl">
                                @if($hall->featured_image)
                                    <img src="{{ asset('storage/' . $hall->featured_image) }}" class="object-cover w-full h-full">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-primary-100 to-primary-200"></div>
                                @endif
                            </div>
                            <h4 class="mt-3 font-semibold text-gray-900">{{ is_array($hall->name) ? ($hall->name[app()->getLocale()] ?? $hall->name['en']) : $hall->name }}</h4>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="py-4 space-y-3 border-gray-200 border-y">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">{{ __('halls.hall_price') }}</span>
                                <span class="font-semibold text-gray-900">{{ number_format($hall->price_per_slot, 3) }} OMR</span>
                            </div>
                            <div x-show="servicesTotal > 0" class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">{{ __('halls.extra_services_total') }}</span>
                                <span class="font-semibold text-gray-900" x-text="servicesTotal.toFixed(3) + ' OMR'"></span>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="pt-4 mt-4 border-t-2 border-gray-300">
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-gray-900">{{ __('halls.total') }}</span>
                                <span class="text-2xl font-bold text-primary-600" x-text="total.toFixed(3) + ' OMR'"></span>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="pt-6 mt-6 border-t border-gray-200">
                            <p class="flex items-center gap-2 text-xs text-gray-500">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                {{ __('halls.terms_agree') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function bookingWizard() {
            return {
                currentStep: 0,
                steps: @json([
                    __('halls.booking_details'),
                    __('halls.your_information'),
                    __('halls.review_confirm')
                ]),
                formData: {
                    booking_date: '',
                    time_slot: '',
                    number_of_guests: {{ $hall->capacity_min }},
                    event_type: '',
                    customer_name: '{{ old("customer_name", Auth::user()->name ?? "") }}',
                    customer_email: '{{ old("customer_email", Auth::user()->email ?? "") }}',
                    customer_phone: '{{ old("customer_phone", Auth::user()->phone ?? "") }}',
                    customer_notes: '',
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

                async checkAvailability() {
                    if (!this.formData.booking_date || !this.formData.time_slot) {
                        this.availabilityMessage = '';
                        return;
                    }

                    this.availabilityChecking = true;
                    this.availabilityMessage = '';

                    try {
                        const response = await fetch('{{ route("customer.check-availability") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
                    } catch (error) {
                        console.error('Error:', error);
                        this.availabilityMessage = '{{ __("halls.checking_availability") }}';
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
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                previousStep() {
                    if (this.currentStep > 0) {
                        this.currentStep--;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                getTimeSlotLabel(slot) {
                    const labels = {
                        'morning': '{{ __("halls.morning") }}',
                        'afternoon': '{{ __("halls.afternoon") }}',
                        'evening': '{{ __("halls.evening") }}',
                        'full_day': '{{ __("halls.full_day") }}'
                    };
                    return labels[slot] || '-';
                },

                getEventTypeLabel(type) {
                    const labels = {
                        'wedding': '{{ __("halls.wedding") }}',
                        'corporate': '{{ __("halls.corporate") }}',
                        'birthday': '{{ __("halls.birthday") }}',
                        'conference': '{{ __("halls.conference") }}',
                        'graduation': '{{ __("halls.graduation") }}',
                        'other': '{{ __("halls.other") }}'
                    };
                    return labels[type] || '-';
                },

                submitBooking() {
                    if (!this.agreeToTerms || !this.isAvailable) {
                        alert('{{ __("halls.terms_agree") }}');
                        return;
                    }

                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("customer.booking.store", $hall->slug) }}';

                    // Add CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
                    form.appendChild(csrfInput);

                    // Add form data
                    Object.keys(this.formData).forEach(key => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = this.formData[key];
                        form.appendChild(input);
                    });

                    // Add selected services
                    this.selectedServices.forEach(serviceId => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'services[]';
                        input.value = serviceId;
                        form.appendChild(input);
                    });

                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }
    </script>
</body>
</html>
