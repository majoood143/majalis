<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      class="min-h-screen fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('dashboard.sign_in_title') }} - Majalis Admin</title>

    <style>
        @font-face { font-family: 'Tajawal'; src: url('{{ asset("fonts/Tajawal-Regular.ttf") }}') format('truetype'); font-weight: 400; font-style: normal; font-display: swap; }
        @font-face { font-family: 'Tajawal'; src: url('{{ asset("fonts/Tajawal-Medium.ttf") }}') format('truetype'); font-weight: 500; font-style: normal; font-display: swap; }
        @font-face { font-family: 'Tajawal'; src: url('{{ asset("fonts/Tajawal-Bold.ttf") }}') format('truetype'); font-weight: 700; font-style: normal; font-display: swap; }
        *, *::before, *::after { font-family: 'Tajawal', sans-serif !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak], [x-cloak='x-cloak'], [x-cloak='1'] { display: none !important; }
        @media (max-width: 1023px) { [x-cloak='-lg'] { display: none !important; } }
        @media (min-width: 1024px)  { [x-cloak='lg']  { display: none !important; } }

        :root {
            --primary-50:  239, 246, 255;
            --primary-100: 219, 234, 254;
            --primary-200: 191, 219, 254;
            --primary-300: 147, 197, 253;
            --primary-400: 96,  165, 250;
            --primary-500: 59,  130, 246;
            --primary-600: 185, 145, 109;
            --primary-700: 29,  78,  216;
            --primary-800: 30,  64,  175;
            --primary-900: 30,  58,  138;
            --primary-950: 23,  37,  84;

            --gray-50:  248, 250, 252;
            --gray-100: 241, 245, 249;
            --gray-200: 226, 232, 240;
            --gray-300: 203, 213, 225;
            --gray-400: 148, 163, 184;
            --gray-500: 100, 116, 139;
            --gray-600: 71,  85,  105;
            --gray-700: 51,  65,  85;
            --gray-800: 30,  41,  59;
            --gray-900: 15,  23,  42;
            --gray-950: 2,   6,   23;

            --danger-50:  254, 242, 242;
            --danger-100: 254, 226, 226;
            --danger-200: 254, 202, 202;
            --danger-300: 252, 165, 165;
            --danger-400: 248, 113, 113;
            --danger-500: 239, 68,  68;
            --danger-600: 220, 38,  38;
            --danger-700: 185, 28,  28;
            --danger-800: 153, 27,  27;
            --danger-900: 127, 29,  29;
            --danger-950: 69,  10,  10;
        }

        /* ── Layout ─────────────────────────────────────────────────────── */
        .fi-simple-layout {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .fi-simple-main-ctn {
            width: 100%;
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .fi-simple-main {
            width: 100%;
            max-width: 28rem;
            background: white;
            padding: 3rem 1.5rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0/.1), 0 1px 2px -1px rgb(0 0 0/.1);
            border: 1px solid rgb(2 6 23 / .05);
        }
        @media (min-width: 640px) {
            .fi-simple-main { border-radius: .75rem; padding: 3rem; }
        }
        .dark .fi-simple-main { background: rgb(15 23 42); border-color: rgb(255 255 255/.1); }

        /* ── Header ─────────────────────────────────────────────────────── */
        .fi-simple-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .fi-logo { height: 2.5rem; margin-bottom: 1rem; }
        .fi-simple-header-heading {
            text-align: center;
            font-size: 1.5rem;
            line-height: 2rem;
            font-weight: 700;
            letter-spacing: -.025em;
            color: rgb(2 6 23);
        }
        .dark .fi-simple-header-heading { color: white; }

        /* ── Form ───────────────────────────────────────────────────────── */
        .fi-form { display: grid; gap: 1.5rem; }
        .fi-fo-field-wrp { display: grid; gap: .5rem; }
        .fi-fo-field-wrp-label {
            display: inline-flex;
            align-items: center;
            gap: .75rem;
            font-size: .875rem;
            line-height: 1.5rem;
            font-weight: 500;
            color: rgb(2 6 23);
        }
        .dark .fi-fo-field-wrp-label { color: white; }

        /* ── Input wrapper ──────────────────────────────────────────────── */
        .fi-input-wrp {
            display: flex;
            border-radius: .5rem;
            box-shadow: 0 1px 2px 0 rgb(0 0 0/.05);
            border: 1px solid rgb(2 6 23 / .1);
            transition: border-color 75ms, box-shadow 75ms;
            background: white;
            overflow: hidden;
        }
        .dark .fi-input-wrp { background: rgb(255 255 255/.05); border-color: rgb(255 255 255/.2); }
        .fi-input-wrp:focus-within {
            border-color: rgb(var(--primary-600));
            box-shadow: 0 0 0 1px rgb(var(--primary-600));
        }
        .dark .fi-input-wrp:focus-within {
            border-color: rgb(var(--primary-500));
            box-shadow: 0 0 0 1px rgb(var(--primary-500));
        }
        .fi-input-wrp-input { min-width: 0; flex: 1; }
        .fi-input {
            display: block;
            width: 100%;
            border: none;
            outline: none;
            padding: .375rem .75rem;
            font-size: .875rem;
            line-height: 1.5rem;
            color: rgb(2 6 23);
            background: transparent;
        }
        .fi-input::placeholder { color: rgb(148 163 184); }
        .dark .fi-input { color: white; }
        .dark .fi-input::placeholder { color: rgb(100 116 139); }

        /* ── Password toggle suffix ─────────────────────────────────────── */
        .fi-input-wrp-suffix {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding-inline-end: .75rem;
            border-inline-start: 1px solid rgb(226 232 240);
        }
        .dark .fi-input-wrp-suffix { border-inline-start-color: rgb(255 255 255/.1); }

        .fi-icon-btn {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: .5rem;
            margin: -.375rem;
            height: 2rem;
            width: 2rem;
            color: rgb(148 163 184);
            background: transparent;
            border: none;
            cursor: pointer;
            transition: color 75ms;
        }
        .fi-icon-btn:hover { color: rgb(100 116 139); }
        .fi-icon-btn:focus-visible { outline: 2px solid rgb(var(--primary-600)); outline-offset: 2px; }
        .dark .fi-icon-btn { color: rgb(100 116 139); }
        .dark .fi-icon-btn:hover { color: rgb(148 163 184); }
        .dark .fi-icon-btn:focus-visible { outline-color: rgb(var(--primary-500)); }
        .fi-icon-btn-icon { height: 1.25rem; width: 1.25rem; }

        /* ── Checkbox ───────────────────────────────────────────────────── */
        .fi-checkbox-input {
            width: 1rem;
            height: 1rem;
            border-radius: .25rem;
            border: 1px solid rgb(2 6 23 / .1);
            background: white;
            box-shadow: 0 1px 2px 0 rgb(0 0 0/.05);
            cursor: pointer;
            accent-color: rgb(var(--primary-600));
            transition: border-color 75ms;
        }
        .fi-checkbox-input:focus { outline: 2px solid rgb(var(--primary-600)); outline-offset: 0; }
        .dark .fi-checkbox-input { background: rgb(255 255 255/.05); border-color: rgb(255 255 255/.2); accent-color: rgb(var(--primary-500)); }

        /* ── Button ─────────────────────────────────────────────────────── */
        .fi-btn {
            display: inline-grid;
            grid-auto-flow: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            font-weight: 600;
            border-radius: .5rem;
            gap: .375rem;
            padding: .625rem .75rem;
            font-size: .875rem;
            line-height: 1rem;
            background: rgb(var(--primary-600));
            color: white;
            box-shadow: 0 1px 2px 0 rgb(0 0 0/.05);
            border: none;
            cursor: pointer;
            outline: none;
            transition: background-color 75ms;
        }
        .fi-btn:hover { background: rgb(var(--primary-500)); }
        .fi-btn:focus-visible { outline: 2px solid rgb(var(--primary-600) / .5); outline-offset: 2px; }
        .fi-btn:disabled { opacity: .7; cursor: wait; }
        .dark .fi-btn { background: rgb(var(--primary-500)); }
        .dark .fi-btn:hover { background: rgb(var(--primary-400)); }
        .dark .fi-btn:focus-visible { outline-color: rgb(var(--primary-400) / .5); }
        .fi-btn-icon { height: 1.25rem; width: 1.25rem; }

        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .animate-spin { animation: spin 1s linear infinite; }

        /* ── Link ───────────────────────────────────────────────────────── */
        .fi-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            outline: none;
            gap: .375rem;
            text-decoration: none;
        }
        .fi-link:hover .fi-link-label,
        .fi-link:focus-visible .fi-link-label { text-decoration: underline; }
        .fi-link-label { font-size: .875rem; line-height: 1.25rem; font-weight: 600; color: rgb(var(--primary-600)); }
        .dark .fi-link-label { color: rgb(var(--primary-400)); }

        /* ── Language switcher ──────────────────────────────────────────── */
        .language-switcher {
            position: absolute;
            top: 1.5rem;
            inset-inline-end: 1.5rem;
            z-index: 10;
        }
        .language-switcher-link {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .5rem 1rem;
            background: white;
            border-radius: 9999px;
            box-shadow: 0 1px 2px 0 rgb(0 0 0/.05);
            border: 1px solid rgb(2 6 23 / .05);
            font-size: .875rem;
            font-weight: 500;
            color: rgb(51 65 85);
            transition: background-color .2s;
            text-decoration: none;
        }
        .language-switcher-link:hover { background: rgb(248 250 252); }
        .dark .language-switcher-link { background: rgb(30 41 59); border-color: rgb(255 255 255/.1); color: rgb(226 232 240); }
        .dark .language-switcher-link:hover { background: rgb(51 65 85); }

        /* ── Error alert ────────────────────────────────────────────────── */
        .fi-error-alert {
            padding: 1rem;
            background: rgb(var(--danger-50));
            border: 1px solid rgb(var(--danger-200));
            border-radius: .5rem;
        }
        .dark .fi-error-alert { background: rgb(var(--danger-950) / .4); border-color: rgb(var(--danger-800)); }
        .fi-error-alert-content {
            display: flex;
            align-items: flex-start;
            gap: .625rem;
            font-size: .875rem;
            line-height: 1.25rem;
            color: rgb(var(--danger-700));
        }
        .dark .fi-error-alert-content { color: rgb(var(--danger-300)); }
    </style>
