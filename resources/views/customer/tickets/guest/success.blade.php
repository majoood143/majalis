@extends('customer.layout')

@section('title', __('tickets_guest.success_title') . ' - Majalis')

@section('content')
<div class="flex items-center justify-center min-h-[70vh] px-4 py-12">
    <div class="w-full max-w-lg text-center">

        {{-- Success Icon --}}
        <div class="flex justify-center mb-6">
            <div class="flex items-center justify-center w-20 h-20 bg-green-100 rounded-full">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <h1 class="mb-2 text-2xl font-bold text-gray-900">{{ __('tickets_guest.success_title') }}</h1>
        <p class="mb-6 text-gray-600">{{ __('tickets_guest.success_subtitle') }}</p>

        {{-- Ticket reference box --}}
        <div class="inline-block px-8 py-5 mb-8 bg-white rounded-xl shadow-md">
            <p class="mb-1 text-xs font-semibold tracking-widest text-gray-400 uppercase">
                {{ __('tickets_guest.your_reference') }}
            </p>
            <p class="text-2xl font-bold tracking-widest text-indigo-600">{{ $ticketNumber }}</p>
            @if($guestEmail)
                <p class="mt-2 text-sm text-gray-500">
                    {{ __('tickets_guest.confirmation_sent_to') }} <strong>{{ $guestEmail }}</strong>
                </p>
            @endif
        </div>

        {{-- Account creation CTA --}}
        <div class="p-6 mb-6 bg-indigo-50 border border-indigo-200 rounded-xl">
            <div class="flex items-start gap-3 text-start">
                <svg class="w-6 h-6 text-indigo-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-semibold text-indigo-900">{{ __('tickets_guest.create_account_cta_title') }}</p>
                    <p class="mt-1 text-sm text-indigo-700">
                        {{ __('tickets_guest.create_account_cta_body') }}
                    </p>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-1 mt-3 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
                        {{ __('tickets_guest.create_account_link') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-center">
            <a href="{{ route('home') }}"
               class="px-6 py-2.5 font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                {{ __('tickets_guest.back_home') }}
            </a>
            <a href="{{ route('guest.tickets.verify') }}"
               class="px-6 py-2.5 font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                {{ __('tickets_guest.submit_another') }}
            </a>
        </div>

    </div>
</div>
@endsection
