<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ is_array($hall->name) ? $hall->name[app()->getLocale()] ?? $hall->name['en'] : $hall->name }} - Majalis
    </title>
    {{-- Favicon --}}
    @php
        $favicon = \App\Models\Setting::get('seo', 'favicon');
    @endphp
    <link rel="icon" type="image/ico" href="{{ $favicon ? Storage::url($favicon) : asset('images/favicon.ico') }}" />

    {{-- Open Graph --}}
    @php
        $locale        = app()->getLocale();
        $ogTitle       = is_array($hall->name) ? $hall->name[app()->getLocale()] ?? $hall->name['en'] : $hall->name;
        $ogDescription = is_array($hall->description) ? $hall->description[app()->getLocale()] ?? $hall->description['en'] : $hall->description;

        $ogImage       = $hall->featured_image ?: ($hall->images()->active()->first()->image_path ?? null);
        $ogType        = \App\Models\Setting::get('seo', 'og_type', 'website');
    @endphp
    <meta property="og:type"        content="{{ $ogType }}">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta property="og:title"       content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    @if($ogImage)
    <meta property="og:image"       content="{{ Storage::url($ogImage) }}">
    @endif

    {{-- Twitter / X Card --}}
    @php
        $twCard        = \App\Models\Setting::get('seo', 'twitter_card', 'summary_large_image');
        $twSite        = \App\Models\Setting::get('seo', 'twitter_site', '');
        $twTitle       = \App\Models\Setting::get('seo', 'twitter_title_' . $locale)
                         ?? \App\Models\Setting::get('seo', 'twitter_title_en', $ogTitle);
        $twDescription = \App\Models\Setting::get('seo', 'twitter_description_' . $locale)
                         ?? \App\Models\Setting::get('seo', 'twitter_description_en', $ogDescription);
        $twImage       = \App\Models\Setting::get('seo', 'twitter_image', $ogImage);
    @endphp
    <meta name="twitter:card"        content="{{ $twCard }}">
    @if($twSite)
    <meta name="twitter:site"        content="{{ $twSite }}">
    @endif
    <meta name="twitter:title"       content="{{ $twTitle }}">
    <meta name="twitter:description" content="{{ $twDescription }}">
    @if($twImage)
    <meta name="twitter:image"       content="{{ Storage::url($twImage) }}">
    @endif
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-9K3B5QBV5Y');
    </script>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tajawal Font -->

    <style>
        * {
            font-family: 'Tajawal', 'system-ui', '-apple-system', sans-serif;
        }

        .glass-morphism {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .booking-card {
            position: sticky;
            top: 80px;
        }

        @media (max-width: 1023px) {
            .booking-card {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                top: auto;
                z-index: 50;
                padding-bottom: env(safe-area-inset-bottom);
                box-shadow: 0 -4px 6px -1px rgb(0 0 0 / 0.1);
            }
        }

        .safe-area-top {
            padding-top: env(safe-area-inset-top);
        }

        .image-gallery {
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }

        .image-gallery>* {
            scroll-snap-align: start;
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
                            600: '#B9916D',
                            700: '#E8D5C4',
                        }
                    }
                }
            }
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    {{-- Swiper CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.css" />
    <style>
        .leaflet-container { font-family: 'Tajawal', sans-serif !important; }
        .leaflet-popup-content-wrapper { border-radius: 12px !important; box-shadow: 0 4px 20px rgba(0,0,0,0.15) !important; }
        .leaflet-popup-content { margin: 10px 14px !important; font-size: 13px !important; line-height: 1.5 !important; }
        /* Contain Leaflet's internal z-indices so the map doesn't bleed over navbar/other elements */
        #hall-map-desktop, #hall-map-mobile { isolation: isolate; }
    </style>

    <style>
        /* ... existing styles ... */

        /* Gallery Swiper Styles */
        .hallGalleryMain .swiper-slide-thumb-active .aspect-square>div {
            background: rgba(14, 165, 233, 0.2) !important;
            border-color: #0ea5e9 !important;
        }

        .hallGalleryThumbs .swiper-slide {
            opacity: 0.6;
            transition: opacity 0.3s;
        }

        .hallGalleryThumbs .swiper-slide-thumb-active {
            opacity: 1;
        }

        .swiper-pagination-bullet {
            background: white !important;
            opacity: 0.5 !important;
        }

        .swiper-pagination-bullet-active {
            opacity: 1 !important;
        }

        /* Lightbox Styles */
        #lightbox.active {
            display: block;
        }

        .lightboxSwiper {
            width: 100%;
            height: 100%;
        }

        /* RTL Support */
        [dir="rtl"] .swiper-button-prev:after {
            content: 'next' !important;
        }

        [dir="rtl"] .swiper-button-next:after {
            content: 'prev' !important;
        }
    </style>
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
</head>

<body class="bg-gray-50">

    <!-- Top Navigation -->
    <nav class="sticky top-0 z-50 border-b border-gray-200 glass-morphism safe-area-top">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between h-16">
                <!-- Back Button -->
                <a href="{{ route('customer.halls.index') }}?lang={{ app()->getLocale() }}"
                    class="flex items-center gap-2 text-gray-700 transition hover:text-gray-900">
                    <svg class="w-6 h-6 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    <span class="hidden font-medium sm:inline">{{ __('halls.breadcrumb_halls') }}</span>
                </a>

                <!-- Logo (Desktop) -->
                <a href="/" class="items-center hidden gap-2 md:flex">
                    <div class="flex items-center justify-center w-10 h-10 shadow-lg bg-gradient-to-br rounded-xl">
                        <img src="{{ asset('images/logo.webp') }}" alt="Majalis Logo" class="w-8 h-8 rounded-xl">
                    </div>
                    <span class="text-xl font-bold text-gray-800">
                        {{ app()->getLocale() === 'ar' ? 'مجالس' : 'Majalis' }}</span>
                </a>

                <!-- Language Switcher -->
                <a href="{{ request()->fullUrlWithQuery(['lang' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}"
                    class="flex items-center gap-2 px-3 py-2 transition rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129">
                        </path>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">
                        {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
                    </span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Image -->
    <div class="relative h-64 overflow-hidden bg-gray-200 md:h-96">
        @if ($hall->featured_image)
            <img src="{{ asset('storage/' . $hall->featured_image) }}"
                alt="{{ is_array($hall->name) ? $hall->name[app()->getLocale()] ?? $hall->name['en'] : $hall->name }}"
                class="object-cover w-full h-full">
        @else
            <div
                class="flex items-center justify-center w-full h-full bg-gradient-to-br from-primary-400 to-primary-600">
                <svg class="w-24 h-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
            </div>
        @endif

        <!-- Badges Overlay -->
        <div class="absolute top-4 {{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }} flex flex-col gap-2">
            @if ($hall->is_featured)
                <span class="px-3 py-1.5 bg-amber-400 text-amber-900 rounded-full text-sm font-bold shadow-lg">
                    {{ __('halls.featured') }}
                </span>
            @endif
            @if ($hall->average_rating > 0)
                <span class="px-3 py-1.5 bg-white rounded-full text-sm font-bold shadow-lg flex items-center gap-1">
                    <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    {{ number_format($hall->average_rating, 1) }}
                    <span class="text-gray-600">({{ $hall->total_reviews }})</span>
                </span>
            @endif
        </div>
    </div>

    <!-- Hall Image Gallery -->
    @if ($hall->images()->active()->count() > 0)
        <div class="relative -mt-8 md:-mt-12">
            <div class="container px-4 mx-auto">
                <div class="overflow-hidden bg-white shadow-2xl rounded-2xl md:rounded-3xl">

                    {{-- Gallery Header (Optional) --}}
                    <div class="px-4 pt-4 md:px-6 md:pt-6">
                        <h2 class="text-lg font-bold text-gray-900 md:text-xl">
                            {{ __('halls.image_gallery') }}
                        </h2>
                        <p class="text-sm text-gray-600">
                            {{ __('halls.images_count', ['count' => $hall->images()->active()->count()]) }}
                        </p>
                    </div>

                    <!-- Main Swiper Slider -->
                    <div class="swiper hallGalleryMain">
                        <div class="swiper-wrapper">
                            @foreach ($hall->images()->active()->orderBy('order')->get() as $image)
                                <div class="swiper-slide">
                                    <div class="relative bg-gray-200 aspect-[4/3] md:aspect-[16/9] overflow-hidden">
                                        {{-- Lazy Loading Image --}}
                                        <img src="{{ asset('storage/' . $image->image_path) }}"
                                            alt="{{ $image->alt_text ?? (is_array($hall->name) ? $hall->name[app()->getLocale()] ?? $hall->name['en'] : $hall->name) }}"
                                            class="object-cover w-full h-full swiper-lazy">
                                        {{-- Loading Spinner --}}
                                        <div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div>

                                        {{-- Caption Overlay (if exists) --}}
                                        @if ($image->caption && !empty($image->caption[app()->getLocale()]))
                                            <div
                                                class="absolute bottom-0 left-0 right-0 px-4 py-3 text-white bg-gradient-to-t from-black/70 to-transparent md:px-6 md:py-4">
                                                <p class="text-sm md:text-base">
                                                    {{ $image->caption[app()->getLocale()] }}
                                                </p>
                                            </div>
                                        @endif

                                        {{-- Zoom Icon --}}
                                        <button onclick="openLightbox({{ $loop->index }})"
                                            class="absolute p-2 transition bg-white rounded-full shadow-lg top-4 {{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }} hover:bg-gray-100 group"
                                            aria-label="{{ __('halls.view_fullscreen') }}"
                                            title="{{ __('halls.view_fullscreen') }}">
                                            <svg class="w-5 h-5 text-gray-700 transition group-hover:scale-110"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Navigation Arrows (Desktop) --}}
                        <div class="hidden md:block">
                            <button
                                class="swiper-button-prev {{ app()->getLocale() === 'ar' ? '!right-4 !left-auto' : '!left-4' }} !text-white !w-12 !h-12 after:!text-2xl bg-black/30 hover:bg-black/50 rounded-full backdrop-blur-sm transition"
                                aria-label="{{ __('halls.previous_image') }}"></button>
                            <button
                                class="swiper-button-next {{ app()->getLocale() === 'ar' ? '!left-4 !right-auto' : '!right-4' }} !text-white !w-12 !h-12 after:!text-2xl bg-black/30 hover:bg-black/50 rounded-full backdrop-blur-sm transition"
                                aria-label="{{ __('halls.next_image') }}"></button>
                        </div>

                        {{-- Pagination Dots --}}
                        <div class="swiper-pagination !bottom-4"></div>
                    </div>

                    {{-- Thumbnail Navigation --}}
                    <div class="p-4 bg-gray-50 md:p-6">
                        <div class="swiper hallGalleryThumbs">
                            <div class="swiper-wrapper">
                                @foreach ($hall->images()->active()->orderBy('order')->get() as $image)
                                    <div class="swiper-slide">
                                        <div
                                            class="relative overflow-hidden transition border-2 border-transparent cursor-pointer aspect-square rounded-xl hover:border-primary-500">
                                            <img src="{{ $image->thumbnail_path ? asset('storage/' . $image->thumbnail_path) : asset('storage/' . $image->image_path) }}"
                                                alt="{{ $image->alt_text ?? '' }}"
                                                class="object-cover w-full h-full">
                                            {{-- Active Overlay --}}
                                            <div class="absolute inset-0 transition bg-primary-600/0"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Image Counter --}}
                        <div class="flex items-center justify-center gap-2 mt-4 text-sm text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="font-medium gallery-counter">1 /
                                {{ $hall->images()->active()->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- No Images Fallback --}}
        <div class="relative -mt-8 md:-mt-12">
            <div class="container px-4 mx-auto">
                <div class="p-8 text-center bg-white border border-gray-200 shadow-sm rounded-2xl md:rounded-3xl">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <p class="text-gray-600">{{ __('halls.no_images_available') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Lightbox Modal --}}
    <div id="lightbox" class="fixed inset-0 z-[100] hidden bg-black/95 backdrop-blur-sm">
        {{-- Close Button --}}
        <button onclick="closeLightbox()"
            class="absolute z-10 p-3 text-white transition rounded-full top-4 {{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }} hover:bg-white/10 group"
            aria-label="{{ __('halls.close') }}" title="{{ __('halls.close') }}">
            <svg class="w-8 h-8 transition group-hover:rotate-90" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                </path>
            </svg>
        </button>

        {{-- Lightbox Swiper --}}
        <div class="h-full swiper lightboxSwiper">
            <div class="items-center swiper-wrapper">
                @foreach ($hall->images()->active()->orderBy('order')->get() as $image)
                    <div class="flex items-center justify-center p-4 swiper-slide md:p-8">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->alt_text ?? '' }}"
                            class="object-contain max-w-full max-h-full rounded-lg shadow-2xl">
                    </div>
                @endforeach
            </div>

            {{-- Navigation --}}
            <button
                class="swiper-button-prev {{ app()->getLocale() === 'ar' ? '!right-4 !left-auto' : '!left-4' }} !text-white after:!text-3xl"
                aria-label="{{ __('halls.previous_image') }}"></button>
            <button
                class="swiper-button-next {{ app()->getLocale() === 'ar' ? '!left-4 !right-auto' : '!right-4' }} !text-white after:!text-3xl"
                aria-label="{{ __('halls.next_image') }}"></button>

            {{-- Counter --}}
            <div
                class="absolute z-10 px-4 py-2 text-white -translate-x-1/2 rounded-lg shadow-lg bottom-4 left-1/2 bg-black/50 backdrop-blur-sm">
                <span class="font-medium lightbox-counter">1 / {{ $hall->images()->active()->count() }}</span>
            </div>
        </div>
    </div>

    {{-- Lightbox Modal --}}
    <div id="lightbox" class="fixed inset-0 z-[100] hidden bg-black/95 backdrop-blur-sm">
        {{-- Close Button --}}
        <button onclick="closeLightbox()"
            class="absolute z-10 p-3 text-white transition rounded-full top-4 {{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }} hover:bg-white/10 group"
            aria-label="{{ __('halls.close') }}">
            <svg class="w-8 h-8 transition group-hover:rotate-90" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                </path>
            </svg>
        </button>

        {{-- Lightbox Swiper --}}
        <div class="h-full swiper lightboxSwiper">
            <div class="items-center swiper-wrapper">
                @foreach ($hall->images()->active()->orderBy('order')->get() as $image)
                    <div class="flex items-center justify-center p-4 swiper-slide md:p-8">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->alt_text ?? '' }}"
                            class="object-contain max-w-full max-h-full rounded-lg shadow-2xl">
                    </div>
                @endforeach
            </div>

            {{-- Navigation --}}
            <div
                class="swiper-button-prev {{ app()->getLocale() === 'ar' ? '!right-4 !left-auto' : '!left-4' }} !text-white after:!text-3xl">
            </div>
            <div
                class="swiper-button-next {{ app()->getLocale() === 'ar' ? '!left-4 !right-auto' : '!right-4' }} !text-white after:!text-3xl">
            </div>

            {{-- Counter --}}
            <div
                class="absolute z-10 px-4 py-2 text-white -translate-x-1/2 rounded-lg shadow-lg bottom-4 left-1/2 bg-black/50 backdrop-blur-sm">
                <span class="font-medium lightbox-counter">1 / {{ $hall->images()->active()->count() }}</span>
            </div>
        </div>
    </div>

    @php
        $locale       = app()->getLocale();
        $isRtl        = $locale === 'ar';

        // Address: prefer localized version for current locale
        $displayAddress = is_array($hall->address_localized)
            ? ($hall->address_localized[$locale] ?? $hall->address_localized['en'] ?? $hall->address)
            : $hall->address;

        // Google Maps URL: use stored value or build from coordinates
        $mapsUrl = $hall->google_maps_url
            ?: 'https://www.google.com/maps?q=' . $hall->latitude . ',' . $hall->longitude;

        // Instagram: normalise handle/URL → full URL & display text
        $instagramRaw     = $hall->instagram;
        $instagramUrl     = $instagramRaw
            ? (str_starts_with($instagramRaw, 'http')
                ? $instagramRaw
                : 'https://instagram.com/' . ltrim($instagramRaw, '@'))
            : null;
        $instagramDisplay = $instagramRaw
            ? (str_starts_with($instagramRaw, 'http')
                ? trim(parse_url($instagramRaw, PHP_URL_PATH), '/')
                : '@' . ltrim($instagramRaw, '@'))
            : null;

        // WhatsApp: digits-only number → wa.me link
        $whatsappNum = preg_replace('/[^0-9]/', '', $hall->whatsapp ?? '');
        $whatsappUrl = $whatsappNum ? 'https://api.whatsapp.com/send?phone=' . $whatsappNum : null;

        // Function hours helpers
        $todayDay = strtolower(now()->format('l'));   // e.g. 'monday'
        $weekDays = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        $hoursMap = collect($hall->function_hours ?? [])->keyBy('day');

        // Hall name for map popup
        $hallName = is_array($hall->name)
            ? ($hall->name[$locale] ?? $hall->name['en'] ?? '')
            : $hall->name;
    @endphp

    <div class="container px-4 py-6 pb-32 mx-auto lg:pb-8">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Main Content -->
            <div class="space-y-6 lg:col-span-2">

                <!-- Hall Title & Location -->
                <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                    <h1 class="mb-3 text-2xl font-bold text-gray-900 md:text-3xl">
                        {{ is_array($hall->name) ? $hall->name[app()->getLocale()] ?? $hall->name['en'] : $hall->name }}
                    </h1>

                    <div class="flex items-center mb-4 text-gray-600">
                        <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>
                            {{ is_array($hall->city->name) ? $hall->city->name[app()->getLocale()] ?? $hall->city->name['en'] : $hall->city->name }},
                            {{ $hall->address_localized[$locale] ?? $hall->address_localized['en'] ?? $hall->address_localized }}
                        </span>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-2 gap-3 p-4 sm:grid-cols-4 bg-gray-50 rounded-xl">

                        {{-- Capacity --}}
                        <div class="flex flex-col items-center gap-1.5 p-3 bg-white rounded-xl shadow-sm">
                            <span class="flex items-center justify-center bg-gray-100 rounded-full w-9 h-9">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </span>
                            <div class="text-base font-bold leading-none text-gray-900">{{ $hall->capacity_min }}–{{ $hall->capacity_max }}</div>
                            <div class="text-xs text-gray-500">{{ __('halls.guests_count') }}</div>
                        </div>

                        {{-- Area --}}
                        <div class="flex flex-col items-center gap-1.5 p-3 bg-white rounded-xl shadow-sm">
                            <span class="flex items-center justify-center bg-gray-100 rounded-full w-9 h-9">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/>
                                </svg>
                            </span>
                            <div class="text-base font-bold leading-none text-gray-900">{{ $hall->area }}</div>
                            <div class="text-xs text-gray-500">{{ __('halls.square_meters') }}</div>
                        </div>

                        {{-- Price --}}
                        <div class="flex flex-col items-center gap-1.5 p-3 bg-white rounded-xl shadow-sm">
                            <span class="flex items-center justify-center bg-gray-100 rounded-full w-9 h-9">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            <div class="text-base font-bold leading-none text-primary-600">{{ number_format($hall->price_per_slot, 3) }}</div>
                            <div class="text-xs text-gray-500 flex items-center gap-0.5">
                                <img src="{{ asset('images/Medium.svg') }}" alt="OMR" class="inline w-4 h-4">
                                {{ __('halls.per_day') }}
                            </div>
                        </div>

                        {{-- Bookings --}}
                        <div class="flex flex-col items-center gap-1.5 p-3 bg-white rounded-xl shadow-sm">
                            <span class="flex items-center justify-center bg-gray-100 rounded-full w-9 h-9">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <div class="text-base font-bold leading-none text-gray-900">{{ $hall->bookings->count() }}</div>
                            <div class="text-xs text-gray-500">{{ __('halls.total_bookings') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                    <h3 class="flex items-center gap-2 mb-4 text-xl font-bold text-gray-900">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('halls.about_hall') }}
                    </h3>
                    <div class="leading-relaxed prose text-gray-700 max-w-none">
                        @php
                            $description = is_array($hall->description)
                                ? $hall->description[app()->getLocale()] ?? ($hall->description['en'] ?? '')
                                : $hall->description;
                        @endphp
                        {!! $description !!}
                    </div>
                </div>

                <!-- Perfect For (Hall Types) -->
                @if($hall->hallTypes->isNotEmpty())
                    <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                        <h3 class="flex items-center gap-2 mb-4 text-xl font-bold text-gray-900">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                            {{ __('halls.perfect_for') }}
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($hall->hallTypes as $type)
                                @php
                                    $typeName = is_array($type->name)
                                        ? ($type->name[app()->getLocale()] ?? $type->name['en'] ?? '')
                                        : $type->name;
                                    $typeDesc = is_array($type->description)
                                        ? ($type->description[app()->getLocale()] ?? $type->description['en'] ?? null)
                                        : $type->description;
                                @endphp
                                <div class="group relative inline-flex items-center gap-2 px-4 py-2 rounded-full border text-sm font-medium transition
                                    {{ $type->color ? '' : 'bg-primary-50 border-primary-200 text-primary-700' }}"
                                    @if($type->color)
                                    style="background-color: {{ $type->color }}1a; border-color: {{ $type->color }}4d; color: {{ $type->color }};"
                                    @endif
                                    @if($typeDesc) title="{{ $typeDesc }}" @endif>
                                    @if($type->icon)
                                        <x-dynamic-component :component="$type->icon" class="flex-shrink-0 w-4 h-4" />
                                    @else
                                        <svg class="flex-shrink-0 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endif
                                    {{ $typeName }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Features & Amenities -->
                @if ($features && count($features) > 0)
                    <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                        <h3 class="flex items-center gap-2 mb-4 text-xl font-bold text-gray-900">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('halls.features_amenities') }}
                        </h3>
                        <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                            @foreach ($features as $feature)
                                <div
                                    class="flex items-center gap-3 p-3 border border-green-100 bg-green-50 rounded-xl">
                                    {{-- <svg class="flex-shrink-0 w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg> --}}
                                    <x-dynamic-component :component="$feature->icon" class="w-5 h-5 text-green-500" />
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ is_array($feature->name) ? $feature->name[app()->getLocale()] ?? $feature->name['en'] : $feature->name }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Recent Reviews -->
                @php
                    $approvedReviews = $hall->reviews->where('is_approved', true);
                    $reviewCount     = $approvedReviews->count();
                @endphp
                @if ($reviewCount > 0)
                    <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">

                        {{-- Section heading --}}
                        <h3 class="flex items-center gap-2 mb-5 text-xl font-bold text-gray-900">
                            <svg class="w-6 h-6 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24l5.46 4.73L5.82 21z"/>
                            </svg>
                            {{ __('review.customer.recent_reviews') }}
                            @if ($hall->average_rating > 0)
                                <span class="flex items-center gap-1 text-sm font-semibold text-gray-600 ms-auto">
                                    <span class="text-base font-bold text-amber-400">{{ number_format($hall->average_rating, 1) }}</span>
                                    <span class="text-gray-400">/</span>
                                    <span>{{ __('review.customer.out_of') }}</span>
                                    <span class="text-xs font-normal text-gray-400 ms-1">
                                        ({{ trans_choice('review.customer.based_on', $hall->total_reviews, ['count' => $hall->total_reviews]) }})
                                    </span>
                                </span>
                            @endif
                        </h3>

                        {{-- Review cards --}}
                        <div class="space-y-4">
                            @foreach ($approvedReviews->sortByDesc('created_at') as $review)
                                @php
                                    $reviewerName  = $review->user->name ?? 'Guest';
                                    $initials      = mb_strtoupper(mb_substr($reviewerName, 0, 1));
                                    $hasSubRatings = $review->cleanliness_rating || $review->service_rating
                                                     || $review->value_rating || $review->location_rating;
                                    $avatarColors  = ['bg-primary-100 text-primary-700', 'bg-amber-100 text-amber-700',
                                                      'bg-emerald-100 text-emerald-700', 'bg-purple-100 text-purple-700',
                                                      'bg-rose-100 text-rose-700'];
                                    $colorClass    = $avatarColors[$review->id % count($avatarColors)];
                                @endphp

                                <div class="p-4 border border-gray-100 rounded-xl bg-gray-50">

                                    {{-- Header row: avatar · name · stars · date --}}
                                    <div class="flex items-start gap-3">

                                        {{-- Avatar --}}
                                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full text-sm font-bold {{ $colorClass }}">
                                            {{ $initials }}
                                        </div>

                                        <div class="flex-1 min-w-0">

                                            {{-- Name + badges --}}
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="text-sm font-semibold text-gray-900">
                                                    {{ $reviewerName }}
                                                </span>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ __('review.customer.verified_guest') }}
                                                </span>
                                                @if ($review->is_featured)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24l5.46 4.73L5.82 21z"/>
                                                        </svg>
                                                        {{ __('review.customer.featured_review') }}
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Stars + date --}}
                                            <div class="flex items-center gap-2 mt-1">
                                                <div class="flex items-center gap-0.5">
                                                    @for ($s = 1; $s <= 5; $s++)
                                                        <svg class="w-3.5 h-3.5 {{ $s <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}"
                                                             fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24l5.46 4.73L5.82 21z"/>
                                                        </svg>
                                                    @endfor
                                                </div>
                                                <span class="text-xs text-gray-400">
                                                    {{ $review->created_at->format('d M Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Sub-ratings --}}
                                    @if ($hasSubRatings)
                                        <div class="grid grid-cols-2 gap-x-6 gap-y-1.5 mt-3 sm:grid-cols-4">
                                            @foreach ([
                                                'cleanliness' => $review->cleanliness_rating,
                                                'service'     => $review->service_rating,
                                                'value'       => $review->value_rating,
                                                'location'    => $review->location_rating,
                                            ] as $key => $val)
                                                @if ($val)
                                                    <div>
                                                        <div class="flex items-center justify-between mb-0.5">
                                                            <span class="text-xs text-gray-500">{{ __('review.customer.' . $key) }}</span>
                                                            <span class="text-xs font-semibold text-gray-700">{{ $val }}/5</span>
                                                        </div>
                                                        <div class="h-1 overflow-hidden bg-gray-200 rounded-full">
                                                            <div class="h-full rounded-full bg-amber-400" style="width: {{ ($val / 5) * 100 }}%"></div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Comment --}}
                                    @if ($review->comment)
                                        <div x-data="{ expanded: false }" class="mt-3">
                                            <p class="text-sm leading-relaxed text-gray-700"
                                               :class="!expanded && '{{ mb_strlen($review->comment) > 200 ? 'line-clamp-3' : '' }}'">
                                                {{ $review->comment }}
                                            </p>
                                            @if (mb_strlen($review->comment) > 200)
                                                <button @click="expanded = !expanded"
                                                        class="mt-1 text-xs font-medium text-primary-600 hover:underline">
                                                    <span x-text="expanded ? '{{ __('review.customer.show_less') }}' : '{{ __('review.customer.read_more') }}'"></span>
                                                </button>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Owner response --}}
                                    @if ($review->owner_response)
                                        <div class="mt-3 ms-3 ps-3 border-s-2 border-primary-200">
                                            <div class="flex items-center gap-1.5 mb-1">
                                                <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                                </svg>
                                                <span class="text-xs font-semibold text-primary-700">{{ __('review.customer.owner_response') }}</span>
                                                @if ($review->owner_response_at)
                                                    <span class="text-xs text-gray-400">· {{ $review->owner_response_at->format('d M Y') }}</span>
                                                @endif
                                            </div>
                                            <p class="text-xs leading-relaxed text-gray-600">{{ $review->owner_response }}</p>
                                        </div>
                                    @endif

                                </div>
                            @endforeach
                        </div>

                    </div>
                @endif

                <!-- Terms & Conditions -->
                @php
                    $terms = is_array($hall->terms_and_conditions)
                        ? ($hall->terms_and_conditions[app()->getLocale()] ?? $hall->terms_and_conditions['en'] ?? null)
                        : $hall->terms_and_conditions;
                @endphp
                @if(!empty($terms))
                    <div x-data="{ open: false }" class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-2xl">
                        <button @click="open = !open"
                            class="flex items-center justify-between w-full p-6 text-start">
                            <h3 class="flex items-center gap-2 text-xl font-bold text-gray-900">
                                <svg class="flex-shrink-0 w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                {{ __('halls.terms_and_conditions') }}
                            </h3>
                            <svg class="flex-shrink-0 w-5 h-5 text-gray-400 transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="px-6 pb-6">
                            <div class="p-4 border border-gray-100 rounded-xl bg-gray-50">
                                <div class="leading-relaxed prose-sm prose text-gray-700 max-w-none">
                                    {!! $terms !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Extra Services -->
                @if ($hall->activeExtraServices->count() > 0)
                    <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                        <h3 class="flex items-center gap-2 mb-4 text-xl font-bold text-gray-900">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            {{ __('halls.available_services') }}
                        </h3>
                        <div class="space-y-3">
                            @foreach ($hall->activeExtraServices as $service)
                                <div
                                    class="flex items-center justify-between p-4 border border-blue-100 bg-blue-50 rounded-xl">
                                    <div class="flex-1">
                                        <div class="mb-1 font-semibold text-gray-900">
                                            {{ is_array($service->name) ? $service->name[app()->getLocale()] ?? $service->name['en'] : $service->name }}
                                        </div>
                                        @php
                                            $serviceDesc = is_array($service->description)
                                                ? $service->description[app()->getLocale()] ??
                                                    ($service->description['en'] ?? '')
                                                : $service->description;
                                        @endphp
                                        @if ($serviceDesc)
                                            <div class="text-sm text-gray-600">{!! $serviceDesc !!}</div>
                                        @endif
                                    </div>
                                    <div
                                        class="text-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} {{ app()->getLocale() === 'ar' ? 'mr-4' : 'ml-4' }}">
                                        <div class="font-bold text-primary-600 whitespace-nowrap">
                                            {{ number_format($service->price, 3) }} <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                        class="inline w-5 h-5 -mt-1"></div>
                                        <div class="text-xs text-gray-500">{{ $service->unit }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ═══ FAQ ═══ --}}
                @if (!empty($hall->faq) && count($hall->faq) > 0)
                    <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                        <h3 class="flex items-center gap-2 mb-5 text-xl font-bold text-gray-900">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('halls.faq_title') }}
                        </h3>
                        <div class="space-y-3" x-data="{ open: null }">
                            @foreach ($hall->faq as $index => $item)
                                @php
                                    $locale = app()->getLocale();
                                    $question = is_array($item['question'] ?? null)
                                        ? ($item['question'][$locale] ?? $item['question']['en'] ?? '')
                                        : ($item['question'] ?? '');
                                    $answer = is_array($item['answer'] ?? null)
                                        ? ($item['answer'][$locale] ?? $item['answer']['en'] ?? '')
                                        : ($item['answer'] ?? '');
                                @endphp
                                @if ($question && $answer)
                                    <div class="overflow-hidden border border-gray-200 rounded-xl">
                                        <button
                                            type="button"
                                            class="flex items-center justify-between w-full gap-3 px-5 py-4 text-left text-gray-900 transition-colors hover:bg-gray-50"
                                            @click="open = open === {{ $index }} ? null : {{ $index }}"
                                            :aria-expanded="open === {{ $index }}"
                                        >
                                            <span class="font-medium text-gray-900">{{ $question }}</span>
                                            <svg
                                                class="flex-shrink-0 w-5 h-5 transition-transform duration-200 text-primary-600"
                                                :class="{ 'rotate-180': open === {{ $index }} }"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                        <div
                                            x-show="open === {{ $index }}"
                                            x-transition:enter="transition ease-out duration-150"
                                            x-transition:enter-start="opacity-0 -translate-y-1"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-100"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 -translate-y-1"
                                            class="px-5 pb-4 text-sm leading-relaxed text-gray-600 border-t border-gray-100"
                                        >
                                            <div class="pt-3">{{ $answer }}</div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ═══ Hall Info Card: Contact · Map · Hours (mobile) ═══ --}}
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm lg:hidden rounded-2xl">

                    {{-- ── Address ── --}}
                    {{-- <div class="p-5 border-b border-gray-100">
                        <h4 class="flex items-center gap-2 mb-3 text-xs font-bold tracking-widest text-gray-500 uppercase">
                            <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ __('halls.address') }}
                        </h4>
                        <p class="mb-3 text-sm leading-relaxed text-gray-700">{{ $displayAddress }}</p>
                        <div class="flex items-center gap-2">
                            <button onclick="hallCopyText({{ json_encode($displayAddress) }}, this)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 transition bg-gray-100 rounded-lg hover:bg-gray-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                {{ __('halls.copy') }}
                            </button>
                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium transition rounded-lg text-primary-600 bg-primary-50 hover:bg-primary-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                                {{ __('halls.get_directions') }}
                            </a>
                        </div>
                    </div> --}}

                    {{-- ── Contact Details ── --}}
                    <div class="p-5 border-b border-gray-100">
                        <h4 class="flex items-center gap-2 mb-3 text-xs font-bold tracking-widest text-gray-500 uppercase">
                            <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ __('halls.contact_details') }}
                        </h4>
                        <div class="space-y-2">
                            @if($hall->phone)
                            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50">
                                <span class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </span>
                                <a href="tel:{{ $hall->phone }}" class="flex-1 min-w-0 text-sm font-medium text-gray-700 truncate transition hover:text-blue-600">{{ $hall->phone }}</a>
                                <button onclick="hallCopyText({{ json_encode($hall->phone) }}, this)" title="{{ __('halls.copy') }}" class="flex-shrink-0 p-1.5 text-gray-400 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                            </div>
                            @endif
                            @if($hall->whatsapp && $whatsappUrl)
                            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50">
                                <span class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg">
                                    <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </span>
                                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="flex-1 min-w-0 text-sm font-medium text-gray-700 truncate transition hover:text-green-600">{{ $hall->whatsapp }}</a>
                                <button onclick="hallCopyText({{ json_encode($hall->whatsapp) }}, this)" title="{{ __('halls.copy') }}" class="flex-shrink-0 p-1.5 text-gray-400 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                            </div>
                            @endif
                            @if($hall->email)
                            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50">
                                <span class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg ">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </span>
                                <a href="mailto:{{ $hall->email }}" class="flex-1 min-w-0 text-sm font-medium text-gray-700 truncate transition hover:text-amber-600">{{ $hall->email }}</a>
                                <button onclick="hallCopyText({{ json_encode($hall->email) }}, this)" title="{{ __('halls.copy') }}" class="flex-shrink-0 p-1.5 text-gray-400 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                            </div>
                            @endif
                            @if($instagramUrl)
                            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50">
                                <span class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg">
                                    <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                </span>
                                <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" class="flex-1 min-w-0 text-sm font-medium text-gray-700 truncate transition hover:text-pink-600">{{ $instagramDisplay }}</a>
                                <button onclick="hallCopyText({{ json_encode($instagramUrl) }}, this)" title="{{ __('halls.copy') }}" class="flex-shrink-0 p-1.5 text-gray-400 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- ── Map (mobile) ── --}}
                    @if($hall->latitude && $hall->longitude)
                    <div class="p-5 border-b border-gray-100">
                        <h4 class="flex items-center gap-2 mb-3 text-xs font-bold tracking-widest text-gray-500 uppercase">
                            <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            {{ __('halls.location_info') }}
                        </h4>
                        <div id="hall-map-mobile" class="overflow-hidden bg-gray-100 border border-gray-200 h-52 rounded-xl">
                            <div class="flex items-center justify-center h-full gap-2 text-sm text-gray-400">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                {{ __('halls.loading_map') }}
                            </div>
                        </div>
                        <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer"
                            class="flex items-center justify-center w-full gap-2 px-4 py-2.5 mt-3 text-sm font-medium text-gray-700 transition border border-gray-200 rounded-xl hover:bg-gray-50">
                            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ __('halls.get_directions') }}
                        </a>
                    </div>
                    @endif

                    {{-- ── Opening Hours (mobile) ── --}}
                    @if($hall->is_24_hours || !empty($hall->function_hours))
                    <div class="p-5">
                        <h4 class="flex items-center gap-2 mb-3 text-xs font-bold tracking-widest text-gray-500 uppercase">
                            <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ __('halls.opening_hours') }}
                        </h4>
                        @if($hall->is_24_hours)
                            <div class="flex items-center gap-3 p-3 border border-green-100 rounded-xl bg-green-50">
                                <span class="inline-block w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                <span class="text-sm font-semibold text-green-700">{{ __('halls.open_24_hours') }}</span>
                            </div>
                        @else
                            <div class="grid grid-cols-2 gap-1 sm:grid-cols-1">
                                @foreach($weekDays as $day)
                                    @php $dayData = $hoursMap->get($day); $isToday = ($day === $todayDay); @endphp
                                    <div class="flex items-center justify-between px-3 py-1.5 rounded-lg {{ $isToday ? 'bg-primary-50 border border-primary-100' : 'hover:bg-gray-50' }} transition">
                                        <div class="flex items-center gap-1.5">
                                            @if($isToday)<span class="inline-block w-1.5 h-1.5 rounded-full bg-primary-600"></span>@endif
                                            <span class="text-sm {{ $isToday ? 'font-bold text-primary-700' : 'text-gray-600' }}">{{ __('halls.day_' . $day) }}</span>
                                            @if($isToday)<span class="text-xs text-primary-500">({{ __('halls.today') }})</span>@endif
                                        </div>
                                        @if(!$dayData || ($dayData['is_closed'] ?? false))
                                            <span class="text-xs font-medium {{ $isToday ? 'text-red-600' : 'text-red-400' }}">{{ __('halls.closed') }}</span>
                                        @else
                                            <span class="text-xs font-medium {{ $isToday ? 'font-bold text-primary-700' : 'text-gray-600' }}">{{ $dayData['open_time'] }} – {{ $dayData['close_time'] }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @if($hall->special_hours_note)
                            <div class="flex items-start gap-2 p-3 mt-3 border rounded-xl bg-amber-50 border-amber-100">
                                <svg class="flex-shrink-0 w-4 h-4 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-xs text-amber-700">{{ $hall->special_hours_note }}</p>
                            </div>
                        @endif
                    </div>
                    @endif

                </div>
                {{-- ═══ End Hall Info Card (mobile) ═══ --}}
            </div>

            <!-- Sidebar (Desktop Only) -->
            <div class="hidden lg:block lg:col-span-1">
                <div class="p-6 bg-white border border-gray-200 shadow-lg booking-card rounded-2xl">
                    <!-- Price -->
                    <div class="pb-6 mb-6 text-center border-b border-gray-200">
                        <div class="mb-2 text-4xl font-bold text-primary-600">
                            {{ number_format($hall->price_per_slot, 3) }} <span class="text-xl"><img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                        class="inline w-6 h-6 -mt-3"></span>
                        </div>
                        <div class="text-sm text-gray-600">{{ __('halls.per_day') }}</div>
                    </div>

                    <!-- Book Button with Guest Option -->
                    @auth
                        {{-- Logged in users go directly to booking --}}
                        <a href="{{ route('customer.book', $hall->slug) }}"
                            class="flex items-center justify-center w-full gap-2 mb-6 font-bold text-center text-white transition shadow-lg h-14 bg-primary-600 rounded-xl hover:bg-primary-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            {{ __('halls.book_now') }}
                        </a>
                    @else
                        {{-- Guest users see the choice modal --}}
                        <button type="button" x-data @click="$dispatch('open-booking-modal')"
                            class="flex items-center justify-center w-full gap-2 mb-6 font-bold text-center text-white transition shadow-lg cursor-pointer h-14 bg-primary-600 rounded-xl hover:bg-primary-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            {{ __('halls.book_now') }}
                        </button>
                    @endauth

                    <!-- Benefits -->
                    <div class="mb-6 space-y-3">
                        <div class="flex items-center gap-3">
                            <svg class="flex-shrink-0 w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-700">{{ __('halls.instant_confirmation') }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="flex-shrink-0 w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-700">{{ __('halls.secure_payment') }}</span>
                        </div>
                    </div>

                    <!-- Contact Owner -->
                    <div class="pt-6 border-t border-gray-200">
                        <h4 class="mb-4 text-lg font-bold">{{ __('halls.contact_owner') }}</h4>
                        <div class="space-y-3">
                            <a href="tel:{{ $hall->owner->phone ?? '' }}"
                                class="flex items-center gap-3 p-3 transition bg-gray-50 rounded-xl hover:bg-gray-100">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                    </path>
                                </svg>
                                <span
                                    class="text-sm font-medium text-gray-700">{{ $hall->owner->phone ?? __('halls.not_available') }}</span>
                            </a>
                            <a href="mailto:{{ $hall->owner->email }}"
                                class="flex items-center gap-3 p-3 transition bg-gray-50 rounded-xl hover:bg-gray-100">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span
                                    class="text-sm font-medium text-gray-700 truncate">{{ $hall->owner->email }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ═══ Hall Info Card: Contact · Map · Hours ═══ --}}
                <div class="p-6 mt-4 bg-white border border-gray-200 shadow-sm booking-card rounded-2xl">

                    {{-- ── Address ── --}}
                    {{-- <div class="p-5 border-b border-gray-100">
                        <h4 class="flex items-center gap-2 mb-3 text-xs font-bold tracking-widest text-gray-500 uppercase">
                            <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ __('halls.address') }}
                        </h4>
                        <p class="mb-3 text-sm leading-relaxed text-gray-700">{{ $displayAddress }}</p>
                        <div class="flex items-center gap-2">
                            <button onclick="hallCopyText({{ json_encode($displayAddress) }}, this)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 transition bg-gray-100 rounded-lg hover:bg-gray-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                {{ __('halls.copy') }}
                            </button>
                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium transition rounded-lg text-primary-600 bg-primary-50 hover:bg-primary-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                                {{ __('halls.get_directions') }}
                            </a>
                        </div>
                    </div> --}}

                    {{-- ── Contact Details ── --}}
                    <div class="p-5 border-b border-gray-100">
                        <h4 class="flex items-center gap-2 mb-3 text-xs font-bold tracking-widest text-gray-500 uppercase">
                            <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ __('halls.contact_details') }}
                        </h4>
                        <div class="space-y-2">
                            @if($hall->phone)
                            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50">
                                <span class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </span>
                                <a href="tel:{{ $hall->phone }}" class="flex-1 min-w-0 text-sm font-medium text-gray-700 truncate transition hover:text-blue-600">{{ $hall->phone }}</a>
                                <button onclick="hallCopyText({{ json_encode($hall->phone) }}, this)" title="{{ __('halls.copy') }}" class="flex-shrink-0 p-1.5 text-gray-400 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                            </div>
                            @endif
                            @if($hall->whatsapp && $whatsappUrl)
                            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50">
                                <span class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg">
                                    <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </span>
                                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="flex-1 min-w-0 text-sm font-medium text-gray-700 truncate transition hover:text-green-600">{{ $hall->whatsapp }}</a>
                                <button onclick="hallCopyText({{ json_encode($hall->whatsapp) }}, this)" title="{{ __('halls.copy') }}" class="flex-shrink-0 p-1.5 text-gray-400 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                            </div>
                            @endif
                            @if($hall->email)
                            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50">
                                <span class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg ">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </span>
                                <a href="mailto:{{ $hall->email }}" class="flex-1 min-w-0 text-sm font-medium text-gray-700 truncate transition hover:text-amber-600">{{ $hall->email }}</a>
                                <button onclick="hallCopyText({{ json_encode($hall->email) }}, this)" title="{{ __('halls.copy') }}" class="flex-shrink-0 p-1.5 text-gray-400 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                            </div>
                            @endif
                            @if($instagramUrl)
                            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50">
                                <span class="flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg">
                                    <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                </span>
                                <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" class="flex-1 min-w-0 text-sm font-medium text-gray-700 truncate transition hover:text-pink-600"> {{ $instagramDisplay }}</a>
                                <button onclick="hallCopyText({{ json_encode($instagramUrl) }}, this)" title="{{ __('halls.copy') }}" class="flex-shrink-0 p-1.5 text-gray-400 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- ── Map (desktop) ── --}}
                    @if($hall->latitude && $hall->longitude)
                    <div class="p-5 border-b border-gray-100">
                        <h4 class="flex items-center gap-2 mb-3 text-xs font-bold tracking-widest text-gray-500 uppercase">
                            <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            {{ __('halls.location_info') }}
                        </h4>
                        <div id="hall-map-desktop" class="h-48 overflow-hidden bg-gray-100 border border-gray-200 rounded-xl">
                            <div class="flex items-center justify-center h-full gap-2 text-sm text-gray-400">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                {{ __('halls.loading_map') }}
                            </div>
                        </div>
                        <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer"
                            class="flex items-center justify-center w-full gap-2 px-4 py-2.5 mt-3 text-sm font-medium text-gray-700 transition border border-gray-200 rounded-xl hover:bg-gray-50">
                            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ __('halls.get_directions') }}
                        </a>
                    </div>
                    @endif

                    {{-- ── Opening Hours (desktop) ── --}}
                    @if($hall->is_24_hours || !empty($hall->function_hours))
                    <div class="p-5">
                        <h4 class="flex items-center gap-2 mb-3 text-xs font-bold tracking-widest text-gray-500 uppercase">
                            <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ __('halls.opening_hours') }}
                        </h4>
                        @if($hall->is_24_hours)
                            <div class="flex items-center gap-3 p-3 border border-green-100 rounded-xl bg-green-50">
                                <span class="inline-block w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                <span class="text-sm font-semibold text-green-700">{{ __('halls.open_24_hours') }}</span>
                            </div>
                        @else
                            <div class="space-y-0.5">
                                @foreach($weekDays as $day)
                                    @php $dayData = $hoursMap->get($day); $isToday = ($day === $todayDay); @endphp
                                    <div class="flex items-center justify-between px-3 py-1.5 rounded-lg {{ $isToday ? 'bg-primary-50 border border-primary-100' : 'hover:bg-gray-50' }} transition">
                                        <div class="flex items-center gap-1.5">
                                            @if($isToday)<span class="inline-block w-1.5 h-1.5 rounded-full bg-primary-600"></span>@endif
                                            <span class="text-sm {{ $isToday ? 'font-bold text-primary-700' : 'text-gray-600' }}">{{ __('halls.day_' . $day) }}</span>
                                            @if($isToday)<span class="text-xs text-primary-500">({{ __('halls.today') }})</span>@endif
                                        </div>
                                        @if(!$dayData || ($dayData['is_closed'] ?? false))
                                            <span class="text-xs font-medium {{ $isToday ? 'text-red-600' : 'text-red-400' }}">{{ __('halls.closed') }}</span>
                                        @else
                                            <span class="text-xs font-medium {{ $isToday ? 'font-bold text-primary-700' : 'text-gray-600' }}">{{ $dayData['open_time'] }} – {{ $dayData['close_time'] }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @if($hall->special_hours_note)
                            <div class="flex items-start gap-2 p-3 mt-3 border rounded-xl bg-amber-50 border-amber-100">
                                <svg class="flex-shrink-0 w-4 h-4 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-xs text-amber-700">{{ $hall->special_hours_note }}</p>
                            </div>
                        @endif
                    </div>
                    @endif

                </div>
                {{-- ═══ End Hall Info Card ═══ --}}

            </div>
        </div>

        <!-- Similar Halls -->
        @if ($similarHalls->count() > 0)
            <div class="mt-12">
                <h2 class="mb-6 text-2xl font-bold text-gray-900 md:text-3xl">{{ __('halls.similar_halls') }}</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 md:gap-6">
                    @foreach ($similarHalls as $similar)
                        <a href="{{ route('customer.halls.show', $similar->slug) }}?lang={{ app()->getLocale() }}"
                            class="block overflow-hidden transition bg-white border border-gray-200 shadow-sm hall-card rounded-2xl hover:shadow-xl">
                            <div class="h-48 overflow-hidden bg-gray-200">
                                @if ($similar->featured_image)
                                    <img src="{{ asset('storage/' . $similar->featured_image) }}"
                                        alt="{{ is_array($similar->name) ? $similar->name[app()->getLocale()] ?? $similar->name['en'] : $similar->name }}"
                                        class="object-cover w-full h-full">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-primary-100 to-primary-200"></div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="mb-2 font-bold text-gray-900 line-clamp-1">
                                    {{ is_array($similar->name) ? $similar->name[app()->getLocale()] ?? $similar->name['en'] : $similar->name }}
                                </h3>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span
                                            class="text-xl font-bold text-primary-600">{{ number_format($similar->price_per_slot, 3) }}</span>
                                        <span class="text-sm text-gray-600"> <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                        class="inline w-5 h-5 -mt-1"></span>
                                    </div>
                                    <span class="text-sm font-medium text-primary-600">{{ __('halls.view_details') }}
                                        →</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Mobile Bottom Booking Bar -->
    <div class="lg:hidden booking-card glass-morphism">
        <div class="container px-4 py-4 mx-auto">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <div class="mb-1 text-xs text-gray-600">{{ __('halls.price_per_day') }}</div>
                    <div class="text-2xl font-bold text-primary-600">
                        {{ number_format($hall->price_per_slot, 3) }} <span class="text-sm"><img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                        class="inline w-5 h-5 -mt-1"></span>
                    </div>
                </div>
                @auth
                    <a href="{{ route('customer.book', $hall->slug) }}"
                        class="flex items-center justify-center flex-1 h-12 max-w-xs gap-2 font-bold text-center text-white transition shadow-lg bg-primary-600 rounded-xl hover:bg-primary-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        {{ __('halls.book_now') }}
                    </a>
                @else
                    <button type="button" x-data @click="$dispatch('open-booking-modal')"
                        class="flex items-center justify-center flex-1 h-12 max-w-xs gap-2 font-bold text-center text-white transition shadow-lg cursor-pointer bg-primary-600 rounded-xl hover:bg-primary-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        {{ __('halls.book_now') }}
                    </button>
                @endauth
            </div>
        </div>
    </div>

    {{-- Swiper JS --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Thumbnail Swiper
            const thumbsSwiper = new Swiper('.hallGalleryThumbs', {
                spaceBetween: 12,
                slidesPerView: 4,
                freeMode: true,
                watchSlidesProgress: true,
                breakpoints: {
                    640: {
                        slidesPerView: 5,
                        spaceBetween: 16,
                    },
                    768: {
                        slidesPerView: 6,
                        spaceBetween: 16,
                    },
                    1024: {
                        slidesPerView: 8,
                        spaceBetween: 16,
                    },
                },
            });

            // Initialize Main Gallery Swiper
            const mainSwiper = new Swiper('.hallGalleryMain', {
                spaceBetween: 0,
                lazy: {
                    loadPrevNext: true,
                    loadPrevNextAmount: 2,
                },
                navigation: {
                    nextEl: '.hallGalleryMain .swiper-button-next',
                    prevEl: '.hallGalleryMain .swiper-button-prev',
                },
                pagination: {
                    el: '.hallGalleryMain .swiper-pagination',
                    clickable: true,
                },
                thumbs: {
                    swiper: thumbsSwiper,
                },
                on: {
                    slideChange: function() {
                        updateCounter(this.activeIndex + 1, this.slides.length);
                    }
                },
                keyboard: {
                    enabled: true,
                },
                loop: false,
            });

            // Initialize Lightbox Swiper
            const lightboxSwiper = new Swiper('.lightboxSwiper', {
                spaceBetween: 20,
                navigation: {
                    nextEl: '.lightboxSwiper .swiper-button-next',
                    prevEl: '.lightboxSwiper .swiper-button-prev',
                },
                keyboard: {
                    enabled: true,
                },
                on: {
                    slideChange: function() {
                        updateLightboxCounter(this.activeIndex + 1, this.slides.length);
                    }
                },
            });

            // Sync main and lightbox swipers
            window.mainSwiper = mainSwiper;
            window.lightboxSwiper = lightboxSwiper;

            function updateCounter(current, total) {
                const counter = document.querySelector('.gallery-counter');
                if (counter) {
                    counter.textContent = `${current} / ${total}`;
                }
            }

            function updateLightboxCounter(current, total) {
                const counter = document.querySelector('.lightbox-counter');
                if (counter) {
                    counter.textContent = `${current} / ${total}`;
                }
            }
        });

        // Lightbox Functions
        function openLightbox(index) {
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';

            // Sync to the same slide
            if (window.lightboxSwiper) {
                window.lightboxSwiper.slideTo(index, 0);
            }
        }

        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close lightbox on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
            }
        });

        // Close lightbox on background click
        document.getElementById('lightbox')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });
    </script>

    {{-- Leaflet JS --}}
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js"></script>

    <script>
        // ── Copy to clipboard ───────────────────────────────────────────────────
        function hallCopyText(text, btn) {
            const originalHTML = btn.innerHTML;
            const checkIcon = '<svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';

            const finish = () => {
                btn.innerHTML = checkIcon;
                setTimeout(() => { btn.innerHTML = originalHTML; }, 2000);
            };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(finish).catch(() => fallbackCopy(text, finish));
            } else {
                fallbackCopy(text, finish);
            }
        }

        function fallbackCopy(text, callback) {
            const el = document.createElement('textarea');
            el.value = text;
            el.style.cssText = 'position:fixed;opacity:0;pointer-events:none';
            document.body.appendChild(el);
            el.select();
            try { document.execCommand('copy'); } catch (e) {}
            document.body.removeChild(el);
            if (callback) callback();
        }

        // ── Leaflet map initialisation ──────────────────────────────────────────
        @if($hall->latitude && $hall->longitude)
        (function () {
            const LAT  = {{ $hall->latitude }};
            const LNG  = {{ $hall->longitude }};
            const NAME = @json($hallName);
            const ADDR = @json($displayAddress);

            const hallMaps = {};

            function initHallMap(elementId) {
                const el = document.getElementById(elementId);
                if (!el) return null;

                const map = L.map(elementId, {
                    scrollWheelZoom: false,
                    zoomControl: true,
                }).setView([LAT, LNG], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>',
                    maxZoom: 19,
                }).addTo(map);

                const marker = L.marker([LAT, LNG]).addTo(map);
                if (NAME || ADDR) {
                    marker.bindPopup(
                        '<strong style="display:block;margin-bottom:4px">' + NAME + '</strong>' +
                        '<span style="color:#6b7280;font-size:12px">' + ADDR + '</span>'
                    );
                }

                return map;
            }

            function tryInit(elementId) {
                const el = document.getElementById(elementId);
                if (!el) return;
                if (el.offsetWidth > 0 && el.offsetHeight > 0) {
                    hallMaps[elementId] = initHallMap(elementId);
                } else {
                    // Element hidden (Tailwind `hidden`); use IntersectionObserver
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting && !hallMaps[elementId]) {
                                hallMaps[elementId] = initHallMap(elementId);
                                if (hallMaps[elementId]) observer.disconnect();
                            }
                        });
                    }, { threshold: 0.1 });
                    observer.observe(el);
                }
            }

            // Script is at bottom of page — DOM is already ready, call directly.
            tryInit('hall-map-desktop');
            tryInit('hall-map-mobile');

            window.addEventListener('resize', function () {
                Object.values(hallMaps).forEach(m => m && m.invalidateSize());
            });
        })();
        @endif
    </script>

    @guest
        @include('components.booking-choice-modal', ['hall' => $hall])
    @endguest

    @include('layouts.footer')
</body>

</html>
