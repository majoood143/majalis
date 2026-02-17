{{--
|--------------------------------------------------------------------------
| Majalis — Hall Browsing with Smart Date Search (COMPLETE)
|--------------------------------------------------------------------------
|
| REPLACES: resources/views/customer/halls/index.blade.php
|
| Route:      customer.halls.index
| Controller: App\Http\Controllers\Customer\HallController@index
|
| ALL FEATURES:
|   ✅ Smart date + time slot search (Phase 1-4)
|   ✅ Language switcher (AR/EN)
|   ✅ Leaflet map view with availability-colored pins
|   ✅ Mobile bottom navigation (grid/map/filters)
|   ✅ Mobile filter bottom-sheet modal
|   ✅ Desktop filter sidebar
|   ✅ Text search
|   ✅ View mode: All / By Region
|   ✅ Slot badges + prices on hall cards
|   ✅ Grayed-out fully booked halls
|   ✅ Nearby date suggestions fallback
|   ✅ Price stats (min/max)
|   ✅ Active filter count badge
|   ✅ Tajawal font + full RTL support
|   ✅ Footer include
|   ✅ Mobile-first responsive design
|
| Variables from HallController@index:
|   $halls, $regions, $cities, $features, $timeSlots,
|   $isDateSearch, $suggestions, $availableCount,
|   $mapHalls, $stats, $hallsByRegion
|
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>{{ __('halls.browse_halls') }} - Majalis</title>
    <link rel="icon" type="image/ico" href="{{ asset('images/favicon.ico') }}" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tajawal Font (Arabic-optimized) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { tajawal: ['Tajawal', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0', 300: '#6ee7b7',
                            400: '#34d399', 500: '#10b981', 600: '#059669', 700: '#047857',
                            800: '#065f46', 900: '#064e3b',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        * { font-family: 'Tajawal', 'system-ui', '-apple-system', sans-serif; }

        /* ── Map ── */
        #map { height: calc(100vh - 200px); min-height: 400px; position: relative; z-index: 1; }
        .leaflet-container { position: relative !important; z-index: 1 !important; }
        .leaflet-pane { z-index: 5 !important; }
        [dir="rtl"] .leaflet-right { left: 0; right: auto; }

        /* ── Alpine cloak ── */
        [x-cloak] { display: none !important; }

        /* ── Mobile card interactions ── */
        .hall-card { transition: transform 0.2s, box-shadow 0.2s; }
        .hall-card:active { transform: scale(0.98); }
        @media (min-width: 768px) {
            .hall-card:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1); }
        }

        /* ── Bottom nav ── */
        .bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: white; border-top: 1px solid #e5e7eb; z-index: 50; padding-bottom: env(safe-area-inset-bottom); }

        /* ── Mobile filter modal ── */
        .filter-modal { position: fixed; inset: 0; z-index: 100; display: flex; align-items: flex-end; }
        .filter-content { background: white; width: 100%; max-height: 85vh; border-radius: 24px 24px 0 0; display: flex; flex-direction: column; overflow: hidden; }
        .filter-header { position: sticky; top: 0; background: white; z-index: 10; padding: 16px 20px; border-bottom: 1px solid #e5e7eb; flex-shrink: 0; }
        .filter-body { flex: 1; overflow-y: auto; padding: 20px; padding-bottom: 100px; -webkit-overflow-scrolling: touch; }
        .filter-footer { position: sticky; bottom: 0; background: white; border-top: 1px solid #e5e7eb; padding: 16px 20px; padding-bottom: calc(16px + env(safe-area-inset-bottom)); flex-shrink: 0; box-shadow: 0 -4px 6px -1px rgb(0 0 0 / 0.1); }

        /* ── Z-index layers ── */
        nav.sticky.top-0 { z-index: 800; }
        .sticky-search { z-index: 40; }

        /* ── Smooth scroll + safe areas ── */
        html { scroll-behavior: smooth; }
        .safe-area-top { padding-top: env(safe-area-inset-top); }

        /* ── Better touch targets ── */
        button, a, select { min-height: 44px; }
        input:focus, select:focus { outline: none; }

        /* ── Glass morphism ── */
        .glass { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
    </style>
</head>

<body class="font-tajawal bg-gray-50" x-data="hallsApp()" x-cloak>

    {{-- ═══════════════════════════════════════════════════════════
         TOP NAVIGATION BAR
         ═══════════════════════════════════════════════════════════ --}}
    <nav class="sticky top-0 z-50 border-b border-gray-200 glass safe-area-top">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="/" class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-10 h-10 shadow-lg rounded-xl">
                        <img src="{{ asset('images/logo.webp') }}" alt="Majalis Logo" class="w-8 h-8">
                    </div>
                    <span class="hidden text-xl font-bold text-gray-800 sm:block">{{ __('guest.majalis') }}</span>
                </a>

                {{-- Right side: Language + Auth --}}
                <div class="flex items-center gap-2">
                    {{-- Language Switcher --}}
                    <a href="{{ request()->fullUrlWithQuery(['lang' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}"
                       class="flex items-center gap-1.5 px-3 py-2 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                        </svg>
                        <span class="text-sm font-medium text-gray-700">
                            {{ app()->getLocale() === 'ar' ? 'EN' : 'عربي' }}
                        </span>
                    </a>

                    {{-- Login/Dashboard Button --}}
                    @auth
                        <a href="{{ route('customer.dashboard') }}"
                           class="items-center hidden gap-2 px-4 py-2 text-white transition rounded-lg shadow-sm md:flex bg-brand-600 hover:bg-brand-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            <span class="text-sm font-medium">{{ __('halls.dashboard') }}</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="items-center hidden gap-2 px-4 py-2 text-white transition rounded-lg shadow-sm md:flex bg-brand-600 hover:bg-brand-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-sm font-medium">{{ __('halls.login') }}</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ═══════════════════════════════════════════════════════════
         HERO + SMART SEARCH BAR
         ═══════════════════════════════════════════════════════════ --}}
    <section class="relative overflow-hidden text-white bg-gradient-to-br from-gray-900 via-gray-800 to-brand-900">
        {{-- Pattern overlay --}}
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;40&quot; height=&quot;40&quot; viewBox=&quot;0 0 40 40&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;%23fff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M20 20.5V18H0v-2h20v-2l2 3.5-2 3zM0 20h2v2H0v-2z&quot;/%3E%3C/g%3E%3C/svg%3E');"></div>

        <div class="container relative px-4 py-8 mx-auto md:py-14">
            <div class="mb-6 text-center md:mb-8">
                <h1 class="mb-2 text-2xl font-bold md:text-4xl">{{ __('halls.browse_halls') }}</h1>
                <p class="text-sm text-brand-200/80 md:text-lg">{{ __('halls.find_perfect_venue') }}</p>
            </div>

            {{-- ── Smart Search Form ── --}}
            <form id="smart-search-form" action="{{ route('customer.halls.index') }}" method="GET"
                  class="max-w-5xl p-4 mx-auto border shadow-2xl bg-white/10 backdrop-blur-xl rounded-2xl md:p-6 border-white/20">
                <input type="hidden" name="lang" value="{{ app()->getLocale() }}">

                <div class="grid grid-cols-2 gap-3 md:grid-cols-12">
                    {{-- Date --}}
                    <div class="col-span-1 md:col-span-3">
                        <label class="block text-[10px] md:text-xs font-semibold text-brand-300 uppercase tracking-wider mb-1">
                            {{ __('halls.event_date') }} <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <input type="date" id="search-date" name="date" value="{{ request('date', '') }}"
                                   min="{{ now()->format('Y-m-d') }}" required
                                   class="w-full px-3 md:px-4 py-2.5 md:py-3 bg-white/90 text-gray-800 rounded-xl border-0 focus:ring-2 focus:ring-brand-400 font-medium text-sm">
                            <div id="date-badge" class="hidden absolute -top-2 {{ app()->getLocale() === 'ar' ? '-left-2' : '-right-2' }} text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-lg"></div>
                        </div>
                    </div>

                    {{-- Time Slot --}}
                    <div class="col-span-1 md:col-span-3">
                        <label class="block text-[10px] md:text-xs font-semibold text-brand-300 uppercase tracking-wider mb-1">
                            {{ __('halls.time_slot') }} <span class="text-red-400">*</span>
                        </label>
                        <select id="search-time-slot" name="time_slot" required
                                class="w-full px-3 md:px-4 py-2.5 md:py-3 bg-white/90 text-gray-800 rounded-xl border-0 focus:ring-2 focus:ring-brand-400 font-medium appearance-none text-sm">
                            <option value="" disabled {{ request('time_slot') ? '' : 'selected' }}>{{ __('halls.select_time') }}</option>
                            @foreach ($timeSlots as $val => $lbl)
                                <option value="{{ $val }}" {{ request('time_slot') === $val ? 'selected' : '' }}>{{ __($lbl) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Region --}}
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-[10px] md:text-xs font-semibold text-brand-300 uppercase tracking-wider mb-1">
                            {{ __('halls.region') }}
                        </label>
                        <select id="search-region" name="region_id"
                                class="w-full px-3 md:px-4 py-2.5 md:py-3 bg-white/90 text-gray-800 rounded-xl border-0 focus:ring-2 focus:ring-brand-400 font-medium appearance-none text-sm">
                            <option value="">{{ __('halls.all_regions') }}</option>
                            @foreach ($regions as $region)
                                <option value="{{ $region->id }}" {{ (int) request('region_id') === $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- City --}}
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-[10px] md:text-xs font-semibold text-brand-300 uppercase tracking-wider mb-1">
                            {{ __('halls.city') }}
                        </label>
                        <select id="search-city" name="city_id"
                                class="w-full px-3 md:px-4 py-2.5 md:py-3 bg-white/90 text-gray-800 rounded-xl border-0 focus:ring-2 focus:ring-brand-400 font-medium appearance-none text-sm">
                            <option value="">{{ __('halls.all_cities') }}</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" {{ (int) request('city_id') === $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search Button --}}
                    <div class="flex items-end col-span-2 md:col-span-2">
                        <button type="submit"
                                class="w-full px-4 py-2.5 md:py-3 bg-brand-500 hover:bg-brand-400 text-white font-bold rounded-xl transition-all shadow-lg shadow-brand-500/30 hover:shadow-brand-400/40 flex items-center justify-center gap-2 text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            {{ __('halls.search') }}
                        </button>
                    </div>
                </div>

                {{-- Advanced Filters (collapsible) --}}
                <div x-data="{ open: {{ request('max_price') || request('features') || request('min_guests') || request('search') ? 'true' : 'false' }} }" class="mt-3">
                    <button type="button" @click="open = !open" class="flex items-center gap-1 text-xs transition text-brand-300 hover:text-white">
                        <svg class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        <span x-text="open ? '{{ __('halls.hide_filters') }}' : '{{ __('halls.more_filters') }}'"></span>
                    </button>
                    <div x-show="open" x-collapse class="grid grid-cols-2 gap-3 mt-3 md:grid-cols-4">
                        {{-- Text Search --}}
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-[10px] md:text-xs font-semibold text-brand-300 uppercase tracking-wider mb-1">{{ __('halls.search_placeholder') }}</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('halls.search_placeholder') }}"
                                   class="w-full px-3 py-2.5 bg-white/90 text-gray-800 rounded-xl border-0 focus:ring-2 focus:ring-brand-400 placeholder-gray-400 font-medium text-sm">
                        </div>
                        {{-- Min Guests --}}
                        <div>
                            <label class="block text-[10px] md:text-xs font-semibold text-brand-300 uppercase tracking-wider mb-1">{{ __('halls.guests') }}</label>
                            <input type="number" name="min_guests" value="{{ request('min_guests') }}" min="1" placeholder="{{ __('halls.any') }}"
                                   class="w-full px-3 py-2.5 bg-white/90 text-gray-800 rounded-xl border-0 focus:ring-2 focus:ring-brand-400 placeholder-gray-400 font-medium text-sm">
                        </div>
                        {{-- Max Price --}}
                        <div>
                            <label class="block text-[10px] md:text-xs font-semibold text-brand-300 uppercase tracking-wider mb-1">{{ __('halls.max_price') }} <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                        class="inline w-5 h-5 -mt-1 text-brand-300"></label>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" min="0" step="0.001" placeholder="{{ __('halls.no_limit') }}"
                                   class="w-full px-3 py-2.5 bg-white/90 text-gray-800 rounded-xl border-0 focus:ring-2 focus:ring-brand-400 placeholder-gray-400 font-medium text-sm">
                        </div>
                        {{-- Features --}}
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-[10px] md:text-xs font-semibold text-brand-300 uppercase tracking-wider mb-1">{{ __('halls.features') }}</label>
                            <div class="flex flex-wrap gap-1.5 max-h-20 overflow-y-auto p-1.5 bg-white/5 rounded-xl">
                                @foreach ($features as $feature)
                                    @php $fc = in_array($feature->id, array_map('intval', (array) request('features', []))); @endphp
                                    <label class="inline-flex items-center gap-1 px-2 py-1 rounded-md cursor-pointer transition text-xs {{ $fc ? 'bg-brand-500 text-white' : 'bg-white/20 text-white/80 hover:bg-white/30' }}">
                                        <input type="checkbox" name="features[]" value="{{ $feature->id }}" class="sr-only" {{ $fc ? 'checked' : '' }}>
                                        <span>{{ $feature->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Price stats hint --}}
                @if (isset($stats) && $stats['min_price'])
                    <p class="mt-2 text-[11px] text-brand-300/60 text-center">
                        {{ __('halls.starting_from') }} {{ number_format($stats['min_price'], 3) }} – {{ number_format($stats['max_price'], 3) }} <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                        class="inline w-5 h-5 -mt-1">
                    </p>
                @endif
            </form>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
         RESULTS TOOLBAR (count + view mode + sort)
         ═══════════════════════════════════════════════════════════ --}}
    <div class="container px-4 pt-6 pb-2 mx-auto">
        <div class="flex flex-wrap items-center justify-between gap-3">
            {{-- Left: result count + availability summary --}}
            <div class="flex-1 min-w-0">
                @if ($isDateSearch)
                    <h2 class="text-lg font-bold text-gray-800 truncate">
                        @if ($availableCount > 0)
                            <span class="text-brand-600">{{ $availableCount }}</span> {{ __('halls.halls_available') }}
                        @else
                            {{ __('halls.no_halls_available') }}
                        @endif
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5 truncate">
                        {{ \Carbon\Carbon::parse(request('date'))->translatedFormat('l, j F Y') }}
                        · {{ __($timeSlots[request('time_slot')] ?? '') }}
                        @if ($halls->count() > $availableCount && $availableCount > 0)
                            · {{ __('halls.plus_booked', ['count' => $halls->count() - $availableCount]) }}
                        @endif
                    </p>
                @else
                    <h2 class="text-lg font-bold text-gray-800">
                        <span class="text-brand-600">{{ $halls->count() }}</span> {{ __('halls.halls_found') }}
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">{{ __('halls.search_hint') }}</p>
                @endif
            </div>

            {{-- Right: View mode + Sort (desktop only) --}}
            <div class="items-center hidden gap-2 md:flex">
                {{-- View mode: All / By Region --}}
                <div class="flex items-center gap-1 p-1 bg-gray-100 rounded-xl">
                    <a href="{{ request()->fullUrlWithQuery(['view_mode' => null]) }}"
                       class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ !request('view_mode') ? 'bg-white shadow-sm text-brand-600' : 'text-gray-600 hover:text-gray-900' }}">
                        {{ __('halls.view_all') }}
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['view_mode' => 'by_region']) }}"
                       class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ request('view_mode') === 'by_region' ? 'bg-white shadow-sm text-brand-600' : 'text-gray-600 hover:text-gray-900' }}">
                        {{ __('halls.view_by_region') }}
                    </a>
                </div>

                {{-- Grid / Map toggle --}}
                <div class="flex items-center gap-1 p-1 bg-gray-100 rounded-xl">
                    <button @click="view = 'grid'" :class="view === 'grid' ? 'bg-white shadow-sm' : ''" class="p-2 transition rounded-lg">
                        <svg class="w-5 h-5" :class="view === 'grid' ? 'text-brand-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </button>
                    <button @click="view = 'map'" :class="view === 'map' ? 'bg-white shadow-sm' : ''" class="p-2 transition rounded-lg">
                        <svg class="w-5 h-5" :class="view === 'map' ? 'text-brand-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                    </button>
                </div>

                {{-- Sort --}}
                <select id="sort-select" onchange="updateSort(this.value)"
                        class="px-3 py-2 text-sm font-medium bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-400">
                    <option value="" {{ !request('sort') ? 'selected' : '' }}>{{ __('halls.best_match') }}</option>
                    <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>{{ __('halls.highest_rated') }}</option>
                    <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>{{ __('halls.price_low_high') }}</option>
                    <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>{{ __('halls.price_high_low') }}</option>
                    <option value="capacity" {{ request('sort') === 'capacity' ? 'selected' : '' }}>{{ __('halls.largest_capacity') }}</option>
                </select>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         PHASE 4: NO RESULTS — DATE SUGGESTIONS
         ═══════════════════════════════════════════════════════════ --}}
    @if ($isDateSearch && $availableCount === 0)
        <div class="container px-4 mx-auto mb-4">
            @if (count($suggestions) > 0)
                <div class="p-4 border bg-amber-50 border-amber-200 rounded-2xl md:p-5">
                    <div class="flex items-start gap-3">
                        <div class="flex items-center justify-center flex-shrink-0 rounded-full w-9 h-9 bg-amber-100">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="mb-1 text-base font-bold text-amber-800">
                                {{ __('halls.no_availability_on', ['date' => \Carbon\Carbon::parse(request('date'))->translatedFormat('j F Y')]) }}
                            </h3>
                            <p class="mb-3 text-xs text-amber-700">{{ __('halls.try_nearby_dates') }}</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($suggestions as $s)
                                    <a href="{{ route('customer.halls.index', array_merge(request()->except('date'), ['date' => $s['date']])) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-amber-200 rounded-xl hover:border-brand-400 hover:bg-brand-50 transition-all group text-sm">
                                        <div>
                                            <div class="font-semibold text-gray-800 group-hover:text-brand-700">{{ $s['formatted'] }}</div>
                                            <div class="text-[10px] text-gray-500">{{ $s['hall_count'] }} {{ __('halls.available') }}</div>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-300 group-hover:text-brand-500 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-8 text-center border border-gray-200 bg-gray-50 rounded-2xl">
                    <div class="flex items-center justify-center mx-auto mb-3 bg-gray-100 rounded-full w-14 h-14">
                        <svg class="text-gray-400 w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="mb-1 text-base font-bold text-gray-700">{{ __('halls.no_halls_around_date') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('halls.try_different_date') }}</p>
                </div>
            @endif
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
         MAIN CONTENT: GRID VIEW / MAP VIEW
         ═══════════════════════════════════════════════════════════ --}}
    <div class="container px-4 mx-auto pb-28 md:pb-12">

        {{-- ── GRID VIEW ── --}}
        <div x-show="view === 'grid'">
            @if ($halls->count() > 0)
                {{-- By Region view --}}
                @if (request('view_mode') === 'by_region')
                    @php $hasRegionResults = false; @endphp
                    @foreach ($regions as $region)
                        @php
                            $regionHalls = $halls->filter(fn($h) => $h->city?->region_id == $region->id);
                        @endphp
                        @if ($regionHalls->count() > 0)
                            @php $hasRegionResults = true; @endphp
                            <div class="mb-10">
                                <div class="flex items-center justify-between pb-3 mb-5 border-b-2 border-brand-100">
                                    <h2 class="text-xl font-bold text-gray-800 md:text-2xl">{{ $region->name }}</h2>
                                    <span class="px-3 py-1 text-sm font-medium text-gray-600 bg-gray-100 rounded-full">
                                        {{ $regionHalls->count() }} {{ __('halls.halls_in_region') }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 md:gap-6">
                                    @foreach ($regionHalls as $hall)
                                        @include('customer.halls.partials.smart-hall-card', ['hall' => $hall, 'isDateSearch' => $isDateSearch, 'timeSlots' => $timeSlots])
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @if (!$hasRegionResults)
                        @include('customer.halls.partials.empty-state')
                    @endif
                @else
                    {{-- Regular grid --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 md:gap-6">
                        @foreach ($halls as $hall)
                            @include('customer.halls.partials.smart-hall-card', ['hall' => $hall, 'isDateSearch' => $isDateSearch, 'timeSlots' => $timeSlots])
                        @endforeach
                    </div>
                @endif
            @else
                @include('customer.halls.partials.empty-state')
            @endif
        </div>

        {{-- ── MAP VIEW ── --}}
        <div x-show="view === 'map'" x-cloak>
            <div id="map" class="border-2 border-gray-200 shadow-lg rounded-2xl"></div>
            <div class="flex items-center gap-2 p-3 mt-3 border border-blue-200 bg-blue-50 rounded-xl">
                <svg class="flex-shrink-0 w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-medium text-blue-800">
                    <strong>{{ $mapHalls->count() }}</strong> {{ __('halls.halls_on_map') }}
                    @if ($isDateSearch)
                        · <span class="text-brand-600">{{ __('halls.green_available') }}</span>
                        · <span class="text-red-500">{{ __('halls.red_booked') }}</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         MOBILE FILTER MODAL (Bottom Sheet)
         ═══════════════════════════════════════════════════════════ --}}
    <div x-show="showFilters" x-cloak @click.self="showFilters = false"
         class="md:hidden filter-modal"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         style="background: rgba(0,0,0,0.6);">

        <div class="filter-content"
             x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">

            {{-- Header --}}
            <div class="filter-header">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">{{ __('halls.filters') }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5" x-text="activeFiltersCount > 0 ? activeFiltersCount + ' {{ __('halls.active_filters') }}' : '{{ __('halls.no_active_filters') }}'"></p>
                    </div>
                    <button @click="showFilters = false" class="flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-100">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <form action="{{ route('customer.halls.index') }}" method="GET">
                <div class="filter-body">
                    <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                    @if (request('date'))
                        <input type="hidden" name="date" value="{{ request('date') }}">
                    @endif
                    @if (request('time_slot'))
                        <input type="hidden" name="time_slot" value="{{ request('time_slot') }}">
                    @endif

                    <div class="space-y-6">
                        {{-- Region --}}
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">{{ __('halls.region') }}</label>
                            <select name="region_id" x-model="mobileFilters.region_id" @change="loadCitiesMobile()"
                                    class="w-full px-4 text-base border-2 border-gray-200 h-14 rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                                <option value="">{{ __('halls.all_regions') }}</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- City --}}
                        <div x-show="mobileCities.length > 0">
                            <label class="block mb-2 text-sm font-bold text-gray-700">{{ __('halls.city') }}</label>
                            <select name="city_id" x-model="mobileFilters.city_id"
                                    class="w-full px-4 text-base border-2 border-gray-200 h-14 rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                                <option value="">{{ __('halls.all_cities') }}</option>
                                <template x-for="city in mobileCities" :key="city.id">
                                    <option :value="city.id" x-text="typeof city.name === 'object' ? (city.name['{{ app()->getLocale() }}'] || city.name.en) : city.name"></option>
                                </template>
                            </select>
                        </div>

                        {{-- Guests --}}
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">{{ __('halls.guests') }}</label>
                            <input type="number" name="min_guests" value="{{ request('min_guests') }}" min="1" placeholder="{{ __('halls.enter_guests') }}"
                                   class="w-full px-4 text-base border-2 border-gray-200 h-14 rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                        </div>

                        {{-- Price Range --}}
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">{{ __('halls.price_range') }}</label>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" step="0.001" placeholder="{{ __('halls.min_price') }}"
                                       class="w-full px-4 text-base border-2 border-gray-200 h-14 rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                                <input type="number" name="max_price" value="{{ request('max_price') }}" step="0.001" placeholder="{{ __('halls.max_price') }}"
                                       class="w-full px-4 text-base border-2 border-gray-200 h-14 rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                            </div>
                            @if (isset($stats) && $stats['min_price'])
                                <p class="mt-2 text-xs text-gray-500">{{ __('halls.starting_from') }} {{ number_format($stats['min_price'], 3) }} – {{ number_format($stats['max_price'], 3) }} <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                        class="inline w-5 h-5 -mt-1"></p>
                            @endif
                        </div>

                        {{-- View Mode --}}
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">{{ __('halls.view_mode') }}</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center justify-center px-4 transition border-2 cursor-pointer h-14 rounded-xl"
                                       :class="!mobileFilters.view_mode ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-gray-200'">
                                    <input type="radio" name="view_mode" value="" x-model="mobileFilters.view_mode" class="hidden">
                                    <span class="font-medium">{{ __('halls.view_all') }}</span>
                                </label>
                                <label class="flex items-center justify-center px-4 transition border-2 cursor-pointer h-14 rounded-xl"
                                       :class="mobileFilters.view_mode === 'by_region' ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-gray-200'">
                                    <input type="radio" name="view_mode" value="by_region" x-model="mobileFilters.view_mode" class="hidden">
                                    <span class="font-medium">{{ __('halls.view_by_region') }}</span>
                                </label>
                            </div>
                        </div>

                        {{-- Sort --}}
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">{{ __('halls.sort_by') }}</label>
                            <select name="sort" class="w-full px-4 text-base border-2 border-gray-200 h-14 rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                                <option value="" {{ !request('sort') ? 'selected' : '' }}>{{ __('halls.best_match') }}</option>
                                <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>{{ __('halls.highest_rated') }}</option>
                                <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>{{ __('halls.price_low_high') }}</option>
                                <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>{{ __('halls.price_high_low') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="filter-footer">
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('customer.halls.index') }}?lang={{ app()->getLocale() }}"
                           class="flex items-center justify-center font-semibold text-gray-700 border-2 border-gray-300 h-14 rounded-xl hover:bg-gray-50">
                            {{ __('halls.clear_filters') }}
                        </a>
                        <button type="submit"
                                class="flex items-center justify-center font-bold text-white shadow-lg h-14 bg-brand-600 rounded-xl hover:bg-brand-700">
                            {{ __('halls.apply') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         MOBILE BOTTOM NAVIGATION
         ═══════════════════════════════════════════════════════════ --}}
    <nav class="bottom-nav md:hidden">
        <div class="grid grid-cols-3 gap-1 p-2">
            <button @click="view = 'grid'" :class="view === 'grid' ? 'text-brand-600' : 'text-gray-500'" class="flex flex-col items-center gap-1 p-2 rounded-lg active:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="text-xs font-medium">{{ __('halls.grid') }}</span>
            </button>

            <button @click="view = 'map'" :class="view === 'map' ? 'text-brand-600' : 'text-gray-500'" class="flex flex-col items-center gap-1 p-2 rounded-lg active:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <span class="text-xs font-medium">{{ __('halls.map') }}</span>
            </button>

            <button @click="showFilters = true" class="relative flex flex-col items-center gap-1 p-2 text-gray-500 rounded-lg active:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
                <span class="text-xs font-medium">{{ __('halls.filters') }}</span>
                <span x-show="activeFiltersCount > 0"
                      class="absolute top-0 {{ app()->getLocale() === 'ar' ? 'left-2' : 'right-2' }} bg-red-500 text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center shadow-lg"
                      x-text="activeFiltersCount"></span>
            </button>
        </div>
    </nav>

    {{-- ═══════════════════════════════════════════════════════════
         FOOTER
         ═══════════════════════════════════════════════════════════ --}}
    @include('layouts.footer')

    {{-- ═══════════════════════════════════════════════════════════
         SCRIPTS
         ═══════════════════════════════════════════════════════════ --}}
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
                const p = new URLSearchParams(window.location.search);
                ['region_id','city_id','min_guests','min_price','max_price','search','features[]'].forEach(k => {
                    if (p.get(k)) count++;
                });
                return count;
            },

            init() {
                this.$watch('view', val => {
                    if (val === 'map' && !this.map) this.$nextTick(() => this.initMap());
                });
                this.$watch('showFilters', val => {
                    document.body.style.overflow = val ? 'hidden' : '';
                });
            },

            async loadCitiesMobile() {
                if (!this.mobileFilters.region_id) {
                    this.mobileCities = [];
                    this.mobileFilters.city_id = '';
                    return;
                }
                try {
                    const r = await fetch(`{{ url('/halls/cities') }}/${this.mobileFilters.region_id}`);
                    this.mobileCities = await r.json();
                } catch (e) { console.error('City load error:', e); }
            },

            initMap() {
                this.map = L.map('map').setView([23.61, 58.54], 7);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap', maxZoom: 19
                }).addTo(this.map);

                const halls = @json($mapHalls);
                const locale = '{{ app()->getLocale() }}';
                const isDateSearch = {{ $isDateSearch ? 'true' : 'false' }};
                const viewUrl = '{{ url('/halls') }}';

                halls.forEach(h => {
                    if (!h.lat || !h.lng) return;

                    // Color markers: green=available, red=booked, blue=default
                    const isAvail = h.available !== false;
                    const color = isDateSearch ? (isAvail ? '#10b981' : '#ef4444') : '#3b82f6';

                    const icon = L.divIcon({
                        html: `<div style="background:${color};width:32px;height:32px;border-radius:50%;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;">
                            <svg width="16" height="16" fill="white" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>`,
                        iconSize: [32, 32],
                        iconAnchor: [16, 32],
                        popupAnchor: [0, -32],
                        className: ''
                    });

                    const name = typeof h.name === 'object' ? (h.name[locale] || h.name.en || '') : h.name;
                    const img = h.image ? `<img src="${h.image}" style="width:100%;height:100px;object-fit:cover;border-radius:8px;margin-bottom:8px;">` : '';
                    const badge = isDateSearch
                        ? `<span style="display:inline-block;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:bold;color:white;background:${color};margin-bottom:6px;">${isAvail ? '{{ __('halls.available') }}' : '{{ __('halls.fully_booked') }}'}</span><br>`
                        : '';

                    const popup = `
                        <div style="font-family:'Tajawal',sans-serif;min-width:180px;direction:${locale === 'ar' ? 'rtl' : 'ltr'}">
                            ${img}
                            ${badge}
                            <strong style="font-size:14px;">${name}</strong><br>
                            <span style="color:#059669;font-weight:bold;font-size:13px;">${parseFloat(h.price).toFixed(3)} <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                        class="inline w-5 h-5 -mt-1"></span><br>
                            <a href="${viewUrl}/${h.slug}" style="display:block;text-align:center;background:#059669;color:white;padding:8px;border-radius:8px;text-decoration:none;font-weight:600;margin-top:8px;font-size:13px;">
                                {{ __('halls.view_details') }}
                            </a>
                        </div>`;

                    L.marker([h.lat, h.lng], { icon }).addTo(this.map).bindPopup(popup, { maxWidth: 250 });
                });

                // Fit bounds
                const coords = halls.filter(h => h.lat && h.lng).map(h => [h.lat, h.lng]);
                if (coords.length > 1) this.map.fitBounds(coords, { padding: [30, 30] });
            }
        };
    }

    // ── Search form: Region → City cascade ──
    document.addEventListener('DOMContentLoaded', function() {
        const regionSel = document.getElementById('search-region');
        const citySel = document.getElementById('search-city');
        if (regionSel && citySel) {
            regionSel.addEventListener('change', function() {
                citySel.innerHTML = '<option value="">{{ __('halls.all_cities') }}</option>';
                if (!this.value) return;
                fetch(`{{ url('/halls/cities') }}/${this.value}`)
                    .then(r => r.json())
                    .then(cities => {
                        const loc = '{{ app()->getLocale() }}';
                        cities.forEach(c => {
                            const opt = document.createElement('option');
                            opt.value = c.id;
                            opt.textContent = typeof c.name === 'object' ? (c.name[loc] || c.name.en || '') : c.name;
                            citySel.appendChild(opt);
                        });
                    });
            });
        }

        // ── AJAX availability badge ──
        const dateIn = document.getElementById('search-date');
        const slotIn = document.getElementById('search-time-slot');
        const badge  = document.getElementById('date-badge');
        let timer = null;

        function checkAvail() {
            if (!dateIn?.value || !slotIn?.value) { badge?.classList.add('hidden'); return; }
            clearTimeout(timer);
            timer = setTimeout(() => {
                const p = new URLSearchParams({ date: dateIn.value, time_slot: slotIn.value });
                if (citySel?.value) p.append('city_id', citySel.value);
                fetch(`/api/halls/check-availability?${p}`)
                    .then(r => r.json())
                    .then(d => {
                        badge.textContent = d.available_count;
                        badge.className = `absolute -top-2 {{ app()->getLocale() === 'ar' ? '-left-2' : '-right-2' }} text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-lg ${d.available_count > 0 ? 'bg-brand-500' : 'bg-red-500'}`;
                    }).catch(() => badge?.classList.add('hidden'));
            }, 400);
        }
        dateIn?.addEventListener('change', checkAvail);
        slotIn?.addEventListener('change', checkAvail);
        if (dateIn?.value && slotIn?.value) checkAvail();

        // ── Feature checkbox styling ──
        document.querySelectorAll('input[name="features[]"]').forEach(cb => {
            cb.addEventListener('change', function() {
                const l = this.closest('label');
                l.classList.toggle('bg-brand-500', this.checked);
                l.classList.toggle('text-white', this.checked);
                l.classList.toggle('bg-white/20', !this.checked);
                l.classList.toggle('text-white/80', !this.checked);
            });
        });
    });

    function updateSort(v) {
        const u = new URL(window.location.href);
        v ? u.searchParams.set('sort', v) : u.searchParams.delete('sort');
        window.location.href = u.toString();
    }
    </script>
</body>
</html>
