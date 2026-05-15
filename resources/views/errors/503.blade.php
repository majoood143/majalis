<!DOCTYPE html>
<html lang="{{ isset($locale) ? $locale : 'en' }}" dir="{{ isset($locale) && $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($locale) && $locale === 'ar' ? 'الموقع تحت الصيانة' : 'Under Maintenance' }} | Majalis</title>
    {{-- No asset() calls here — app may be fully down. Use relative paths instead. --}}
    <link rel="icon" href="/images/logo.webp" type="image/webp">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @font-face {
            font-family: 'Tajawal';
            src: url('/fonts/Tajawal-Regular.ttf') format('truetype');
            font-weight: 400; font-style: normal; font-display: swap;
        }
        @font-face {
            font-family: 'Tajawal';
            src: url('/fonts/Tajawal-Medium.ttf') format('truetype');
            font-weight: 500; font-style: normal; font-display: swap;
        }
        @font-face {
            font-family: 'Tajawal';
            src: url('/fonts/Tajawal-Bold.ttf') format('truetype');
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

        @keyframes wrench {
            0%, 100% { transform: rotate(0deg); }
            25%       { transform: rotate(-20deg); }
            75%       { transform: rotate(20deg); }
        }
        .wrench-anim { animation: wrench 2s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex flex-col">

    {{-- Minimal nav — no server-side calls since app may be down --}}
    <nav class="sticky top-0 z-50 bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center h-16">
                <a href="/" class="flex items-center gap-2">
                    <img src="/images/logo.webp" alt="Majalis" class="w-9 h-9 rounded-xl">
                    <span class="hidden sm:inline text-xl font-bold text-gray-800">Majalis</span>
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
                    <svg class="w-12 h-12 wrench-anim" style="color:#B9916D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Error number --}}
            <div class="error-number mb-2 leading-none">
                <span>5</span>0<span>3</span>
            </div>

            {{-- Divider --}}
            <div class="flex items-center justify-center gap-3 mb-8">
                <div class="h-px w-16" style="background-color:#E8D5C4;"></div>
                <div class="w-2 h-2 rounded-full" style="background-color:#B9916D;"></div>
                <div class="h-px w-16" style="background-color:#E8D5C4;"></div>
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-3">
                نحن تحت الصيانة / We're Under Maintenance
            </h1>

            <p class="text-gray-500 text-base sm:text-lg mb-4 leading-relaxed">
                الموقع متوقف مؤقتاً لأعمال الصيانة. سنعود قريباً!
            </p>
            <p class="text-gray-500 text-base sm:text-lg mb-10 leading-relaxed">
                The site is temporarily down for maintenance. We'll be back shortly!
            </p>

            @if(isset($exception) && $exception->getMessage())
            <p class="text-sm text-gray-400 mb-8 italic">{{ $exception->getMessage() }}</p>
            @endif

            <button onclick="location.reload()"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-white font-semibold transition-all duration-200 shadow-md hover:shadow-lg"
                    style="background-color:#B9916D;"
                    onmouseover="this.style.backgroundColor='#9A7355'"
                    onmouseout="this.style.backgroundColor='#B9916D'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Try Again / حاول مرة أخرى
            </button>

        </div>
    </main>

    <footer class="py-6 bg-gray-800 text-gray-400 text-sm text-center">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; {{ date('Y') }} Majalis. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
