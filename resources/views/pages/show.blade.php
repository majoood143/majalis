<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/webp">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $metaTitle ?? $title }} - Majalis</title>

    {{-- SEO Meta Tags --}}
    <meta name="description" content="{{ $metaDescription ?? $title }}">
    <meta property="og:title" content="{{ $metaTitle ?? $title }}">
    <meta property="og:description" content="{{ $metaDescription ?? $title }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Additional Styles --}}
    <style>
        /* Custom Prose Styles for Content */
        .prose {
            max-width: none;
        }
        .prose h2 {
            font-size: 1.875rem;
            font-weight: 700;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: #1f2937;
        }
        .prose h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: #374151;
        }
        .prose p {
            margin-bottom: 1rem;
            line-height: 1.75;
            color: #4b5563;
        }
        .prose ul,
        .prose ol {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }
        .prose li {
            margin-bottom: 0.5rem;
        }
        .prose a {
            color: #2563eb;
            text-decoration: underline;
        }
        .prose a:hover {
            color: #1d4ed8;
        }

        /* RTL Support */
        [dir="rtl"] .prose ul,
        [dir="rtl"] .prose ol {
            padding-right: 1.5rem;
            padding-left: 0;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    {{-- Header Navigation --}}
    <header class="sticky top-0 z-50 bg-white shadow-sm">
        <nav class="px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-center">
                    <span class="text-2xl font-bold text-blue-600">Majalis</span>
                </a>

                <div class="hidden md:flex items-center space-x-6 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <a href="{{ url('/') }}" class="text-gray-700 transition hover:text-blue-600">
                        {{ app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home' }}
                    </a>
                    <a href="{{ url('/about-us') }}" class="text-gray-700 transition hover:text-blue-600">
                        {{ app()->getLocale() === 'ar' ? 'من نحن' : 'About Us' }}
                    </a>
                    <a href="{{ url('/contact-us') }}" class="text-gray-700 transition hover:text-blue-600">
                        {{ app()->getLocale() === 'ar' ? 'اتصل بنا' : 'Contact' }}
                    </a>

                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-gray-700 transition hover:text-blue-600">
                            {{ app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard' }}
                        </a>
                    @else
                        <a href="{{ url('/login') }}" class="text-gray-700 transition hover:text-blue-600">
                            {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Login' }}
                        </a>
                    @endauth
                </div>

                {{-- Mobile Menu Button --}}
                <button type="button" class="text-gray-700 md:hidden" onclick="toggleMobileMenu()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            {{-- Mobile Menu --}}
            <div id="mobileMenu" class="hidden pb-4 mt-4 space-y-2 md:hidden">
                <a href="{{ url('/') }}" class="block py-2 text-gray-700 hover:text-blue-600">
                    {{ app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home' }}
                </a>
                <a href="{{ url('/about-us') }}" class="block py-2 text-gray-700 hover:text-blue-600">
                    {{ app()->getLocale() === 'ar' ? 'من نحن' : 'About Us' }}
                </a>
                <a href="{{ url('/contact-us') }}" class="block py-2 text-gray-700 hover:text-blue-600">
                    {{ app()->getLocale() === 'ar' ? 'اتصل بنا' : 'Contact' }}
                </a>
            </div>
        </nav>
    </header>

    {{-- Main Content --}}
    <main class="min-h-screen py-8">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <nav class="mb-8" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'flex-row-reverse space-x-reverse' : '' }}">
                    <li>
                        <a href="{{ url('/') }}" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <span class="text-gray-400">/</span>
                    </li>
                    <li>
                        <span class="font-medium text-gray-900">{{ $title }}</span>
                    </li>
                </ol>
            </nav>

            {{-- Page Content Card --}}
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">

                {{-- Page Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-12 sm:px-12 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                    <h1 class="text-3xl font-bold text-white sm:text-4xl">
                        {{ $title }}
                    </h1>
                    <p class="mt-2 text-sm text-blue-100">
                        {{ app()->getLocale() === 'ar' ? 'آخر تحديث' : 'Last Updated' }}:
                        {{ $page->updated_at->format('d F Y') }}
                    </p>
                </div>

                {{-- Page Content --}}
                <div class="px-6 py-8 sm:px-12 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                    <div class="prose max-w-none">
                        {!! $content !!}
                    </div>
                </div>

                {{-- Contact CTA (not on contact page) --}}
                @if($page->slug !== 'contact-us')
                    <div class="bg-gray-50 px-6 py-8 sm:px-12 border-t border-gray-200 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                        <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ app()->getLocale() === 'ar' ? 'تحتاج مساعدة؟' : 'Need Help?' }}
                                </h3>
                                <p class="mt-1 text-gray-600">
                                    {{ app()->getLocale() === 'ar' ? 'فريق الدعم لدينا جاهز لمساعدتك' : 'Our support team is ready to assist you' }}
                                </p>
                            </div>
                            <a href="{{ url('/contact-us') }}"
                               class="inline-flex items-center px-6 py-3 font-medium text-white transition-colors duration-200 bg-blue-600 rounded-lg hover:bg-blue-700">
                                {{ app()->getLocale() === 'ar' ? 'اتصل بنا' : 'Contact Us' }}
                                <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'mr-2 rotate-180' : 'ml-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="py-8 mt-12 text-white bg-gray-800">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div>
                    <h3 class="mb-4 text-lg font-semibold">
                        {{ app()->getLocale() === 'ar' ? 'مجالس' : 'Majalis' }}
                    </h3>
                    <p class="text-gray-400">
                        {{ app()->getLocale() === 'ar' ? 'منصة حجز القاعات الموثوقة في عمان' : 'Your trusted hall booking platform in Oman' }}
                    </p>
                </div>
                <div>
                    <h3 class="mb-4 text-lg font-semibold">
                        {{ app()->getLocale() === 'ar' ? 'روابط سريعة' : 'Quick Links' }}
                    </h3>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/about-us') }}" class="text-gray-400 transition hover:text-white">
                            {{ app()->getLocale() === 'ar' ? 'من نحن' : 'About Us' }}
                        </a></li>
                        <li><a href="{{ url('/contact-us') }}" class="text-gray-400 transition hover:text-white">
                            {{ app()->getLocale() === 'ar' ? 'اتصل بنا' : 'Contact Us' }}
                        </a></li>
                        <li><a href="{{ url('/terms-and-conditions') }}" class="text-gray-400 transition hover:text-white">
                            {{ app()->getLocale() === 'ar' ? 'الشروط والأحكام' : 'Terms & Conditions' }}
                        </a></li>
                        <li><a href="{{ url('/privacy-policy') }}" class="text-gray-400 transition hover:text-white">
                            {{ app()->getLocale() === 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy' }}
                        </a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="mb-4 text-lg font-semibold">
                        {{ app()->getLocale() === 'ar' ? 'اتصل بنا' : 'Contact' }}
                    </h3>
                    <p class="text-gray-400">
                        {{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }}: support@majalis.om<br>
                        {{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}: +968 1234 5678
                    </p>
                </div>
            </div>
            <div class="pt-8 mt-8 text-center text-gray-400 border-t border-gray-700">
                <p>&copy; {{ date('Y') }} Majalis. {{ app()->getLocale() === 'ar' ? 'جميع الحقوق محفوظة' : 'All rights reserved' }}.</p>
            </div>
        </div>
    </footer>

    {{-- Mobile Menu Toggle Script --}}
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>
