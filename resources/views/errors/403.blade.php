<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() === 'ar' ? '403 - غير مصرح' : '403 - Forbidden' }} | {{ config('app.name', 'Majalis') }}</title>
    <link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/webp">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @font-face {
            font-family: 'Tajawal';
            src: url('{{ asset("fonts/Tajawal-Regular.ttf") }}') format('truetype');
            font-weight: 400; font-style: normal; font-display: swap;
        }
        @font-face {
            font-family: 'Tajawal';
            src: url('{{ asset("fonts/Tajawal-Medium.ttf") }}') format('truetype');
            font-weight: 500; font-style: normal; font-display: swap;
        }
        @font-face {
            font-family: 'Tajawal';
            src: url('{{ asset("fonts/Tajawal-Bold.ttf") }}') format('truetype');
            font-weight: 700; font-style: normal; font-display: swap;
        }
        *, *::before, *::after { font-family: 'Tajawal', sans-serif !important; }

        .error-number {
            font-size: clamp(7rem, 20vw, 14rem);
            font-weight: 700;
            line-height: 1;
            color: #E8D5C4;
            letter-spacing: -0.05em;
            user-select: none;
        }
        .error-number span { color: #B9916D; }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-12px); }
        }
        .float-anim { animation: float 3.5s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex flex-col">

    {{-- Navigation --}}
    <nav class="sticky top-0 z-50 bg-white border-b border-gray-200" style="backdrop-filter:blur(10px);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.webp') }}" alt="Majalis" class="w-9 h-9 rounded-xl">
                    <span class="hidden sm:inline text-xl font-bold text-gray-800">
                        {{ app()->getLocale() === 'ar' ? 'مجالس' : 'Majalis' }}
                    </span>
                </a>
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

            {{-- Icon --}}
            <div class="float-anim mb-4 flex justify-center">
                <div class="w-24 h-24 rounded-full flex items-center justify-center" style="background-color:#E8D5C4;">
                    <svg class="w-12 h-12" style="color:#B9916D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m0 0v2m0-2h2m-2 0H10m2-11a7 7 0 00-7 7h2a5 5 0 1110 0h2a7 7 0 00-7-7z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 11V7a4 4 0 018 0v4M5 11h14a1 1 0 011 1v7a2 2 0 01-2 2H6a2 2 0 01-2-2v-7a1 1 0 011-1z"/>
                    </svg>
                </div>
            </div>

            {{-- Error number --}}
            <div class="error-number mb-2 leading-none">
                <span>4</span>0<span>3</span>
            </div>

            {{-- Divider --}}
            <div class="flex items-center justify-center gap-3 mb-8">
                <div class="h-px w-16" style="background-color:#E8D5C4;"></div>
                <div class="w-2 h-2 rounded-full" style="background-color:#B9916D;"></div>
                <div class="h-px w-16" style="background-color:#E8D5C4;"></div>
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-3">
                {{ app()->getLocale() === 'ar' ? 'غير مصرح لك بالوصول' : 'Access Forbidden' }}
            </h1>

            <p class="text-gray-500 text-base sm:text-lg mb-10 leading-relaxed">
                {{ app()->getLocale() === 'ar'
                    ? 'ليس لديك الصلاحية اللازمة للوصول إلى هذه الصفحة. يرجى التواصل مع المسؤول إذا كنت تعتقد أن هذا خطأ.'
                    : 'You do not have permission to access this page. Please contact the administrator if you believe this is a mistake.' }}
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ url('/') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-white font-semibold transition-all duration-200 shadow-md hover:shadow-lg"
                   style="background-color:#B9916D;"
                   onmouseover="this.style.backgroundColor='#9A7355'"
                   onmouseout="this.style.backgroundColor='#B9916D'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
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

    <footer class="py-6 bg-gray-800 text-gray-400 text-sm text-center">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; {{ date('Y') }} {{ app()->getLocale() === 'ar' ? 'مجالس. جميع الحقوق محفوظة' : 'Majalis. All rights reserved' }}.</p>
        </div>
    </footer>

</body>
</html>
