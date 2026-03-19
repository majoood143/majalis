<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      class="min-h-screen fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('dashboard.sign_in_title') }} - Majalis Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&amp;display=swap" rel="stylesheet" />

    <style>
        [x-cloak=''],
        [x-cloak='x-cloak'],
        [x-cloak='1'] {
            display: none !important;
        }

        @media (max-width: 1023px) {
            [x-cloak='-lg'] {
                display: none !important;
            }
        }

        @media (min-width: 1024px) {
            [x-cloak='lg'] {
                display: none !important;
            }
        }

        :root {
            --danger-50: 254, 242, 242;
            --danger-100: 254, 226, 226;
            --danger-200: 254, 202, 202;
            --danger-300: 252, 165, 165;
            --danger-400: 248, 113, 113;
            --danger-500: 239, 68, 68;
            --danger-600: 220, 38, 38;
            --danger-700: 185, 28, 28;
            --danger-800: 153, 27, 27;
            --danger-900: 127, 29, 29;
            --danger-950: 69, 10, 10;

            --gray-50: 248, 250, 252;
            --gray-100: 241, 245, 249;
            --gray-200: 226, 232, 240;
            --gray-300: 203, 213, 225;
            --gray-400: 148, 163, 184;
            --gray-500: 100, 116, 139;
            --gray-600: 71, 85, 105;
            --gray-700: 51, 65, 85;
            --gray-800: 30, 41, 59;
            --gray-900: 15, 23, 42;
            --gray-950: 2, 6, 23;

            --primary-50: 239, 246, 255;
            --primary-100: 219, 234, 254;
            --primary-200: 191, 219, 254;
            --primary-300: 147, 197, 253;
            --primary-400: 96, 165, 250;
            --primary-500: 59, 130, 246;
            --primary-600: 185, 145, 109;
            --primary-700: 29, 78, 216;
            --primary-800: 30, 64, 175;
            --primary-900: 30, 58, 138;
            --primary-950: 23, 37, 84;

            --success-50: 240, 253, 244;
            --success-100: 220, 252, 231;
            --success-200: 187, 247, 208;
            --success-300: 134, 239, 172;
            --success-400: 74, 222, 128;
            --success-500: 34, 197, 94;
            --success-600: 22, 163, 74;
            --success-700: 21, 128, 61;
            --success-800: 22, 101, 52;
            --success-900: 20, 83, 45;
            --success-950: 5, 46, 22;

            --warning-50: 255, 251, 235;
            --warning-100: 254, 243, 199;
            --warning-200: 253, 230, 138;
            --warning-300: 252, 211, 77;
            --warning-400: 251, 191, 36;
            --warning-500: 245, 158, 11;
            --warning-600: 217, 119, 6;
            --warning-700: 180, 83, 9;
            --warning-800: 146, 64, 14;
            --warning-900: 120, 53, 15;
            --warning-950: 69, 26, 3;

            --font-family: 'Inter';
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .fi-body {
            background: linear-gradient(135deg, rgb(var(--gray-50)) 0%, rgb(var(--gray-100)) 100%);
        }

        .dark .fi-body {
            background: linear-gradient(135deg, rgb(var(--gray-950)) 0%, rgb(var(--gray-900)) 100%);
        }

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
        }

        .fi-simple-main {
            width: 100%;
            background: white;
            padding: 3rem 1.5rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            ring: 1px solid rgb(var(--gray-950) / 0.05);
            max-width: 28rem;
        }

        .dark .fi-simple-main {
            background: rgb(var(--gray-900));
            ring: 1px solid rgb(255 255 255 / 0.1);
        }

        @media (min-width: 640px) {
            .fi-simple-main {
                border-radius: 0.75rem;
                padding: 3rem;
            }
        }

        .fi-simple-header {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .fi-logo {
            height: 2.5rem;
            margin-bottom: 1rem;
        }

        .fi-simple-header-heading {
            text-align: center;
            font-size: 1.5rem;
            line-height: 2rem;
            font-weight: 700;
            letter-spacing: -0.025em;
            color: rgb(var(--gray-950));
        }

        .dark .fi-simple-header-heading {
            color: white;
        }

        .fi-form {
            display: grid;
            gap: 1.5rem;
        }

        .fi-fo-field-wrp {
            display: grid;
            gap: 0.5rem;
        }

        .fi-fo-field-wrp-label {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
            line-height: 1.5rem;
            font-weight: 500;
            color: rgb(var(--gray-950));
        }

        .dark .fi-fo-field-wrp-label {
            color: white;
        }

        .fi-input-wrp {
            display: flex;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            ring: 1px solid rgb(var(--gray-950) / 0.1);
            transition: duration-75;
            background: white;
            overflow: hidden;
        }

        .dark .fi-input-wrp {
            background: rgb(255 255 255 / 0.05);
            ring: 1px solid rgb(255 255 255 / 0.2);
        }

        .fi-input-wrp:focus-within {
            ring: 2px solid rgb(var(--primary-600));
        }

        .dark .fi-input-wrp:focus-within {
            ring: 2px solid rgb(var(--primary-500));
        }

        .fi-input-wrp-input {
            min-width: 0;
            flex: 1;
        }

        .fi-input {
            display: block;
            width: 100%;
            border: none;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5rem;
            color: rgb(var(--gray-950));
            transition: duration-75;
            background: transparent;
        }

        .fi-input::placeholder {
            color: rgb(var(--gray-400));
        }

        .dark .fi-input {
            color: white;
        }

        .dark .fi-input::placeholder {
            color: rgb(var(--gray-500));
        }

        .fi-input:focus {
            ring: 0;
        }

        @media (min-width: 640px) {
            .fi-input {
                font-size: 0.875rem;
                line-height: 1.5rem;
            }
        }

        .fi-input-wrp-suffix {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-right: 0.75rem;
            border-left: 1px solid rgb(var(--gray-200));
        }

        .dark .fi-input-wrp-suffix {
            border-left-color: rgb(255 255 255 / 0.1);
        }

        .fi-icon-btn {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            outline: none;
            transition: duration-75;
            margin: -0.375rem;
            height: 2rem;
            width: 2rem;
            color: rgb(var(--gray-400));
        }

        .fi-icon-btn:hover {
            color: rgb(var(--gray-500));
        }

        .fi-icon-btn:focus-visible {
            ring: 2px solid rgb(var(--primary-600));
        }

        .dark .fi-icon-btn {
            color: rgb(var(--gray-500));
        }

        .dark .fi-icon-btn:hover {
            color: rgb(var(--gray-400));
        }

        .dark .fi-icon-btn:focus-visible {
            ring: 2px solid rgb(var(--primary-500));
        }

        .fi-icon-btn-icon {
            height: 1.25rem;
            width: 1.25rem;
        }

        .fi-checkbox-input {
            border-radius: 0.25rem;
            border: none;
            background: white;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            ring: 1px solid rgb(var(--gray-950) / 0.1);
            transition: duration-75;
            color: rgb(var(--primary-600));
        }

        .fi-checkbox-input:checked {
            ring: 0;
        }

        .fi-checkbox-input:focus {
            ring: 2px solid rgb(var(--primary-600));
            ring-offset: 0;
        }

        .fi-checkbox-input:disabled {
            pointer-events: none;
            background: rgb(var(--gray-50));
            color: rgb(var(--gray-50));
        }

        .dark .fi-checkbox-input {
            background: rgb(255 255 255 / 0.05);
            ring: 1px solid rgb(255 255 255 / 0.2);
        }

        .dark .fi-checkbox-input:checked {
            background: rgb(var(--primary-500));
        }

        .dark .fi-checkbox-input:focus {
            ring: 2px solid rgb(var(--primary-500));
        }

        .fi-btn {
            display: inline-grid;
            grid-auto-flow: column;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            outline: none;
            transition: duration-75;
            border-radius: 0.5rem;
            gap: 0.375rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1rem;
            background: rgb(var(--primary-600));
            color: white;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        }

        .fi-btn:hover {
            background: rgb(var(--primary-500));
        }

        .fi-btn:focus-visible {
            ring: 2px solid rgb(var(--primary-500) / 0.5);
        }

        .dark .fi-btn {
            background: rgb(var(--primary-500));
        }

        .dark .fi-btn:hover {
            background: rgb(var(--primary-400));
        }

        .dark .fi-btn:focus-visible {
            ring: 2px solid rgb(var(--primary-400) / 0.5);
        }

        .fi-btn:disabled {
            opacity: 0.7;
            cursor: wait;
        }

        .fi-btn-icon {
            height: 1.25rem;
            width: 1.25rem;
            transition: duration-75;
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .fi-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            outline: none;
            gap: 0.375rem;
        }

        .fi-link:hover .fi-link-label,
        .fi-link:focus-visible .fi-link-label {
            text-decoration: underline;
        }

        .fi-link-label {
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 600;
            color: rgb(var(--primary-600));
        }

        .dark .fi-link-label {
            color: rgb(var(--primary-400));
        }

        .fi-color-danger {
            color: rgb(var(--danger-600));
        }

        .dark .fi-color-danger {
            color: rgb(var(--danger-400));
        }

        .language-switcher {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 10;
        }

        .language-switcher-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: white;
            border-radius: 9999px;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            ring: 1px solid rgb(var(--gray-950) / 0.05);
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 500;
            color: rgb(var(--gray-700));
            transition: all 0.2s;
        }

        .language-switcher-link:hover {
            background: rgb(var(--gray-50));
        }

        .dark .language-switcher-link {
            background: rgb(var(--gray-800));
            ring: 1px solid rgb(255 255 255 / 0.1);
            color: rgb(var(--gray-200));
        }

        .dark .language-switcher-link:hover {
            background: rgb(var(--gray-700));
        }

        .error-alert {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: rgb(var(--danger-50));
            border-left: 4px solid rgb(var(--danger-500));
            border-radius: 0.5rem;
        }

        .dark .error-alert {
            background: rgb(var(--danger-950) / 0.3);
        }

        .error-alert-content {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            color: rgb(var(--danger-700));
        }

        .dark .error-alert-content {
            color: rgb(var(--danger-300));
        }

        .rtl .language-switcher {
            right: auto;
            left: 1.5rem;
        }

        .rtl .fi-input-wrp-suffix {
            border-left: none;
            border-right: 1px solid rgb(var(--gray-200));
            padding-right: 0;
            padding-left: 0.75rem;
        }

        .dark.rtl .fi-input-wrp-suffix {
            border-right-color: rgb(255 255 255 / 0.1);
        }
    </style>
