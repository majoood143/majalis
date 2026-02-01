<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Majalis')</title>

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

        .safe-area-top {
            padding-top: env(safe-area-inset-top);
        }

        .safe-area-bottom {
            padding-bottom: env(safe-area-inset-bottom);
        }

        /* Loading spinner */
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #0284c7;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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

    @stack('styles')
</head>

<body class="min-h-screen bg-gray-50">

    <!-- Top Navigation -->
    <nav class="sticky top-0 z-50 border-b border-gray-200 glass-morphism safe-area-top">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between h-16">
                <!-- Back Button -->
                <a href="{{ url()->previous() }}"
                    class="flex items-center gap-2 text-gray-700 transition hover:text-gray-900">
                    <svg class="w-6 h-6 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    <span class="hidden font-medium sm:inline">{{ __('guest.back') }}</span>
                </a>

                <!-- Logo -->
                <a href="{{ route('customer.halls.index') }}" class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-10 h-10 shadow-lg bg-gradient-to-br rounded-xl">
                        {{-- <span class="text-xl font-bold text-white">م</span> --}}
                        <img src="{{ asset('images/logo.webp') }}" alt="Majalis Logo" class="w-8 h-8">
                    </div>
                    <span class="hidden text-xl font-bold text-gray-800 sm:inline">{{ __('guest.majalis') }}</span>
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
                        {{ app()->getLocale() === 'ar' ? 'EN' : 'ع' }}
                    </span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="py-8 mt-12 bg-white border-t border-gray-200 safe-area-bottom">
        <div class="container px-4 mx-auto">
            <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br ">
                         <img src="{{ asset('images/logo.webp') }}" alt="Majalis Logo" class="w-8 h-8">
                        {{-- <span class="text-sm font-bold text-white">م</span> --}}
                    </div>
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
