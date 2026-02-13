{{--
|--------------------------------------------------------------------------
| Empty State — No halls found
|--------------------------------------------------------------------------
|
| LOCATION: resources/views/customer/halls/partials/empty-state.blade.php
|
--}}

<div class="col-span-full py-16 text-center">
    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
    </div>
    <h3 class="text-lg font-bold text-gray-700 mb-2">{{ __('halls.no_halls_found') }}</h3>
    <p class="text-gray-500 text-sm mb-5">{{ __('halls.adjust_filters') }}</p>
    <a href="{{ route('customer.halls.index') }}?lang={{ app()->getLocale() }}"
       class="inline-flex items-center gap-2 px-6 py-3 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        {{ __('halls.clear_filters') }}
    </a>
</div>
