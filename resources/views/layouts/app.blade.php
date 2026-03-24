<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/webp">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

         @include('layouts.footer')
    </body>
</html>