</head>
<body class="min-h-screen antialiased font-normal fi-body bg-gray-50 text-gray-950 dark:bg-gray-950 dark:text-white">
    <!-- Language Switcher -->
    <div class="language-switcher">
        <a href="{{ request()->fullUrlWithQuery(['lang' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}"
           class="language-switcher-link">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
            </svg>
            <span>{{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}</span>
        </a>
    </div>

    <div class="fi-simple-layout">
        <div class="fi-simple-main-ctn">
            <main class="fi-simple-main">
                <section class="grid auto-cols-fr gap-y-6">
                    <!-- Header with Logo -->
                    <header class="fi-simple-header">
                        <!-- Light mode logo -->
                        <img alt="Majalis Admin logo"
                             src="{{ asset('images/logo.webp') }}"
                             style="height: 2.5rem;"
                             class="flex mb-4 fi-logo dark:hidden">

                        <!-- Dark mode logo -->
                        <img alt="Majalis Admin logo"
                             src="{{ asset('images/logo.webp') }}"
                             style="height: 2.5rem;"
                             class="hidden mb-4 fi-logo dark:flex">

                        <h1 class="fi-simple-header-heading">
                            {{ __('dashboard.sign_in_title') }}
                        </h1>
                    </header>

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="error-alert">
                            <div class="error-alert-content">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    @foreach ($errors->all() as $error)
                                        <p>{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST"
                          action="{{ route('login') }}"
                          class="grid fi-form gap-y-6"
                          x-data="{
                              isProcessing: false,
                              passwordFieldType: 'password'
                          }"
                          x-on:submit="isProcessing = true">
                        @csrf

                        <!-- Email Field -->
                        <div class="fi-fo-field-wrp">
                            <div class="grid gap-y-2">
                                <div class="flex items-center justify-between gap-x-3">
                                    <label class="fi-fo-field-wrp-label" for="email">
                                        <span class="text-sm font-medium leading-6">
                                            {{ __('dashboard.email_address') }}
                                            <sup class="font-medium text-danger-600 dark:text-danger-400">*</sup>
                                        </span>
                                    </label>
                                </div>
                                <div class="grid auto-cols-fr gap-y-2">
                                    <div class="fi-input-wrp">
                                        <div class="fi-input-wrp-input">
                                            <input type="email"
                                                   name="email"
                                                   id="email"
                                                   class="fi-input"
                                                   placeholder="name@example.com"
                                                   value="{{ old('email') }}"
                                                   required
                                                   autofocus
                                                   wire:model="email">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div class="fi-fo-field-wrp">
                            <div class="grid gap-y-2">
                                <div class="flex items-center justify-between gap-x-3">
                                    <label class="fi-fo-field-wrp-label" for="password">
                                        <span class="text-sm font-medium leading-6">
                                            {{ __('dashboard.password') }}
                                            <sup class="font-medium text-danger-600 dark:text-danger-400">*</sup>
                                        </span>
                                    </label>
                                    <div class="flex items-center text-sm fi-fo-field-wrp-hint gap-x-3">
                                        <span class="text-gray-500 dark:text-gray-400">
                                            <a href="{{ route('password.request') }}"
                                               class="fi-link group/link relative inline-flex items-center justify-center outline-none gap-1.5">
                                                <span class="text-sm font-semibold fi-link-label">
                                                    {{ __('dashboard.forgot_password_link') }}
                                                </span>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                                <div class="grid auto-cols-fr gap-y-2">
                                    <div class="fi-input-wrp"
                                         x-data="{ showPassword: false }">
                                        <div class="fi-input-wrp-input">
                                            <input :type="showPassword ? 'text' : 'password'"
                                                   name="password"
                                                   id="password"
                                                   class="fi-input"
                                                   placeholder="••••••••"
                                                   required
                                                   wire:model="password">
                                        </div>
                                        <div class="fi-input-wrp-suffix">
                                            <div class="flex items-center gap-3">
                                                <button type="button"
                                                        class="fi-icon-btn"
                                                        @click="showPassword = !showPassword"
                                                        x-show="!showPassword"
                                                        title="{{ __('Show password') }}">
                                                    <span class="sr-only">{{ __('Show password') }}</span>
                                                    <svg class="fi-icon-btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                                                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                        class="fi-icon-btn"
                                                        @click="showPassword = !showPassword"
                                                        x-show="showPassword"
                                                        style="display: none;"
                                                        title="{{ __('Hide password') }}">
                                                    <span class="sr-only">{{ __('Hide password') }}</span>
                                                    <svg class="fi-icon-btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 0 0-1.06 1.06l14.5 14.5a.75.75 0 1 0 1.06-1.06l-1.745-1.745a10.029 10.029 0 0 0 3.3-4.38 1.651 1.651 0 0 0 0-1.185A10.004 10.004 0 0 0 9.999 3a9.956 9.956 0 0 0-4.744 1.194L3.28 2.22ZM7.752 6.69l1.092 1.092a2.5 2.5 0 0 1 3.374 3.373l1.091 1.092a4 4 0 0 0-5.557-5.557Z" clip-rule="evenodd" />
                                                        <path d="m10.748 13.93 2.523 2.523a9.987 9.987 0 0 1-3.27.547c-4.258 0-7.894-2.66-9.337-6.41a1.651 1.651 0 0 1 0-1.186A10.007 10.007 0 0 1 2.839 6.02L6.07 9.252a4 4 0 0 0 4.678 4.678Z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Remember Me Checkbox -->
                        <div class="fi-fo-field-wrp">
                            <div class="grid gap-y-2">
                                <div class="flex items-center justify-between gap-x-3">
                                    <label class="inline-flex items-center fi-fo-field-wrp-label gap-x-3" for="remember">
                                        <input type="checkbox"
                                               name="remember"
                                               id="remember"
                                               class="fi-checkbox-input"
                                               {{ old('remember') ? 'checked' : '' }}>
                                        <span class="text-sm font-medium leading-6">
                                            {{ __('dashboard.remember_me') }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="fi-form-actions">
                            <div class="fi-ac gap-3 grid grid-cols-[repeat(auto-fit,minmax(0,1fr))]">
                                <button type="submit"
                                        class="w-full fi-btn"
                                        :disabled="isProcessing">
                                    <!-- Spinner (shown when processing) -->
                                    <svg fill="none"
                                         viewBox="0 0 24 24"
                                         xmlns="http://www.w3.org/2000/svg"
                                         class="w-5 h-5 text-white transition duration-75 animate-spin fi-btn-icon"
                                         x-show="isProcessing"
                                         style="display: none;">
                                        <path clip-rule="evenodd"
                                              d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                                              fill-rule="evenodd"
                                              fill="currentColor"
                                              opacity="0.2" />
                                        <path d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z"
                                              fill="currentColor" />
                                    </svg>

                                    <!-- Button text -->
                                    <span x-show="!isProcessing" class="fi-btn-label">
                                        {{ __('dashboard.sign_in') }}
                                    </span>
                                    <span x-show="isProcessing" class="fi-btn-label" style="display: none;">
                                        {{ __('Processing...') }}
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Register Link -->
                        <p class="text-sm text-center text-gray-600 dark:text-gray-400">
                            {{ __("dashboard.don't_have_account") }}
                            <a href="{{ route('register') }}"
                               class="fi-link group/link relative inline-flex items-center justify-center outline-none gap-1.5">
                                <span class="text-sm font-semibold fi-link-label">
                                    {{ __('dashboard.create_new_account') }}
                                </span>
                            </a>
                        </p>

                        <!-- Back to Halls Link -->
                        <div class="pt-4 text-center border-t border-gray-200 dark:border-white/10">
                            <a href="{{ route('customer.halls.index') }}"
                               class="inline-flex items-center gap-2 text-sm text-gray-600 transition hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="{{ app()->getLocale() === 'ar' ? 'M15 19l-7-7 7-7' : 'M9 5l7 7-7 7' }}" />
                                </svg>
                                {{ __('dashboard.back_to_halls') }}
                            </a>
                        </div>
                    </form>
                </section>
            </main>
        </div>

        <!-- Footer -->
        <p class="my-8 text-sm text-center text-gray-500 dark:text-gray-400">
            &copy; {{ date('Y') }} Majalis. {{ __('dashboard.all_rights_reserved') }}
        </p>
    </div>

    <!-- Alpine.js for interactive components -->
    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
