@extends('customer.layout')

@section('title', __('tickets_guest.verify_title') . ' - Majalis')

@section('content')
<div class="flex items-center justify-center min-h-[70vh] px-4 py-12">
    <div class="w-full max-w-md">

        {{-- Logo / Icon --}}
        <div class="flex justify-center mb-6">
            <div class="flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
        </div>

        {{-- Heading --}}
        <div class="mb-8 text-center">
            <h1 class="mb-2 text-2xl font-bold text-gray-900">{{ __('tickets_guest.verify_title') }}</h1>
            <p class="text-sm text-gray-600">{{ __('tickets_guest.verify_subtitle') }}</p>
        </div>

        {{-- Flash errors --}}
        @if(session('error'))
            <div class="p-4 mb-6 text-red-800 bg-red-100 border border-red-200 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Step indicator --}}
        <div class="flex items-center mb-8">
            <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white bg-indigo-600 rounded-full shrink-0">1</div>
            <div class="flex-1 h-1 mx-2 bg-gray-200 rounded">
                <div class="h-1 bg-indigo-200 rounded" style="width:0%"></div>
            </div>
            <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-gray-400 bg-gray-100 rounded-full shrink-0">2</div>
        </div>

        {{-- Verification Card --}}
        <div class="bg-white rounded-xl shadow-md p-8">
            <form action="{{ route('guest.tickets.lookup') }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label for="lookup" class="block mb-1.5 text-sm font-medium text-gray-700">
                        {{ __('tickets_guest.lookup_label') }}
                    </label>

                    {{-- Hint icons --}}
                    <div class="flex flex-wrap gap-4 mb-3 text-xs text-gray-500">
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                            {{ __('tickets_guest.hint_number') }}
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ __('tickets_guest.hint_email') }}
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ __('tickets_guest.hint_phone') }}
                        </span>
                    </div>

                    <input type="text"
                           id="lookup"
                           name="lookup"
                           value="{{ old('lookup') }}"
                           placeholder="{{ __('tickets_guest.lookup_placeholder') }}"
                           autofocus
                           class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                  {{ $errors->has('lookup') ? 'border-red-400' : 'border-gray-300' }}">
                    @error('lookup')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full px-6 py-3 font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                    {{ __('tickets_guest.verify_btn') }}
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center my-6">
                <div class="flex-1 border-t border-gray-200"></div>
                <span class="px-3 text-xs text-gray-400">{{ __('tickets_guest.or') }}</span>
                <div class="flex-1 border-t border-gray-200"></div>
            </div>

            {{-- Login prompt --}}
            <p class="text-sm text-center text-gray-600">
                {{ __('tickets_guest.have_account') }}
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-800">
                    {{ __('tickets_guest.login_link') }}
                </a>
            </p>
        </div>

    </div>
</div>
@endsection
