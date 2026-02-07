<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ __('halls.browse_halls') }} - Majalis</title>

    <link rel="icon" type="image/ico" href="{{ asset('images/favicon.ico') }}" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

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

        #map {
            height: calc(100vh - 160px);
            min-height: 400px;
            position: relative;
            z-index: 1;
        }

        /* Control Leaflet layers */
        .leaflet-container {
            position: relative !important;
            z-index: 1 !important;
        }

        [x-cloak] {
            display: none !important;
        }

        .glass-morphism {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .hall-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .hall-card:active {
            transform: scale(0.98);
        }

        @media (min-width: 768px) {
            .hall-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            }
        }

        /* RTL specific styles */
        [dir="rtl"] .leaflet-top .leaflet-bottom {
            right: auto;
            left: 10px;
            z-index: 10 !important;
        }

        .leaflet-pane {
            z-index: 5 !important;
        }

        /* Ensure other elements stay above map */
        .bottom-nav {
            z-index: 50;
        }

        .filter-modal {
            z-index: 1000;
        }

        nav.sticky.top-0 {
            z-index: 800;
        }

        /* For the search bar */
        .top-16 {
            z-index: 40;
        }

        [dir="rtl"] .leaflet-right {
            left: 0;
            right: auto;
        }

        /* Bottom navigation for mobile */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e5e7eb;
            z-index: 50;
            padding-bottom: env(safe-area-inset-bottom);
        }

        /* Mobile filter modal - improved */
        .filter-modal {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: flex;
            align-items: flex-end;
        }

        .filter-content {
            background: white;
            width: 100%;
            max-height: 85vh;
            border-radius: 24px 24px 0 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .filter-header {
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
            padding: 16px 20px;
            border-bottom: 1px solid #e5e7eb;
            flex-shrink: 0;
        }

        .filter-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            padding-bottom: 100px;
            /* Space for sticky footer */
            -webkit-overflow-scrolling: touch;
        }

        .filter-footer {
            position: sticky;
            bottom: 0;
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 16px 20px;
            padding-bottom: calc(16px + env(safe-area-inset-bottom));
            flex-shrink: 0;
            box-shadow: 0 -4px 6px -1px rgb(0 0 0 / 0.1);
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Input focus styles */
        input:focus,
        select:focus {
            outline: none;
        }

        /* Active states for better mobile feedback */
        button:active,
        a:active {
            opacity: 0.7;
        }

        /* Safe area for notched phones */
        .safe-area-top {
            padding-top: env(safe-area-inset-top);
        }

        /* Better touch targets */
        button,
        a,
        input,
        select {
            min-height: 44px;
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
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50" x-data="hallsApp()">

    <!-- Top Navigation -->
    <nav class="sticky top-0 z-50 border-b border-gray-200 glass-morphism safe-area-top">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="/"
                    class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <div
                        class="flex items-center justify-center w-10 h-10 shadow-lg bg-gradient-to-br rounded-xl">
                        {{-- <span class="text-xl font-bold text-white">م</span> --}}
                         <img src="{{ asset('images/logo.webp') }}" alt="Majalis Logo" class="w-8 h-8">
                    </div>
                    <span class="hidden text-xl font-bold text-gray-800 sm:block">{{ __('guest.majalis') }}</span>
                </a>

                <!-- Language Switcher -->
                <div class="flex items-center gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['lang' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}"
                        class="flex items-center gap-2 px-3 py-2 transition rounded-lg hover:bg-gray-100 active:bg-gray-200">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129">
                            </path>
                        </svg>
                        <span class="hidden text-sm font-medium text-gray-700 sm:inline">
                            {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
                        </span>
                    </a>

                    <!-- Login Button (Desktop) -->
                    <a href="{{ route('login') }}"
                        class="items-center hidden gap-2 px-4 py-2 text-white transition rounded-lg shadow-sm md:flex bg-primary-600 hover:bg-primary-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-sm font-medium">{{ __('halls.login') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="text-white bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800">
        <div class="container px-4 py-8 mx-auto md:py-12">
            <h1 class="mb-2 text-2xl font-bold md:text-4xl">{{ __('halls.browse_halls') }}</h1>
            <p class="text-sm text-primary-100 md:text-lg">{{ __('halls.find_perfect_venue') }}</p>
        </div>
    </div>

    <!-- Search Bar (Mobile Optimized) -->
    <div class="sticky z-40 bg-white border-b border-gray-200 shadow-md top-16">
        <div class="container px-4 py-3 mx-auto">
            <form action="{{ route('customer.halls.index') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="lang" value="{{ app()->getLocale() }}">

                <!-- Preserve existing filters -->
                @foreach (request()->except(['search', 'lang', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach

                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('halls.search_placeholder') }}"
                        class="w-full h-12 px-4 {{ app()->getLocale() === 'ar' ? 'pr-12' : 'pl-12' }} rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                    <svg class="w-5 h-5 text-gray-400 absolute top-1/2 -translate-y-1/2 {{ app()->getLocale() === 'ar' ? 'right-4' : 'left-4' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <!-- Search Button (Desktop) -->
                <button type="submit"
                    class="items-center hidden h-12 gap-2 px-6 font-medium text-white transition shadow-sm md:flex bg-primary-600 rounded-xl hover:bg-primary-700">
                    <span>{{ __('halls.search') }}</span>
                </button>

                <!-- Filter Button (Mobile) -->
                <button type="button" @click="showFilters = true"
                    class="relative flex items-center justify-center w-12 h-12 text-white transition shadow-sm md:hidden bg-primary-600 rounded-xl hover:bg-primary-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                        </path>
                    </svg>
                    <span x-show="activeFiltersCount > 0"
                        class="absolute -top-2 {{ app()->getLocale() === 'ar' ? '-left-2' : '-right-2' }} bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center shadow-lg"
                        x-text="activeFiltersCount"></span>
                </button>
            </form>
        </div>
    </div>

    <div class="container px-4 py-6 mx-auto">
        <div class="flex gap-6">
            <!-- Desktop Filters Sidebar -->
            <aside class="hidden md:block w-80 shrink-0">
                <div class="sticky space-y-4 top-32">
                    <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800">{{ __('halls.filters') }}</h3>
                            <a href="{{ route('customer.halls.index') }}?lang={{ app()->getLocale() }}"
                                class="text-sm font-medium text-primary-600 hover:text-primary-700">
                                {{ __('halls.clear_filters') }}
                            </a>
                        </div>

                        <form action="{{ route('customer.halls.index') }}" method="GET" class="space-y-5">
                            <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">

                            <!-- Region -->
                            <div>
                                <label
                                    class="block mb-2 text-sm font-semibold text-gray-700">{{ __('halls.region') }}</label>
                                <select name="region_id" onchange="this.form.submit()"
                                    class="w-full h-12 px-4 transition border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <option value="">{{ __('halls.all_regions') }}</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region->id }}"
                                            {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                            {{ is_array($region->name) ? $region->name[app()->getLocale()] ?? $region->name['en'] : $region->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- City -->
                            @if (request('region_id') && $cities->count() > 0)
                                <div>
                                    <label
                                        class="block mb-2 text-sm font-semibold text-gray-700">{{ __('halls.city') }}</label>
                                    <select name="city_id" onchange="this.form.submit()"
                                        class="w-full h-12 px-4 transition border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        <option value="">{{ __('halls.all_cities') }}</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}"
                                                {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                                {{ is_array($city->name) ? $city->name[app()->getLocale()] ?? $city->name['en'] : $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <!-- Capacity -->
                            <div>
                                <label
                                    class="block mb-2 text-sm font-semibold text-gray-700">{{ __('halls.guests') }}</label>
                                <input type="number" name="capacity" value="{{ request('capacity') }}"
                                    min="1" placeholder="{{ __('halls.guests') }}"
                                    class="w-full h-12 px-4 transition border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>

                            <!-- Price Range -->
                            <div>
                                <label
                                    class="block mb-2 text-sm font-semibold text-gray-700">{{ __('halls.price_range') }}</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <input type="number" name="min_price" value="{{ request('min_price') }}"
                                        step="0.001" placeholder="{{ __('halls.min_price') }}"
                                        class="h-12 px-4 transition border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <input type="number" name="max_price" value="{{ request('max_price') }}"
                                        step="0.001" placeholder="{{ __('halls.max_price') }}"
                                        class="h-12 px-4 transition border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                                @if (isset($stats))
                                    <p class="mt-2 text-xs text-gray-500">
                                        {{ __('halls.starting_from') }} {{ number_format($stats['min_price'], 3) }} -
                                        {{ number_format($stats['max_price'], 3) }} OMR
                                    </p>
                                @endif
                            </div>

                            <button type="submit"
                                class="w-full h-12 font-semibold text-white transition shadow-sm bg-primary-600 rounded-xl hover:bg-primary-700">
                                {{ __('halls.apply') }}
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 min-w-0">
                <!-- View Toggle & Sort -->
                <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-600">
                            <strong class="text-primary-600">{{ $halls->total() }}</strong>
                            {{ __('halls.halls_found') }}
                        </span>

                        <!-- View Mode (Desktop) -->
                        <div class="items-center hidden gap-1 p-1 bg-gray-100 md:flex rounded-xl">
                            <a href="{{ request()->fullUrlWithQuery(['view_mode' => null]) }}"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ !request('view_mode') ? 'bg-white shadow-sm text-primary-600' : 'text-gray-600 hover:text-gray-900' }}">
                                {{ __('halls.view_all') }}
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['view_mode' => 'by_region']) }}"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('view_mode') === 'by_region' ? 'bg-white shadow-sm text-primary-600' : 'text-gray-600 hover:text-gray-900' }}">
                                {{ __('halls.view_by_region') }}
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Sort -->
                        <form action="{{ route('customer.halls.index') }}" method="GET" class="inline">
                            <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                            @foreach (request()->except(['sort', 'lang', 'page']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach

                            <select name="sort" onchange="this.form.submit()"
                                class="h-10 px-3 pr-8 text-sm font-medium border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                                <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>
                                    {{ __('halls.latest') }}</option>
                                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>
                                    {{ __('halls.name_az') }}</option>
                                <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>
                                    {{ __('halls.price_low_high') }}</option>
                                <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>
                                    {{ __('halls.price_high_low') }}</option>
                                <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>
                                    {{ __('halls.highest_rated') }}</option>
                                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>
                                    {{ __('halls.most_popular') }}</option>
                            </select>
                        </form>

                        <!-- Map Toggle (Desktop) -->
                        <div class="items-center hidden gap-1 p-1 bg-gray-100 md:flex rounded-xl">
                            <button @click="view = 'grid'" :class="view === 'grid' ? 'bg-white shadow-sm' : ''"
                                class="p-2 transition rounded-lg">
                                <svg class="w-5 h-5"
                                    :class="view === 'grid' ? 'text-primary-600' : 'text-gray-600'" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                    </path>
                                </svg>
                            </button>
                            <button @click="view = 'map'" :class="view === 'map' ? 'bg-white shadow-sm' : ''"
                                class="p-2 transition rounded-lg">
                                <svg class="w-5 h-5"
                                    :class="view === 'map' ? 'text-primary-600' : 'text-gray-600'" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Grid View -->
                <div x-show="view === 'grid'" x-cloak>
                    @if (request('view_mode') === 'by_region')
                        <!-- Grouped by Region -->
                        @php
                            $hasResults = false;
                        @endphp
                        @foreach ($regions as $region)
                            @php
                                $regionHalls = $halls->filter(function ($hall) use ($region) {
                                    return $hall->city->region_id == $region->id;
                                });
                            @endphp

                            @if ($regionHalls->count() > 0)
                                @php $hasResults = true; @endphp
                                <div class="mb-12">
                                    <div
                                        class="flex items-center justify-between pb-3 mb-6 border-b-2 border-primary-100">
                                        <h2 class="text-2xl font-bold text-gray-800 md:text-3xl">
                                            {{ is_array($region->name) ? $region->name[app()->getLocale()] ?? $region->name['en'] : $region->name }}
                                        </h2>
                                        <span
                                            class="px-3 py-1 text-sm font-medium text-gray-600 bg-gray-100 rounded-full">
                                            {{ $regionHalls->count() }} {{ __('halls.halls_in_region') }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 md:gap-6">
                                        @foreach ($regionHalls as $hall)
                                            @include('customer.halls.partials.hall-card', [
                                                'hall' => $hall,
                                            ])
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        @if (!$hasResults)
                            <div class="py-20 text-center">
                                <svg class="w-24 h-24 mx-auto mb-4 text-gray-300" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                                <h3 class="mb-2 text-xl font-semibold text-gray-700">{{ __('halls.no_halls_found') }}
                                </h3>
                                <p class="mb-4 text-gray-500">{{ __('halls.adjust_filters') }}</p>
                                <a href="{{ route('customer.halls.index') }}?lang={{ app()->getLocale() }}"
                                    class="inline-block px-6 py-3 font-medium text-white transition shadow-sm bg-primary-600 rounded-xl hover:bg-primary-700">
                                    {{ __('halls.clear_filters') }}
                                </a>
                            </div>
                        @endif
                    @else
                        <!-- Regular Grid -->
                        <div class="grid grid-cols-1 gap-4 pb-24 sm:grid-cols-2 lg:grid-cols-3 md:gap-6 md:pb-8">
                            @forelse($halls as $hall)
                                @include('customer.halls.partials.hall-card', ['hall' => $hall])
                            @empty
                                <div class="py-20 text-center col-span-full">
                                    <svg class="w-24 h-24 mx-auto mb-4 text-gray-300" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                    <h3 class="mb-2 text-xl font-semibold text-gray-700">
                                        {{ __('halls.no_halls_found') }}</h3>
                                    <p class="mb-4 text-gray-500">{{ __('halls.adjust_filters') }}</p>
                                    <a href="{{ route('customer.halls.index') }}?lang={{ app()->getLocale() }}"
                                        class="inline-block px-6 py-3 font-medium text-white transition shadow-sm bg-primary-600 rounded-xl hover:bg-primary-700">
                                        {{ __('halls.clear_filters') }}
                                    </a>
                                </div>
                            @endforelse
                        </div>

                        <!-- Pagination -->
                        @if ($halls->hasPages())
                            <div class="pb-24 mt-8 md:pb-0">
                                {{ $halls->links() }}
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Map View-->
                <div x-show="view === 'map'" x-cloak class="pb-24 md:pb-0">
                    <div id="map" class="border-2 border-gray-200 shadow-lg rounded-2xl"></div>
                    <div class="p-4 mt-4 border-2 border-blue-200 bg-blue-50 rounded-xl">
                        <p class="text-sm font-medium text-blue-800">
                            <svg class="w-5 h-5 inline {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <strong>{{ $mapHalls->count() }}</strong> {{ __('halls.halls_on_map') }}
                        </p>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile Filter Modal - ENHANCED -->
    <div x-show="showFilters" x-cloak @click.self="showFilters = false" class="md:hidden filter-modal"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        style="background: rgba(0, 0, 0, 0.6);">

        <div class="filter-content" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full">

            <!-- Header - Sticky -->
            <div class="filter-header">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">{{ __('halls.filters') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            <span x-show="activeFiltersCount > 0">
                                <span x-text="activeFiltersCount"></span> فلتر نشط
                            </span>
                            <span x-show="activeFiltersCount === 0">
                                لا توجد فلاتر نشطة
                            </span>
                        </p>
                    </div>
                    <button @click="showFilters = false"
                        class="flex items-center justify-center w-10 h-10 transition rounded-full hover:bg-gray-100">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Body - Scrollable -->
            <form action="{{ route('customer.halls.index') }}" method="GET">
                <div class="filter-body">
                    <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                    <input type="hidden" name="search" value="{{ request('search') }}">

                    <div class="space-y-6">
                        <!-- Region -->
                        <div>
                            <label class="block mb-3 text-sm font-bold text-gray-700">
                                <svg class="w-5 h-5 inline {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                {{ __('halls.region') }}
                            </label>
                            <select name="region_id" x-model="mobileFilters.region_id" @change="loadCitiesMobile()"
                                class="w-full px-4 text-base transition border-2 border-gray-200 h-14 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="">{{ __('halls.all_regions') }}</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region->id }}">
                                        {{ is_array($region->name) ? $region->name[app()->getLocale()] ?? $region->name['en'] : $region->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- City -->
                        <div x-show="mobileCities.length > 0">
                            <label class="block mb-3 text-sm font-bold text-gray-700">
                                <svg class="w-5 h-5 inline {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                                {{ __('halls.city') }}
                            </label>
                            <select name="city_id" x-model="mobileFilters.city_id"
                                class="w-full px-4 text-base transition border-2 border-gray-200 h-14 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="">{{ __('halls.all_cities') }}</option>
                                <template x-for="city in mobileCities" :key="city.id">
                                    <option :value="city.id"
                                        x-text="typeof city.name === 'object' ? (city.name['{{ app()->getLocale() }}'] || city.name.en) : city.name">
                                    </option>
                                </template>
                            </select>
                        </div>

                        <!-- Capacity -->
                        <div>
                            <label class="block mb-3 text-sm font-bold text-gray-700">
                                <svg class="w-5 h-5 inline {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                {{ __('halls.guests') }}
                            </label>
                            <input type="number" name="capacity" value="{{ request('capacity') }}" min="1"
                                placeholder="أدخل عدد الضيوف"
                                class="w-full px-4 text-base transition border-2 border-gray-200 h-14 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label class="block mb-3 text-sm font-bold text-gray-700">
                                <svg class="w-5 h-5 inline {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                {{ __('halls.price_range') }}
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <input type="number" name="min_price" value="{{ request('min_price') }}"
                                        step="0.001" placeholder="الحد الأدنى"
                                        class="w-full px-4 text-base transition border-2 border-gray-200 h-14 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                                <div>
                                    <input type="number" name="max_price" value="{{ request('max_price') }}"
                                        step="0.001" placeholder="الحد الأعلى"
                                        class="w-full px-4 text-base transition border-2 border-gray-200 h-14 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                            </div>
                            @if (isset($stats))
                                <p class="flex items-center mt-3 text-sm text-gray-500">
                                    <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('halls.starting_from') }} {{ number_format($stats['min_price'], 3) }} -
                                    {{ number_format($stats['max_price'], 3) }} OMR
                                </p>
                            @endif
                        </div>

                        <!-- View Mode -->
                        <div>
                            <label class="block mb-3 text-sm font-bold text-gray-700">
                                <svg class="w-5 h-5 inline {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                                طريقة العرض
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label
                                    class="flex items-center justify-center px-4 transition border-2 cursor-pointer h-14 rounded-xl"
                                    :class="!mobileFilters.view_mode ? 'border-primary-500 bg-primary-50 text-primary-700' :
                                        'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" name="view_mode" value=""
                                        x-model="mobileFilters.view_mode" class="hidden">
                                    <span class="font-medium">{{ __('halls.view_all') }}</span>
                                </label>
                                <label
                                    class="flex items-center justify-center px-4 transition border-2 cursor-pointer h-14 rounded-xl"
                                    :class="mobileFilters.view_mode === 'by_region' ?
                                        'border-primary-500 bg-primary-50 text-primary-700' :
                                        'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" name="view_mode" value="by_region"
                                        x-model="mobileFilters.view_mode" class="hidden">
                                    <span class="font-medium">{{ __('halls.view_by_region') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer - Sticky -->
                <div class="filter-footer">
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('customer.halls.index') }}?lang={{ app()->getLocale() }}"
                            class="flex items-center justify-center font-semibold text-gray-700 transition border-2 border-gray-300 h-14 rounded-xl hover:bg-gray-50">
                            <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            {{ __('halls.clear_filters') }}
                        </a>
                        <button type="submit"
                            class="flex items-center justify-center font-bold text-white transition shadow-lg h-14 bg-primary-600 rounded-xl hover:bg-primary-700">
                            <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('halls.apply') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bottom Navigation (Mobile) -->
    <nav class="bottom-nav md:hidden">
        <div class="grid grid-cols-3 gap-1 p-2">
            <button @click="view = 'grid'" :class="view === 'grid' ? 'text-primary-600' : 'text-gray-600'"
                class="flex flex-col items-center gap-1 p-2 transition rounded-lg active:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                    </path>
                </svg>
                <span class="text-xs font-medium">القائمة</span>
            </button>

            <button @click="view = 'map'" :class="view === 'map' ? 'text-primary-600' : 'text-gray-600'"
                class="flex flex-col items-center gap-1 p-2 transition rounded-lg active:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                    </path>
                </svg>
                <span class="text-xs font-medium">الخريطة</span>
            </button>

            <button @click="showFilters = true"
                class="relative flex flex-col items-center gap-1 p-2 text-gray-600 transition rounded-lg active:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                    </path>
                </svg>
                <span class="text-xs font-medium">الفلاتر</span>
                <span x-show="activeFiltersCount > 0"
                    class="absolute top-0 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full shadow-lg right-2"
                    x-text="activeFiltersCount"></span>
            </button>
        </div>
    </nav>

    {{-- <footer class="text-gray-300 bg-gray-900">
        <div class="px-4 py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                <!-- Company Info -->
                <div class="col-span-1">
                    <div class="flex items-center mb-4 space-x-2 ">

                         <img src="{{ asset('images/logo.webp') }}" alt="Majalis Logo" class="w-8 h-8 rounded-xl">
                        <span class="text-xl font-bold text-white">{{ __('guest.majalis') }}</span>
                    </div>
                    <p class="text-sm text-gray-400">{{ __('halls.find_perfect_venue') }}</p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="mb-4 font-semibold text-white">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('customer.halls.index') }}" class="hover:text-white">Browse Halls</a>
                        </li>
                        <li><a href="#" class="hover:text-white">About Us</a></li>
                        <li><a href="#" class="hover:text-white">Contact</a></li>
                        <li><a href="#" class="hover:text-white">FAQs</a></li>
                    </ul>
                </div>

                <!-- For Owners -->
                <div>
                    <h3 class="mb-4 font-semibold text-white">For Hall Owners</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white">List Your Hall</a></li>
                        <li><a href="#" class="hover:text-white">Owner Dashboard</a></li>
                        <li><a href="#" class="hover:text-white">Pricing</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="mb-4 font-semibold text-white">Contact Us</h3>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>info@majalis.com</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span>+968 1234 5678</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="pt-8 mt-8 text-sm text-center text-gray-400 border-t border-gray-800">
                <p>&copy; {{ date('Y') }} majalis. All rights reserved.</p>
            </div>
        </div>
    </footer> --}}

    @include('layouts.footer')

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        function hallsApp() {
            return {
                view: 'grid',
                showFilters: false,
                map: null,
                mobileFilters: {
                    region_id: '{{ request('region_id') }}',
                    city_id: '{{ request('city_id') }}',
                    view_mode: '{{ request('view_mode') }}'
                },
                mobileCities: @json($cities),

                get activeFiltersCount() {
                    let count = 0;
                    const params = new URLSearchParams(window.location.search);
                    if (params.get('region_id')) count++;
                    if (params.get('city_id')) count++;
                    if (params.get('capacity')) count++;
                    if (params.get('min_price')) count++;
                    if (params.get('max_price')) count++;
                    if (params.get('search')) count++;
                    return count;
                },

                init() {
                    this.$watch('view', value => {
                        if (value === 'map' && !this.map) {
                            this.$nextTick(() => this.initMap());
                        }
                    });

                    // Prevent body scroll when filter modal is open
                    this.$watch('showFilters', value => {
                        if (value) {
                            document.body.style.overflow = 'hidden';
                        } else {
                            document.body.style.overflow = '';
                        }
                    });
                },

                async loadCitiesMobile() {
                    if (!this.mobileFilters.region_id) {
                        this.mobileCities = [];
                        this.mobileFilters.city_id = '';
                        return;
                    }

                    try {
                        const response = await fetch(`{{ url('/halls/cities') }}/${this.mobileFilters.region_id}`);
                        this.mobileCities = await response.json();
                    } catch (error) {
                        console.error('Error loading cities:', error);
                    }
                },

                initMap() {
                    this.map = L.map('map').setView([23.6100, 58.5400], 7);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap',
                        maxZoom: 19
                    }).addTo(this.map);

                    const halls = @json($mapHalls);

                    halls.forEach(hall => {
                        if (hall.latitude && hall.longitude) {
                            const marker = L.marker([parseFloat(hall.latitude), parseFloat(hall.longitude)]).addTo(
                                this.map);
                            const hallName = typeof hall.name === 'object' ? (hall.name[
                                '{{ app()->getLocale() }}'] || hall.name.en) : hall.name;
                            const cityName = typeof hall.city.name === 'object' ? (hall.city.name[
                                '{{ app()->getLocale() }}'] || hall.city.name.en) : hall.city.name;
                            const imageUrl = hall.featured_image ? `/storage/${hall.featured_image}` : '';

                            const popupContent = `
                                <div style="font-family: 'Tajawal', sans-serif; min-width: 200px;">
                                    ${imageUrl ? `<img src="${imageUrl}" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; margin-bottom: 8px;">` : ''}
                                    <h3 style="font-weight: bold; margin-bottom: 4px;">${hallName}</h3>
                                    <p style="color: #666; font-size: 14px; margin-bottom: 8px;">${cityName}</p>
                                    <p style="color: #0284c7; font-weight: bold; margin-bottom: 8px;">${parseFloat(hall.price_per_slot).toFixed(3)} OMR</p>
                                    <a href="{{ url('/halls') }}/${hall.slug}" style="display: block; text-align: center; background: #0284c7; color: white; padding: 8px; border-radius: 8px; text-decoration: none; font-weight: 500;">
                                        {{ __('halls.view_details') }}
                                    </a>
                                </div>
                            `;

                            marker.bindPopup(popupContent, {
                                maxWidth: 250
                            });
                        }
                    });

                    if (halls.length > 0) {
                        const markers = halls.map(h => L.marker([parseFloat(h.latitude), parseFloat(h.longitude)]));
                        const group = new L.featureGroup(markers);
                        this.map.fitBounds(group.getBounds().pad(0.1));
                    }
                }
            }
        }
    </script>
</body>

</html>
