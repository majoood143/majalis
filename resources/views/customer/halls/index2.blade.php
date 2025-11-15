<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ __('halls.browse_halls') }} - Majalis</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Tajawal', 'system-ui', '-apple-system', sans-serif;
        }

        #map {
            height: calc(100vh - 140px);
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
        [dir="rtl"] .leaflet-top {
            right: auto;
            left: 10px;
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
<body class="bg-gray-50" x-data="hallsApp()">

    <!-- Top Navigation -->
    <nav class="sticky top-0 z-50 border-b border-gray-200 glass-morphism">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl">
                        <span class="text-xl font-bold text-white">م</span>
                    </div>
                    <span class="hidden text-xl font-bold text-gray-800 sm:block">Majalis</span>
                </a>

                <div class="flex items-center gap-3">
                    <a href="{{ request()->fullUrlWithQuery(['lang' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}"
                       class="flex items-center gap-2 px-3 py-2 transition rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">
                            {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="text-white bg-gradient-to-br from-primary-600 to-primary-800">
        <div class="container px-4 py-6 mx-auto">
            <h1 class="mb-2 text-2xl font-bold md:text-3xl">{{ __('halls.browse_halls') }}</h1>
            <p class="text-sm text-primary-100 md:text-base">{{ __('halls.find_perfect_venue') }}</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="sticky z-40 bg-white border-b border-gray-200 shadow-sm top-16">
        <div class="container px-4 py-3 mx-auto">
            <form action="{{ route('customer.halls.index') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="lang" value="{{ app()->getLocale() }}">

                <!-- Preserve filters -->
                @foreach(request()->except(['search', 'lang', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach

                <div class="relative flex-1">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="{{ __('halls.search_placeholder') }}"
                        class="w-full px-4 py-3 {{ app()->getLocale() === 'ar' ? 'pr-12' : 'pl-12' }} rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <svg class="w-5 h-5 text-gray-400 absolute top-1/2 -translate-y-1/2 {{ app()->getLocale() === 'ar' ? 'right-4' : 'left-4' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <button type="submit" class="items-center hidden gap-2 px-6 py-3 font-medium text-white transition md:flex bg-primary-600 rounded-xl hover:bg-primary-700">
                    {{ __('halls.search') }}
                </button>

                <button
                    type="button"
                    @click="showFilters = true"
                    class="relative flex items-center gap-2 px-4 py-3 transition bg-gray-100 md:hidden rounded-xl hover:bg-gray-200">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                    <span x-show="activeFiltersCount > 0" class="absolute -top-1 {{ app()->getLocale() === 'ar' ? '-left-1' : '-right-1' }} bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center" x-text="activeFiltersCount"></span>
                </button>
            </form>
        </div>
    </div>

    <div class="container px-4 py-6 mx-auto">
        <div class="flex gap-6">
            <!-- Filters Sidebar (Desktop) -->
            <aside class="hidden md:block w-80 shrink-0">
                <div class="sticky space-y-4 top-32">
                    <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold">{{ __('halls.filters') }}</h3>
                            <a href="{{ route('customer.halls.index') }}?lang={{ app()->getLocale() }}"
                               class="text-sm text-primary-600 hover:text-primary-700">
                                {{ __('halls.clear_filters') }}
                            </a>
                        </div>

                        <form action="{{ route('customer.halls.index') }}" method="GET" class="space-y-4">
                            <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">

                            <!-- Region -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">{{ __('halls.region') }}</label>
                                <select
                                    name="region_id"
                                    onchange="this.form.submit()"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <option value="">{{ __('halls.all_regions') }}</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                            {{ is_array($region->name) ? ($region->name[app()->getLocale()] ?? $region->name['en']) : $region->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- City -->
                            @if(request('region_id'))
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">{{ __('halls.city') }}</label>
                                <select
                                    name="city_id"
                                    onchange="this.form.submit()"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <option value="">{{ __('halls.all_cities') }}</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                            {{ is_array($city->name) ? ($city->name[app()->getLocale()] ?? $city->name['en']) : $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <!-- Capacity -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">{{ __('halls.guests') }}</label>
                                <input
                                    type="number"
                                    name="capacity"
                                    value="{{ request('capacity') }}"
                                    min="1"
                                    placeholder="{{ __('halls.guests') }}"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>

                            <!-- Price Range -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">{{ __('halls.price_range') }}</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <input
                                        type="number"
                                        name="min_price"
                                        value="{{ request('min_price') }}"
                                        step="0.001"
                                        placeholder="{{ __('halls.min_price') }}"
                                        class="px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <input
                                        type="number"
                                        name="max_price"
                                        value="{{ request('max_price') }}"
                                        step="0.001"
                                        placeholder="{{ __('halls.max_price') }}"
                                        class="px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                                @if(isset($stats))
                                <p class="mt-2 text-xs text-gray-500">
                                    {{ __('halls.starting_from') }} {{ number_format($stats['min_price'], 3) }} - {{ number_format($stats['max_price'], 3) }} OMR
                                </p>
                                @endif
                            </div>

                            <button
                                type="submit"
                                class="w-full py-3 font-medium text-white transition rounded-lg bg-primary-600 hover:bg-primary-700">
                                {{ __('halls.apply') }}
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 min-w-0">
                <!-- View Toggle & Sort -->
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <span class="text-sm text-gray-600">
                        <strong>{{ $halls->total() }}</strong> {{ __('halls.halls_found') }}
                    </span>

                    <div class="flex flex-wrap items-center gap-2">
                        <!-- View Mode Toggle -->
                        <div class="flex items-center gap-1 p-1 bg-gray-100 rounded-lg">
                            <a href="{{ request()->fullUrlWithQuery(['view_mode' => null]) }}"
                               class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ !request('view_mode') ? 'bg-white shadow-sm text-primary-600' : 'text-gray-600' }}">
                                {{ __('halls.view_all') }}
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['view_mode' => 'by_region']) }}"
                               class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ request('view_mode') === 'by_region' ? 'bg-white shadow-sm text-primary-600' : 'text-gray-600' }}">
                                {{ __('halls.view_by_region') }}
                            </a>
                        </div>

                        <!-- Sort -->
                        <form action="{{ route('customer.halls.index') }}" method="GET" class="inline">
                            <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                            @foreach(request()->except(['sort', 'lang', 'page']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach

                            <select
                                name="sort"
                                onchange="this.form.submit()"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                                <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>{{ __('halls.latest') }}</option>
                                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>{{ __('halls.name_az') }}</option>
                                <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>{{ __('halls.price_low_high') }}</option>
                                <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>{{ __('halls.price_high_low') }}</option>
                                <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>{{ __('halls.highest_rated') }}</option>
                                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>{{ __('halls.most_popular') }}</option>
                            </select>
                        </form>

                        <!-- Map Toggle -->
                        <div class="items-center hidden gap-1 p-1 bg-gray-100 rounded-lg md:flex">
                            <button
                                @click="view = 'grid'"
                                :class="view === 'grid' ? 'bg-white shadow-sm' : ''"
                                class="p-2 transition rounded-md">
                                <svg class="w-5 h-5" :class="view === 'grid' ? 'text-primary-600' : 'text-gray-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                            </button>
                            <button
                                @click="view = 'map'"
                                :class="view === 'map' ? 'bg-white shadow-sm' : ''"
                                class="p-2 transition rounded-md">
                                <svg class="w-5 h-5" :class="view === 'map' ? 'text-primary-600' : 'text-gray-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Grid View -->
                <div x-show="view === 'grid'" x-cloak>
                    @if(request('view_mode') === 'by_region')
                        <!-- Grouped by Region -->
                        @forelse($regions as $region)
                            @php
                                $regionHalls = $halls->filter(function($hall) use ($region) {
                                    return $hall->city->region_id == $region->id;
                                });
                            @endphp

                            @if($regionHalls->count() > 0)
                                <div class="mb-12">
                                    <div class="flex items-center justify-between mb-6">
                                        <h2 class="text-2xl font-bold text-gray-800">
                                            {{ is_array($region->name) ? ($region->name[app()->getLocale()] ?? $region->name['en']) : $region->name }}
                                        </h2>
                                        <span class="text-sm text-gray-600">
                                            {{ $regionHalls->count() }} {{ __('halls.halls_in_region') }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 md:gap-6">
                                        @foreach($regionHalls as $hall)
                                            @include('customer.halls.partials.hall-card', ['hall' => $hall])
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="py-16 text-center">
                                <p class="text-gray-500">{{ __('halls.no_halls_found') }}</p>
                            </div>
                        @endforelse
                    @else
                        <!-- Regular Grid -->
                        <div class="grid grid-cols-1 gap-4 mb-20 sm:grid-cols-2 lg:grid-cols-3 md:gap-6 md:mb-8">
                            @forelse($halls as $hall)
                                @include('customer.halls.partials.hall-card', ['hall' => $hall])
                            @empty
                                <div class="py-16 text-center col-span-full">
                                    <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <h3 class="mb-2 text-xl font-semibold text-gray-700">{{ __('halls.no_halls_found') }}</h3>
                                    <p class="mb-4 text-gray-500">{{ __('halls.adjust_filters') }}</p>
                                    <a href="{{ route('customer.halls.index') }}?lang={{ app()->getLocale() }}"
                                       class="font-medium text-primary-600 hover:text-primary-700">
                                        {{ __('halls.clear_filters') }}
                                    </a>
                                </div>
                            @endforelse
                        </div>

                        <!-- Pagination -->
                        @if($halls->hasPages())
                            <div class="mt-8 mb-20 md:mb-0">
                                {{ $halls->links() }}
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Map View -->
                <div x-show="view === 'map'" x-cloak class="mb-20 md:mb-0">
                    <div id="map" class="border border-gray-200 shadow-lg rounded-2xl"></div>
                    <div class="p-4 mt-4 border border-blue-200 bg-blue-50 rounded-xl">
                        <p class="text-sm text-blue-800">
                            <strong>{{ $mapHalls->count() }}</strong> {{ __('halls.halls_on_map') }}
                        </p>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile Filter Modal -->
    <div
        x-show="showFilters"
        x-cloak
        @click.self="showFilters = false"
        class="fixed inset-0 z-50 flex items-end bg-black bg-opacity-50 md:hidden">

        <div class="bg-white w-full rounded-t-3xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 flex items-center justify-between p-4 bg-white border-b border-gray-200">
                <h3 class="text-lg font-bold">{{ __('halls.filters') }}</h3>
                <button @click="showFilters = false" class="p-2 rounded-full hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('customer.halls.index') }}" method="GET" class="p-4 space-y-4">
                <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                <input type="hidden" name="search" value="{{ request('search') }}">

                <!-- Mobile filters same as desktop -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">{{ __('halls.region') }}</label>
                    <select name="region_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                        <option value="">{{ __('halls.all_regions') }}</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                {{ is_array($region->name) ? ($region->name[app()->getLocale()] ?? $region->name['en']) : $region->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">{{ __('halls.guests') }}</label>
                    <input type="number" name="capacity" value="{{ request('capacity') }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                </div>

                <div class="flex gap-3 pt-4">
                    <a href="{{ route('customer.halls.index') }}?lang={{ app()->getLocale() }}"
                       class="flex-1 py-3 text-center border border-gray-300 rounded-xl">
                        {{ __('halls.clear_filters') }}
                    </a>
                    <button type="submit" class="flex-1 py-3 text-white bg-primary-600 rounded-xl">
                        {{ __('halls.apply') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bottom Nav (Mobile) -->
    <nav class="bottom-nav md:hidden">
        <div class="grid grid-cols-3 gap-1 p-2">
            <button @click="view = 'grid'" class="flex flex-col items-center gap-1 p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"></path>
                </svg>
                <span class="text-xs">{{ __('halls.list_view') }}</span>
            </button>
            <button @click="view = 'map'" class="flex flex-col items-center gap-1 p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7"></path>
                </svg>
                <span class="text-xs">{{ __('halls.map_view') }}</span>
            </button>
            <button @click="showFilters = true" class="relative flex flex-col items-center gap-1 p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4"></path>
                </svg>
                <span class="text-xs">{{ __('halls.filters') }}</span>
                <span x-show="activeFiltersCount > 0" class="absolute top-0 flex items-center justify-center w-5 h-5 text-xs text-white bg-red-500 rounded-full right-2" x-text="activeFiltersCount"></span>
            </button>
        </div>
    </nav>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        function hallsApp() {
            return {
                view: 'grid',
                showFilters: false,
                map: null,

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
                            const marker = L.marker([parseFloat(hall.latitude), parseFloat(hall.longitude)]).addTo(this.map);
                            const hallName = typeof hall.name === 'object' ? (hall.name['{{ app()->getLocale() }}'] || hall.name.en) : hall.name;
                            const cityName = typeof hall.city.name === 'object' ? (hall.city.name['{{ app()->getLocale() }}'] || hall.city.name.en) : hall.city.name;

                            marker.bindPopup(`
                                <div style="font-family: 'Tajawal', sans-serif;">
                                    <h3 class="font-bold">${hallName}</h3>
                                    <p class="text-sm">${cityName}</p>
                                    <p class="font-bold">${parseFloat(hall.price_per_slot).toFixed(3)} OMR</p>
                                    <a href="{{ url('/halls') }}/${hall.slug}" class="text-primary-600">{{ __('halls.view_details') }}</a>
                                </div>
                            `);
                        }
                    });

                    if (halls.length > 0) {
                        const group = new L.featureGroup(halls.map(h => L.marker([parseFloat(h.latitude), parseFloat(h.longitude)])));
                        this.map.fitBounds(group.getBounds().pad(0.1));
                    }
                }
            }
        }
    </script>
</body>
</html>
