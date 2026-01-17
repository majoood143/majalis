<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ is_array($hall->name) ? $hall->name[app()->getLocale()] ?? $hall->name['en'] : $hall->name }} - Majalis
    </title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

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
                            600: '#0284c7',
                            700: '#0369a1',
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
                    <div
                        class="flex items-center justify-center w-10 h-10 shadow-lg bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl">
                        <span class="text-xl font-bold text-white">م</span>
                    </div>
                    <span class="text-xl font-bold text-gray-800">Majalis</span>
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
                            {{ $hall->address }}
                        </span>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-2 gap-4 p-4 sm:grid-cols-4 bg-gray-50 rounded-xl">
                        <div class="text-center">
                            <div class="mb-1 text-xs text-gray-600">{{ __('halls.capacity') }}</div>
                            <div class="text-lg font-bold text-gray-900">
                                {{ $hall->capacity_min }}-{{ $hall->capacity_max }}</div>
                            <div class="text-xs text-gray-500">{{ __('halls.guests_count') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="mb-1 text-xs text-gray-600">{{ __('halls.area') }}</div>
                            <div class="text-lg font-bold text-gray-900">{{ $hall->area }}</div>
                            <div class="text-xs text-gray-500">{{ __('halls.square_meters') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="mb-1 text-xs text-gray-600">{{ __('halls.price_per_day') }}</div>
                            <div class="text-lg font-bold text-primary-600">
                                {{ number_format($hall->price_per_slot, 3) }}</div>
                            <div class="text-xs text-gray-500">OMR</div>
                        </div>
                        <div class="text-center">
                            <div class="mb-1 text-xs text-gray-600">{{ __('halls.total_bookings') }}</div>
                            <div class="text-lg font-bold text-gray-900">{{ $hall->bookings->count() }}</div>
                            <div class="text-xs text-gray-500">{{ __('halls.reviews') }}</div>
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
                                            {{ number_format($service->price, 3) }} OMR</div>
                                        <div class="text-xs text-gray-500">{{ $service->unit }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Contact Owner (Mobile - in content) -->
                <div class="p-6 bg-white border border-gray-200 shadow-sm lg:hidden rounded-2xl">
                    <h4 class="flex items-center gap-2 mb-4 text-lg font-bold">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ __('halls.contact_owner') }}
                    </h4>
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
                                class="font-medium text-gray-700">{{ $hall->owner->phone ?? __('halls.not_available') }}</span>
                        </a>
                        <a href="mailto:{{ $hall->owner->email }}"
                            class="flex items-center gap-3 p-3 transition bg-gray-50 rounded-xl hover:bg-gray-100">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="font-medium text-gray-700 truncate">{{ $hall->owner->email }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sidebar (Desktop Only) -->
            <div class="hidden lg:block lg:col-span-1">
                <div class="p-6 bg-white border border-gray-200 shadow-lg booking-card rounded-2xl">
                    <!-- Price -->
                    <div class="pb-6 mb-6 text-center border-b border-gray-200">
                        <div class="mb-2 text-4xl font-bold text-primary-600">
                            {{ number_format($hall->price_per_slot, 3) }} <span class="text-xl">OMR</span>
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
                        <!-- ... your existing benefits code ... -->
                    </div>

                    <!-- Contact Owner -->
                    <div class="pt-6 border-t border-gray-200">
                        <!-- ... your existing contact owner code ... -->
                    </div>
                </div>
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
                                        <span class="text-sm text-gray-600"> OMR</span>
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
                        {{ number_format($hall->price_per_slot, 3) }} <span class="text-sm">OMR</span>
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

    @guest
    @include('components.booking-choice-modal', ['hall' => $hall])
@endguest

</body>

</html>
