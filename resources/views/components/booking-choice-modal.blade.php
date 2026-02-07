{{--
    Booking Method Choice Modal Component

    This component displays a modal when user clicks "Book Now" on hall details page.
    Allows user to choose between:
    - Log In (existing users)
    - Create Account (new users)
    - Continue as Guest (no account required)

    Usage in halls/show.blade.php:
    1. Include this component: @include('components.booking-choice-modal', ['hall' => $hall])
    2. Add x-data to parent or use the button below

    @var Hall $hall The hall being booked
--}}

{{-- The modal container with its own Alpine.js state --}}
<div
    x-data="bookingModal()"
    x-on:open-booking-modal.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-init="init()"
    class="relative z-[100]"
    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"
        @click="open = false"
    ></div>

    {{-- Modal --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="fixed inset-0 z-50 overflow-y-auto"
    >
        <div class="flex items-center justify-center min-h-full p-4">
            <div
                class="relative w-full max-w-md overflow-hidden transition-all transform bg-white shadow-2xl rounded-2xl"
                @click.away="open = false"
            >
                {{-- Close Button --}}
                <button
                    @click="open = false"
                    class="absolute text-gray-400 transition top-4 end-4 hover:text-gray-600"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                {{-- Header --}}
                <div class="px-6 pt-6 pb-4 text-center">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-primary-100">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">{{ __('guest.modal_title') ?? __('How would you like to book?') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @php
                            $hallName = '';
                            try {
                                $hallName = $hall->getTranslation('name', app()->getLocale());
                            } catch (\Exception $e) {
                                $hallName = is_array($hall->name) ? ($hall->name[app()->getLocale()] ?? $hall->name['en'] ?? '') : $hall->name;
                            }
                        @endphp
                        {{ $hallName }}
                    </p>
                </div>

                {{-- Options --}}
                <div class="px-6 pb-6 space-y-3">
                    {{-- Login Option --}}
                    @php
                        // Flexible route handling - try different possible login routes
                        $loginUrl = '#';
                        $registerUrl = '#';
                        $bookingRedirect = url()->current(); // Fallback to current page

                        try {
                            if (Route::has('customer.book')) {
                                $bookingRedirect = route('customer.book', $hall->slug);
                            }
                        } catch (\Exception $e) {}

                        // Try different login route names
                        try {
                            if (Route::has('login')) {
                                $loginUrl = route('login') . '?redirect=' . urlencode($bookingRedirect);
                            } elseif (Route::has('filament.admin.auth.login')) {
                                $loginUrl = route('filament.admin.auth.login') . '?lang=' . app()->getLocale();
                            }
                        } catch (\Exception $e) {
                            $loginUrl = '/login?redirect=' . urlencode($bookingRedirect);
                        }

                        // Try different register route names
                        try {
                            if (Route::has('register')) {
                                $registerUrl = route('register') . '?redirect=' . urlencode($bookingRedirect);
                            } elseif (Route::has('filament.admin.auth.register')) {
                                $registerUrl = route('filament.admin.auth.register') . '?lang=' . app()->getLocale();
                            }
                        } catch (\Exception $e) {
                            $registerUrl = '/register?redirect=' . urlencode($bookingRedirect);
                        }
                    @endphp

                    <a
                        href="{{ $loginUrl }}"
                        class="flex items-center p-4 transition border-2 border-gray-200 rounded-xl hover:border-primary-500 hover:bg-primary-50 group"
                    >
                        <div class="flex items-center justify-center w-12 h-12 transition bg-blue-100 rounded-full group-hover:bg-blue-200">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                        </div>
                        <div class="flex-1 ms-4">
                            <div class="font-semibold text-gray-900">{{ __('guest.modal_login_option') ?? __('Log In') }}</div>
                            <div class="text-sm text-gray-500">{{ __('guest.modal_login_description') ?? __('Access your account to manage bookings easily') }}</div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    {{-- Register Option --}}
                    <a
                        href="{{ $registerUrl }}"
                        class="flex items-center p-4 transition border-2 border-gray-200 rounded-xl hover:border-primary-500 hover:bg-primary-50 group"
                    >
                        <div class="flex items-center justify-center w-12 h-12 transition bg-green-100 rounded-full group-hover:bg-green-200">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 ms-4">
                            <div class="font-semibold text-gray-900">{{ __('guest.modal_register_option') ?? __('Create Account') }}</div>
                            <div class="text-sm text-gray-500">{{ __('guest.modal_register_description') ?? __('Sign up for easier booking management') }}</div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    {{-- Divider --}}
                    <div class="relative py-2">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="px-4 text-sm text-gray-500 bg-white">{{ __('guest.or') }}</span>
                        </div>
                    </div>

                    {{-- Guest Option --}}
                    @php
                        $guestBookUrl = '#';
                        try {
                            if (Route::has('guest.book')) {
                                $guestBookUrl = route('guest.book', ['hall' => $hall->slug, 'lang' => app()->getLocale()]);
                            } else {
                                $guestBookUrl = '/guest/book/' . $hall->slug . '?lang=' . app()->getLocale();
                            }
                        } catch (\Exception $e) {
                            $guestBookUrl = '/guest/book/' . $hall->slug . '?lang=' . app()->getLocale();
                        }
                    @endphp
                    <a
                        href="{{ $guestBookUrl }}"
                        class="flex items-center p-4 transition border-2 border-gray-300 border-dashed rounded-xl hover:border-primary-500 hover:bg-gray-50 group"
                    >
                        <div class="flex items-center justify-center w-12 h-12 transition bg-gray-100 rounded-full group-hover:bg-gray-200">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 ms-4">
                            <div class="font-semibold text-gray-900">{{ __('guest.modal_guest_option') ?? __('Continue as Guest') }}</div>
                            <div class="text-sm text-gray-500">{{ __('guest.modal_guest_description') ?? __('Book without creating an account') }}</div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                {{-- Footer Note --}}
                <div class="px-6 pb-6">
                    <p class="text-xs text-center text-gray-400">
                        {{ __('guest.terms_agree') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add x-cloak style to prevent flash on page load --}}
<style>
    [x-cloak] { display: none !important; }
</style>

{{-- Alpine.js component definition --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('bookingModal', () => ({
            open: false,
            init() {
                // Ensure modal starts closed
                this.open = false;
            }
        }));
    });

    // Fallback for when Alpine.js loads after this script
    window.bookingModal = function() {
        return {
            open: false,
            init() {
                this.open = false;
            }
        };
    };
</script>
