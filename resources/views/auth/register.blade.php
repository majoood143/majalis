<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('dashboard.create_account_title') }} - Majalis</title>
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
    <style>

        body { background-color: #F8F5F2; color: #2C2A2A; }

        input:focus, textarea:focus {
            outline: none !important;
            border-color: #B9916D !important;
            box-shadow: 0 0 0 3px rgba(185, 145, 109, 0.18) !important;
        }

        .language-switcher {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
        }
        [dir="rtl"] .language-switcher {
            right: auto;
            left: 1.25rem;
        }
    </style>
</head>
<body class="min-h-screen antialiased" style="background-color:#F8F5F2;">

    {{-- Language Switcher --}}
    <div class="language-switcher">
        <a href="{{ request()->fullUrlWithQuery(['lang' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}"
           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-full text-sm font-medium transition-colors"
           style="background:#fff; border:1px solid #E8D5C4; color:#8A8A8C;"
           onmouseover="this.style.color='#B9916D'" onmouseout="this.style.color='#8A8A8C'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
            </svg>
            <span>{{ app()->getLocale() === 'ar' ? 'EN' : 'ع' }}</span>
        </a>
    </div>

    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-md">

            {{-- Logo & Heading --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-5"
                     style="background-color:rgba(185,145,109,0.12);">
                    <img src="{{ asset('images/logo.webp') }}" alt="{{ config('app.name', 'Majalis') }}"
                         class="w-14 h-14 object-contain rounded-xl">
                </div>
                <h1 class="text-2xl font-bold" style="color:#2C2A2A;">
                    {{ __('dashboard.create_account_title') }}
                </h1>
                <p class="mt-2 text-sm" style="color:#8A8A8C;">
                    {{ __('dashboard.or') }}
                    <a href="{{ route('login') }}"
                       class="font-semibold transition-colors"
                       style="color:#B9916D;"
                       onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                        {{ __('dashboard.sign_in_existing') }}
                    </a>
                </p>
            </div>

            {{-- Card --}}
            <div class="rounded-2xl shadow-sm p-6 sm:p-8"
                 style="background:#fff; border:1px solid #E8D5C4;">

                {{-- Error Banner --}}
                @if($errors->any())
                    <div class="mb-6 rounded-xl p-4"
                         style="background-color:#fff5f5; border:1px solid #fecaca;">
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center"
                                 style="background-color:#fee2e2;">
                                <svg class="w-4 h-4" style="color:#ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <ul class="text-sm space-y-0.5 list-disc list-inside" style="color:#b91c1c;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- Full Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">
                            {{ __('dashboard.full_name') }}
                            <span style="color:#ef4444;" class="ms-0.5">*</span>
                        </label>
                        <input id="name" name="name" type="text" required
                               value="{{ old('name') }}"
                               placeholder="{{ __('dashboard.full_name_placeholder') }}"
                               class="w-full px-3.5 py-2.5 rounded-lg text-sm transition-colors"
                               style="border:1px solid {{ $errors->has('name') ? '#fca5a5' : '#E8D5C4' }}; background:{{ $errors->has('name') ? '#fff5f5' : '#fff' }}; color:#2C2A2A;">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">
                            {{ __('dashboard.email_address') }}
                            <span style="color:#ef4444;" class="ms-0.5">*</span>
                        </label>
                        <input id="email" name="email" type="email" required
                               value="{{ old('email') }}"
                               placeholder="you@example.com"
                               class="w-full px-3.5 py-2.5 rounded-lg text-sm transition-colors"
                               style="border:1px solid {{ $errors->has('email') ? '#fca5a5' : '#E8D5C4' }}; background:{{ $errors->has('email') ? '#fff5f5' : '#fff' }}; color:#2C2A2A;">
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">
                            {{ __('dashboard.password') }}
                            <span style="color:#ef4444;" class="ms-0.5">*</span>
                        </label>
                        <input id="password" name="password" type="password" required
                               placeholder="{{ __('dashboard.password_min') }}"
                               class="w-full px-3.5 py-2.5 rounded-lg text-sm transition-colors"
                               style="border:1px solid {{ $errors->has('password') ? '#fca5a5' : '#E8D5C4' }}; background:{{ $errors->has('password') ? '#fff5f5' : '#fff' }}; color:#2C2A2A;">
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">
                            {{ __('dashboard.confirm_password') }}
                            <span style="color:#ef4444;" class="ms-0.5">*</span>
                        </label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               placeholder="{{ __('dashboard.password_min') }}"
                               class="w-full px-3.5 py-2.5 rounded-lg text-sm transition-colors"
                               style="border:1px solid {{ $errors->has('password_confirmation') ? '#fca5a5' : '#E8D5C4' }}; background:{{ $errors->has('password_confirmation') ? '#fff5f5' : '#fff' }}; color:#2C2A2A;">
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="w-full py-2.5 px-4 rounded-lg text-sm font-semibold text-white transition-colors"
                            style="background:#B9916D; border:none;"
                            onmouseover="this.style.background='#a47a5a'" onmouseout="this.style.background='#B9916D'">
                        {{ __('dashboard.create_account') }}
                    </button>

                </form>
            </div>

            {{-- Back to Halls --}}
            <div class="mt-6 text-center">
                <a href="{{ route('customer.halls.index') }}"
                   class="inline-flex items-center gap-1.5 text-sm transition-colors"
                   style="color:#8A8A8C;"
                   onmouseover="this.style.color='#B9916D'" onmouseout="this.style.color='#8A8A8C'">
                    <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('dashboard.back_to_halls') }}
                </a>
            </div>

        </div>
    </div>

</body>
</html>