</head>
<body class="min-h-screen antialiased font-normal fi-body bg-gray-50 text-gray-950 dark:bg-gray-950 dark:text-white">

    <!-- Language Switcher -->
    <div class="language-switcher">
        <a href="{{ request()->fullUrlWithQuery(['lang' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}"
           class="language-switcher-link">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
            </svg>
            <span>{{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}</span>
        </a>
    </div>

    <div class="fi-simple-layout">
        <div class="fi-simple-main-ctn">
            <main class="fi-simple-main">
                <section class="grid auto-cols-fr gap-y-6">

                    <!-- Header -->
                    <header class="fi-simple-header">
                        <img alt="Majalis Admin logo"
                             src="{{ asset('images/logo.webp') }}"
                             class="fi-logo">
                        <h1 class="fi-simple-header-heading">
                            {{ __('dashboard.sign_in_title') }}
                        </h1>
                    </header>

                    <!-- Validation errors -->
                    @if ($errors->any())
                        <div class="fi-error-alert" role="alert">
                            <div class="fi-error-alert-content">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <ul class="space-y-1 list-none">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- Login form -->
                    <form method="POST"
                          action="{{ route('login') }}"
                          class="fi-form"
                          x-data="{ processing: false }"
                          x-on:submit="processing = true">
                        @csrf

                        <!-- Email -->
                        <div class="fi-fo-field-wrp">
                            <div class="grid gap-y-2">
                                <label class="fi-fo-field-wrp-label" for="email">
                                    {{ __('dashboard.email_address') }}
                                    <sup class="font-medium text-danger-600 dark:text-danger-400" aria-hidden="true">*</sup>
                                </label>
                                <div class="fi-input-wrp">
                                    <div class="fi-input-wrp-input">
                                        <input type="email"
                                               name="email"
                                               id="email"
                                               class="fi-input"
                                               placeholder="name@example.com"
                                               value="{{ old('email') }}"
                                               autocomplete="email"
                                               required
                                               autofocus>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="fi-fo-field-wrp">
                            <div class="grid gap-y-2">
                                <div class="flex items-center justify-between gap-x-3">
                                    <label class="fi-fo-field-wrp-label" for="password">
                                        {{ __('dashboard.password') }}
                                        <sup class="font-medium text-danger-600 dark:text-danger-400" aria-hidden="true">*</sup>
                                    </label>
                                    <a href="{{ route('password.request') }}" class="fi-link">
                                        <span class="fi-link-label text-xs">{{ __('dashboard.forgot_password_link') }}</span>
                                    </a>
                                </div>
                                <div class="fi-input-wrp" x-data="{ show: false }">
                                    <div class="fi-input-wrp-input">
                                        <input :type="show ? 'text' : 'password'"
                                               name="password"
                                               id="password"
                                               class="fi-input"
                                               placeholder="••••••••"
                                               autocomplete="current-password"
                                               required>
                                    </div>
                                    <div class="fi-input-wrp-suffix">
                                        <button type="button"
                                                class="fi-icon-btn"
                                                @click="show = !show"
                                                :aria-label="show ? '{{ __('Hide password') }}' : '{{ __('Show password') }}'">
                                            <!-- eye-open -->
                                            <svg x-show="!show" class="fi-icon-btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                                                <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd"/>
                                            </svg>
                                            <!-- eye-slash -->
                                            <svg x-show="show" class="fi-icon-btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" style="display:none">
                                                <path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 0 0-1.06 1.06l14.5 14.5a.75.75 0 1 0 1.06-1.06l-1.745-1.745a10.029 10.029 0 0 0 3.3-4.38 1.651 1.651 0 0 0 0-1.185A10.004 10.004 0 0 0 9.999 3a9.956 9.956 0 0 0-4.744 1.194L3.28 2.22ZM7.752 6.69l1.092 1.092a2.5 2.5 0 0 1 3.374 3.373l1.091 1.092a4 4 0 0 0-5.557-5.557Z" clip-rule="evenodd"/>
                                                <path d="m10.748 13.93 2.523 2.523a9.987 9.987 0 0 1-3.27.547c-4.258 0-7.894-2.66-9.337-6.41a1.651 1.651 0 0 1 0-1.186A10.007 10.007 0 0 1 2.839 6.02L6.07 9.252a4 4 0 0 0 4.678 4.678Z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Remember me -->
                        <div class="fi-fo-field-wrp">
                            <label class="inline-flex items-center gap-x-3 fi-fo-field-wrp-label cursor-pointer" for="remember">
                                <input type="checkbox"
                                       name="remember"
                                       id="remember"
                                       class="fi-checkbox-input"
                                       {{ old('remember') ? 'checked' : '' }}>
                                <span class="text-sm font-medium leading-6">{{ __('dashboard.remember_me') }}</span>
                            </label>
                        </div>

                        <!-- Submit -->
                        <div>
                            <button type="submit" class="fi-btn" :disabled="processing">
                                <!-- Spinner -->
                                <svg x-show="processing"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     xmlns="http://www.w3.org/2000/svg"
                                     class="fi-btn-icon animate-spin"
                                     style="display:none"
                                     aria-hidden="true">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                                    <path fill="currentColor" class="opacity-75"
                                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <span class="fi-btn-label"
                                      x-text="processing ? '{{ __('Processing...') }}' : '{{ __('dashboard.sign_in') }}'">
                                    {{ __('dashboard.sign_in') }}
                                </span>
                            </button>
                        </div>

                        <!-- Register link -->
                        <p class="text-sm text-center text-gray-500 dark:text-gray-400">
                            {{ __("dashboard.don't_have_account") }}
                            <a href="{{ route('register') }}" class="fi-link">
                                <span class="fi-link-label">{{ __('dashboard.create_new_account') }}</span>
                            </a>
                        </p>

                        <!-- Back to halls -->
                        <div class="pt-4 text-center border-t border-gray-200 dark:border-white/10">
                            <a href="{{ route('customer.halls.index') }}"
                               class="inline-flex items-center gap-2 text-sm text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    @if(app()->getLocale() === 'ar')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    @endif
                                </svg>
                                {{ __('dashboard.back_to_halls') }}
                            </a>
                        </div>

                    </form>
                </section>
            </main>
        </div>

        <!-- Footer -->
        <p class="my-8 text-sm text-center text-gray-400 dark:text-gray-500">
            &copy; {{ date('Y') }} Majalis. {{ __('dashboard.all_rights_reserved') }}
        </p>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
