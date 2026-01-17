{{--
    Booking Method Choice Modal
    
    A reusable modal component that allows users to choose between:
    - Logging in with existing account
    - Creating a new account
    - Continuing as a guest
    
    Usage: @include('customer.components.booking-choice-modal', ['hall' => $hall])
    
    Requires Alpine.js for modal functionality.
    
    @var Hall $hall The hall being booked
--}}

<div 
    x-data="{ open: false }"
    x-on:open-booking-modal.window="open = true"
    x-on:keydown.escape.window="open = false"
>
    {{-- Trigger Button (can be customized or replaced) --}}
    <button 
        type="button"
        @click="open = true"
        class="w-full sm:w-auto px-8 py-4 bg-primary-600 text-white font-bold rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-4 focus:ring-primary-300 transition text-lg"
    >
        <span class="flex items-center justify-center">
            <svg class="w-6 h-6 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            {{ __('Book Now') }}
        </span>
    </button>

    {{-- Modal Backdrop --}}
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        x-cloak
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            {{-- Backdrop overlay --}}
            <div 
                class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                @click="open = false"
            ></div>

            {{-- Modal Content --}}
            <div 
                x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative inline-block w-full max-w-lg p-0 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-xl sm:my-8"
                dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
            >
                {{-- Close Button --}}
                <button 
                    type="button"
                    @click="open = false"
                    class="absolute top-4 {{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }} text-gray-400 hover:text-gray-600 z-10"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                {{-- Modal Header --}}
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-blue-50">
                    <h3 class="text-xl font-bold text-gray-900">{{ __('guest.modal_title') }}</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ $hall->getTranslation('name', app()->getLocale()) }}
                    </p>
                </div>

                {{-- Modal Body - Options --}}
                <div class="p-6 space-y-4">
                    
                    {{-- Option 1: Login --}}
                    <a 
                        href="{{ route('login', ['redirect' => route('customer.book', $hall->slug)]) }}"
                        class="block p-4 border-2 border-gray-200 rounded-xl hover:border-primary-500 hover:bg-primary-50 transition group"
                    >
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center group-hover:bg-primary-200 transition">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                            </div>
                            <div class="ms-4 flex-1">
                                <h4 class="font-semibold text-gray-900">{{ __('guest.modal_login_option') }}</h4>
                                <p class="text-sm text-gray-500">{{ __('guest.modal_login_description') }}</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>

                    {{-- Option 2: Register --}}
                    <a 
                        href="{{ route('register', ['redirect' => route('customer.book', $hall->slug)]) }}"
                        class="block p-4 border-2 border-gray-200 rounded-xl hover:border-primary-500 hover:bg-primary-50 transition group"
                    >
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center group-hover:bg-green-200 transition">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                            </div>
                            <div class="ms-4 flex-1">
                                <h4 class="font-semibold text-gray-900">{{ __('guest.modal_register_option') }}</h4>
                                <p class="text-sm text-gray-500">{{ __('guest.modal_register_description') }}</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>

                    {{-- Divider --}}
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">{{ __('Or') }}</span>
                        </div>
                    </div>

                    {{-- Option 3: Continue as Guest --}}
                    <a 
                        href="{{ route('guest.book', ['hall' => $hall->slug, 'lang' => app()->getLocale()]) }}"
                        class="block p-4 border-2 border-dashed border-gray-300 rounded-xl hover:border-primary-400 hover:bg-gray-50 transition group"
                    >
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-gray-200 transition">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="ms-4 flex-1">
                                <h4 class="font-semibold text-gray-900">{{ __('guest.modal_guest_option') }}</h4>
                                <p class="text-sm text-gray-500">{{ __('guest.modal_guest_description') }}</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>

                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <p class="text-xs text-center text-gray-500">
                        {{ __('By booking, you agree to our') }}
                        <a href="#" class="text-primary-600 hover:underline">{{ __('Terms of Service') }}</a>
                        {{ __('and') }}
                        <a href="#" class="text-primary-600 hover:underline">{{ __('Privacy Policy') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
