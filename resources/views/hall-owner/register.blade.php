<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('hall-owner.registration.page_title') }} - {{ config('app.name', 'Majalis') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/webp">
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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        :root {
            --primary:      #B9916D;
            --primary-dark: #a47a5a;
            --bg:           #F8F5F2;
            --text:         #2C2A2A;
            --card:         #E8D5C4;
            --line:         #8A8A8C;
        }

        /* Focus ring override for all inputs */
        input:focus, textarea:focus, select:focus {
            outline: none !important;
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(185, 145, 109, 0.18) !important;
        }

        /* Upload drop zone hover */
        .upload-drop:hover {
            border-color: var(--primary) !important;
            background-color: rgba(185, 145, 109, 0.05) !important;
        }

        /* Livewire loading spinner */
        .spinner-brand { color: var(--primary) !important; }

        /* Step progress connector lines */
        .step-line-done { background-color: var(--primary) !important; opacity: 0.55; }
    </style>
</head>
<body class="antialiased" style="background-color: #F8F5F2; color: #2C2A2A;">

    {{-- ── Top Bar ───────────────────────────────────────────────────── --}}
    <div style="background:#fff; border-bottom:1px solid #E8D5C4;" class="px-4 py-3 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <img src="{{ asset('images/logo.webp') }}" alt="{{ config('app.name', 'Majalis') }}" class="h-9 w-auto">
        </a>
        <div class="flex items-center gap-5">
            <a href="{{ request()->fullUrlWithQuery(['lang' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}"
               class="flex items-center gap-1.5 text-sm transition-colors"
               style="color:#8A8A8C;"
               onmouseover="this.style.color='#B9916D'" onmouseout="this.style.color='#8A8A8C'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                </svg>
                {{ app()->getLocale() === 'ar' ? 'EN' : 'ع' }}
            </a>
            <a href="{{ route('login') }}"
               class="text-sm transition-colors"
               style="color:#8A8A8C;"
               onmouseover="this.style.color='#B9916D'" onmouseout="this.style.color='#8A8A8C'">
                {{ __('hall-owner.registration.already_have_account') }}
            </a>
        </div>
    </div>

    <div class="min-h-screen py-10 px-4">
        <div class="max-w-2xl mx-auto">

            {{-- ── Page Header ───────────────────────────────────────────── --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-5"
                     style="background-color: rgba(185,145,109,0.12);">
                    <img src="{{ asset('images/logo.webp') }}" alt="{{ config('app.name') }}" class="w-14 h-14 object-contain rounded-xl">
                </div>
                <h1 class="text-2xl font-bold" style="color:#2C2A2A;">
                    {{ __('hall-owner.registration.heading') }}
                </h1>
                <p class="mt-2 text-sm max-w-md mx-auto" style="color:#8A8A8C;">
                    {{ __('hall-owner.registration.subheading') }}
                </p>
            </div>

            {{-- ── Wizard Card ───────────────────────────────────────────── --}}
            <div class="rounded-2xl shadow-sm p-6 sm:p-8"
                 style="background:#fff; border:1px solid #E8D5C4;">
                @livewire('hall-owner-registration')
            </div>

            {{-- ── Footer Note ───────────────────────────────────────────── --}}
            <p class="mt-6 text-center text-xs" style="color:#8A8A8C;">
                {{ __('hall-owner.registration.terms_note') }}
                <a href="{{ route('pages.terms') }}"
                   class="underline transition-colors"
                   style="color:#B9916D;">{{ __('hall-owner.registration.terms_link') }}</a>
                {{ __('hall-owner.registration.and') }}
                <a href="{{ route('pages.privacy') }}"
                   class="underline transition-colors"
                   style="color:#B9916D;">{{ __('hall-owner.registration.privacy_link') }}</a>.
            </p>
        </div>
    </div>

    @include('layouts.footer')

    @livewireScripts

    <script>
        document.addEventListener('livewire:update', () => {
            requestAnimationFrame(() => {
                const banner = document.getElementById('error-banner');
                if (banner) {
                    banner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });
        });
    </script>
</body>
</html>
