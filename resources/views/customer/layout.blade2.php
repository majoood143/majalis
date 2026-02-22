{{--
|--------------------------------------------------------------------------
| Customer Dashboard Layout
|--------------------------------------------------------------------------
|
| Main layout for authenticated customer pages (dashboard, bookings, profile).
| Extends: Used by @extends('customer.layout')
|
| FIX APPLIED:
|   ✅ Added language switcher (AR/EN toggle) in desktop & mobile nav
|   ✅ Replaced all hardcoded English nav text with __() translation keys
|   ✅ RTL-aware spacing (space-x-reverse for Arabic)
|   ✅ Mobile menu includes language switcher link
|   ✅ RTL text-alignment for logout button in mobile menu
|
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Hall Booking System')</title>
    <link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/webp">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('customer.halls.index') }}" class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                         <img src="{{ asset('images/logo.webp') }}" alt="Majalis Logo" class="w-8 h-8">
                        <span class="text-xl font-bold text-gray-900">{{ __('guest.majalis') }}</span>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="items-center hidden space-x-8 md:flex {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <!-- Browse Halls Link -->
                    <a href="{{ route('customer.halls.index') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium {{ request()->routeIs('customer.halls.*') ? 'text-indigo-600 font-semibold' : '' }}">
                        {{ __('dashboard.browse_halls') }}
                    </a>

                    @auth
                        <!-- Dashboard Link -->
                        <a href="{{ route('customer.dashboard') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium {{ request()->routeIs('customer.dashboard') ? 'text-indigo-600 font-semibold' : '' }}">
                            {{ __('dashboard.nav_dashboard') }}
                        </a>

                        <!-- My Bookings Link -->
                        <a href="{{ route('customer.bookings') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium {{ request()->routeIs('customer.bookings') || request()->routeIs('customer.booking.*') ? 'text-indigo-600 font-semibold' : '' }}">
                            {{ __('dashboard.my_bookings') }}
                        </a>

                        <!-- User Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }} text-gray-700 hover:text-indigo-600">
                                <!-- User Avatar Initial -->
                                <div class="flex items-center justify-center w-8 h-8 font-medium text-white bg-indigo-600 rounded-full">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                                <!-- Chevron Icon -->
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false" x-cloak
                                class="absolute {{ app()->getLocale() === 'ar' ? 'left-0' : 'right-0' }} w-48 py-1 mt-2 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                                <a href="{{ route('customer.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    {{ __('dashboard.my_profile') }}
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full px-4 py-2 text-sm {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }} text-gray-700 hover:bg-gray-100">
                                        {{ __('dashboard.logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Guest: Login Link -->
                        <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600">
                            {{ __('dashboard.login') }}
                        </a>
                        <!-- Guest: Register Link -->
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                            {{ __('dashboard.register') }}
                        </a>
                    @endauth

                    {{-- ============================================================
                         LANGUAGE SWITCHER (Desktop)
                         Toggles between Arabic ↔ English via ?lang= query parameter.
                         Matches the pattern used in layouts/customer.blade.php
                         and customer/halls/index.blade.php.
                         ============================================================ --}}
                    <a href="{{ request()->fullUrlWithQuery(['lang' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}"
                        class="flex items-center gap-2 px-3 py-2 transition rounded-lg hover:bg-gray-100">
                        <!-- Globe/Language Icon -->
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129">
                            </path>
                        </svg>
                        <!-- Show opposite language label -->
                        <span class="text-sm font-medium text-gray-700">
                            {{ app()->getLocale() === 'ar' ? 'EN' : 'ع' }}
                        </span>
                    </a>
                </div>

                <!-- Mobile: Language Switcher + Hamburger Menu -->
                <div class="flex items-center gap-2 md:hidden">
                    {{-- Language Switcher (Mobile - always visible) --}}
                    <a href="{{ request()->fullUrlWithQuery(['lang' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}"
                        class="flex items-center gap-1 px-2 py-2 transition rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129">
                            </path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">
                            {{ app()->getLocale() === 'ar' ? 'EN' : 'ع' }}
                        </span>
                    </a>

                    {{-- Hamburger Menu Button --}}
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-700 hover:text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu (Slide-down) -->
        <div x-show="mobileMenuOpen" x-cloak class="md:hidden" x-data="{ mobileMenuOpen: false }">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <!-- Browse Halls -->
                <a href="{{ route('customer.halls.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 rounded-md hover:text-indigo-600 hover:bg-gray-50">
                    {{ __('dashboard.browse_halls') }}
                </a>

                @auth
                    <!-- Dashboard -->
                    <a href="{{ route('customer.dashboard') }}" class="block px-3 py-2 text-base font-medium text-gray-700 rounded-md hover:text-indigo-600 hover:bg-gray-50">
                        {{ __('dashboard.nav_dashboard') }}
                    </a>

                    <!-- My Bookings -->
                    <a href="{{ route('customer.bookings') }}" class="block px-3 py-2 text-base font-medium text-gray-700 rounded-md hover:text-indigo-600 hover:bg-gray-50">
                        {{ __('dashboard.my_bookings') }}
                    </a>

                    <!-- Profile -->
                    <a href="{{ route('customer.profile') }}" class="block px-3 py-2 text-base font-medium text-gray-700 rounded-md hover:text-indigo-600 hover:bg-gray-50">
                        {{ __('dashboard.my_profile') }}
                    </a>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full px-3 py-2 text-base font-medium {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }} text-gray-700 rounded-md hover:text-indigo-600 hover:bg-gray-50">
                            {{ __('dashboard.logout') }}
                        </button>
                    </form>
                @else
                    <!-- Guest: Login -->
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-base font-medium text-gray-700 rounded-md hover:text-indigo-600 hover:bg-gray-50">
                        {{ __('dashboard.login') }}
                    </a>

                    <!-- Guest: Register -->
                    <a href="{{ route('register') }}" class="block px-3 py-2 text-base font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                        {{ __('dashboard.register') }}
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="px-4 py-3 mx-auto mt-4 text-green-800 bg-green-100 border border-green-200 rounded-lg max-w-7xl sm:px-6 lg:px-8">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="px-4 py-3 mx-auto mt-4 text-red-800 bg-red-100 border border-red-200 rounded-lg max-w-7xl sm:px-6 lg:px-8">
            {{ session('error') }}
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="py-8 mt-12 bg-white border-t border-gray-200">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.webp') }}" alt="Majalis Logo" class="w-8 h-8">
                    <span class="font-bold text-gray-800">{{ __('guest.majalis') }}</span>
                </div>
                <p class="text-sm text-gray-500">
                    © {{ date('Y') }} {{ __('guest.majalis') }}. {{ __('guest.rights_reserved') }}
                </p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
