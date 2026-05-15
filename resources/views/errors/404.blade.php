<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() === 'ar' ? '404 - الصفحة غير موجودة' : '404 - Page Not Found' }} | {{ config('app.name', 'Majalis') }}</title>
    <link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/webp">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            DEFAULT: '#B9916D',
                            light:   '#E8D5C4',
                            dark:    '#9A7355',
                        }
                    }
                }
            }
        }
    </script>

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

        .brand-text {
            color: #B9916D;
        }
        .brand-bg {
            background-color: #B9916D;
        }
        .brand-bg-light {
            background-color: #E8D5C4;
        }
        .brand-border {
            border-color: #B9916D;
        }
        .brand-hover:hover {
            background-color: #9A7355;
        }

        .four-o-four {
            font-size: clamp(7rem, 20vw, 14rem);
            font-weight: 700;
            line-height: 1;
            color: #E8D5C4;
            letter-spacing: -0.05em;
            position: relative;
            user-select: none;
        }
        .four-o-four span {
            color: #B9916D;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-12px); }
        }
        .float-anim {
            animation: float 3.5s ease-in-out infinite;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex flex-col">

    {{-- Navigation --}}
    <nav class="sticky top-0 z-50 bg-white border-b border-gray-200" style="backdrop-filter: blur(10px);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.webp') }}" alt="Majalis" class="w-9 h-9 rounded-xl">
                    <span class="hidden sm:inline text-xl font-bold text-gray-800">
                        {{ app()->getLocale() === 'ar' ? 'مجالس' : 'Majalis' }}
                    </span>
                </a>

                {{-- Language switcher --}}
                <a href="{{ request()->fullUrlWithQuery(['lang' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                    </svg>
                    {{ app()->getLocale() === 'ar' ? 'EN' : 'ع' }}
                </a>
            </div>
        </div>
    </nav>

    {{-- Main content --}}
    <main class="flex-1 flex items-center justify-center px-4 py-16">
        <div class="text-center max-w-lg w-full">

            {{-- Big 404 --}}
            <div class="four-o-four float-anim select-none mb-2">
                <span>4</span>0<span>4</span>
            </div>

            {{-- Decorative divider --}}
            <div class="flex items-center justify-center gap-3 mb-8">
                <div class="h-px w-16 bg-brand-light" style="background-color:#E8D5C4;"></div>
                <div class="w-2 h-2 rounded-full" style="background-color:#B9916D;"></div>
                <div class="h-px w-16 bg-brand-light" style="background-color:#E8D5C4;"></div>
            </div>

            {{-- Heading --}}
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-3">
                {{ app()->getLocale() === 'ar' ? 'عذراً، الصفحة غير موجودة' : 'Page Not Found' }}
            </h1>

            {{-- Subtext --}}
            <p class="text-gray-500 text-base sm:text-lg mb-10 leading-relaxed">
                {{ app()->getLocale() === 'ar'
                    ? 'يبدو أن الصفحة التي تبحث عنها قد تم نقلها أو حذفها أو لم تكن موجودة من قبل.'
                    : 'The page you are looking for might have been moved, deleted, or never existed.' }}
            </p>

            {{-- CTA Buttons --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ url('/') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-white font-semibold transition-all duration-200 shadow-md hover:shadow-lg"
                   style="background-color:#B9916D;"
                   onmouseover="this.style.backgroundColor='#9A7355'"
                   onmouseout="this.style.backgroundColor='#B9916D'">
                    <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    {{ app()->getLocale() === 'ar' ? 'العودة للرئيسية' : 'Go Home' }}
                </a>

                <button onclick="history.back()"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Go Back' }}
                </button>
            </div>

        </div>
    </main>

    {{-- Footer --}}
    <footer class="py-6 bg-gray-800 text-gray-400 text-sm text-center">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; {{ date('Y') }} {{ app()->getLocale() === 'ar' ? 'مجالس. جميع الحقوق محفوظة' : 'Majalis. All rights reserved' }}.</p>
        </div>
    </footer>

</body>
</html>
