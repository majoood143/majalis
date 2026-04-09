<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('hall-owner.registration.success.page_title') }} - {{ config('app.name', 'Majalis') }}</title>
    <link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/webp">
    <style>
        @font-face {
            font-family: 'Tajawal';
            src: url('{{ asset("fonts/Tajawal-Regular.ttf") }}') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Tajawal';
            src: url('{{ asset("fonts/Tajawal-Medium.ttf") }}') format('truetype');
            font-weight: 500;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Tajawal';
            src: url('{{ asset("fonts/Tajawal-Bold.ttf") }}') format('truetype');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }
        *, *::before, *::after { font-family: 'Tajawal', sans-serif !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">

    {{-- Top Bar --}}
    <div class="flex items-center justify-between px-4 py-3 bg-white border-b border-gray-200">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <img src="{{ asset('images/logo.webp') }}" alt="Majalis" class="w-8 h-8 rounded-xl">
            <span class="text-lg font-bold text-gray-800">
                {{ app()->getLocale() === 'ar' ? 'مجالس' : 'Majalis' }}
            </span>
        </a>
    </div>

    <div class="flex items-center justify-center min-h-screen px-4 py-12">
        <div class="w-full max-w-md text-center">

            {{-- Success Icon --}}
            <div class="flex justify-center mb-6">
                <div class="flex items-center justify-center w-24 h-24 bg-green-100 rounded-full">
                    <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <h1 class="mb-3 text-2xl font-bold text-gray-900">
                {{ __('hall-owner.registration.success.title') }}
            </h1>
            <p class="mb-6 leading-relaxed text-gray-500">
                {{ __('hall-owner.registration.success.message') }}
            </p>

            {{-- Steps Info --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-left {{ app()->getLocale() === 'ar' ? 'text-right' : '' }} mb-6">
                <h3 class="mb-4 text-sm font-semibold text-gray-700">{{ __('hall-owner.registration.success.next_steps_title') }}</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-xs font-bold text-indigo-600">1</span>
                        </div>
                        <p class="text-sm text-gray-600">{{ __('hall-owner.registration.success.step_1') }}</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-xs font-bold text-indigo-600">2</span>
                        </div>
                        <p class="text-sm text-gray-600">{{ __('hall-owner.registration.success.step_2') }}</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-xs font-bold text-indigo-600">3</span>
                        </div>
                        <p class="text-sm text-gray-600">{{ __('hall-owner.registration.success.step_3') }}</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white transition-colors rounded-lg hover:bg-gray-700" style="background-color: #B9916D">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                {{ __('hall-owner.registration.success.back_home') }}
            </a>
        </div>
    </div>

    @include('layouts.footer')

</body>
</html>
