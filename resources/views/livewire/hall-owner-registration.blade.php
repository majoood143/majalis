{{--
    Root element must NOT have x-data="" — Livewire 3 owns this element's Alpine scope.
    Adding x-data here overwrites Livewire's internal Alpine setup and silently breaks
    wire:click / wire:model / all reactivity.
--}}
<div id="registration-wizard" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

    {{-- ─── Step Progress Bar ─────────────────────────────────────────── --}}
    <div class="mb-8">
        <div class="flex items-center">
            @php
                $stepLabels = [
                    1 => __('hall-owner.registration.steps.account'),
                    2 => __('hall-owner.registration.steps.business'),
                    3 => __('hall-owner.registration.steps.contact'),
                    4 => __('hall-owner.registration.steps.bank'),
                    5 => __('hall-owner.registration.steps.documents'),
                ];
            @endphp

            @foreach($stepLabels as $num => $label)
                <div class="flex flex-col items-center {{ !$loop->last ? 'flex-1' : '' }}">

                    {{-- Step circle --}}
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold border-2 transition-all duration-300 select-none"
                         style="
                            @if($currentStep === $num)
                                background-color:#B9916D; border-color:#B9916D; color:#fff; box-shadow:0 4px 12px rgba(185,145,109,0.3);
                            @elseif($currentStep > $num)
                                background-color:#B9916D; border-color:#B9916D; color:#fff; opacity:0.75;
                            @else
                                background-color:#fff; border-color:#E8D5C4; color:#8A8A8C;
                            @endif
                         ">
                        @if($currentStep > $num)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            {{ $num }}
                        @endif
                    </div>

                    {{-- Step label --}}
                    <span class="text-xs mt-1.5 font-medium text-center leading-tight hidden sm:block w-16"
                          style="
                            @if($currentStep === $num)
                                color:#B9916D;
                            @elseif($currentStep > $num)
                                color:#B9916D; opacity:0.7;
                            @else
                                color:#8A8A8C;
                            @endif
                          ">{{ $label }}</span>
                </div>

                @if(!$loop->last)
                    <div class="flex-1 mb-5 mx-1">
                        <div class="h-0.5 w-full transition-all duration-500"
                             style="{{ $currentStep > $num ? 'background-color:#B9916D; opacity:0.5;' : 'background-color:#E8D5C4;' }}">
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Overall progress bar --}}
        <div class="w-full rounded-full h-1.5 mt-4" style="background-color:#F8F5F2; border:1px solid #E8D5C4;">
            <div class="h-1.5 rounded-full transition-all duration-500"
                 style="width:{{ (($currentStep - 1) / ($totalSteps - 1)) * 100 }}%; background-color:#B9916D;">
            </div>
        </div>
    </div>

    {{-- ─── Global Error Banner ──────────────────────────────────────────── --}}
    @if($errors->any())
        <div id="error-banner" class="mb-6 rounded-xl p-4"
             style="background-color:#fff5f5; border:1px solid #fecaca;">
            <div class="flex gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center"
                     style="background-color:#fee2e2;">
                    <svg class="w-4 h-4" style="color:#ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold mb-1" style="color:#991b1b;">
                        {{ __('hall-owner.registration.errors.fix_below') }}
                    </p>
                    <ul class="text-sm space-y-0.5 list-disc list-inside" style="color:#b91c1c;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- ─────────────────────────────────────────────────────────────────── --}}
    {{-- STEP 1: Account Info                                                --}}
    {{-- ─────────────────────────────────────────────────────────────────── --}}
    @if($currentStep === 1)
        <div wire:key="step-1">
            <div class="mb-6 pb-4" style="border-bottom:1px solid #E8D5C4;">
                <h2 class="text-lg font-semibold" style="color:#2C2A2A;">
                    {{ __('hall-owner.registration.steps.account') }}
                </h2>
                <p class="mt-1 text-sm" style="color:#8A8A8C;">{{ __('hall-owner.registration.steps.account_desc') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                {{-- Full Name --}}
                <div class="sm:col-span-2">
                    <label for="f-name" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">
                        {{ __('hall-owner.registration.fields.name') }}
                        <span class="ms-0.5" style="color:#ef4444;">*</span>
                    </label>
                    <input
                        id="f-name"
                        type="text"
                        wire:model="name"
                        autocomplete="name"
                        placeholder="{{ __('hall-owner.registration.placeholders.name') }}"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm transition-colors"
                        style="border:1px solid {{ $errors->has('name') ? '#fca5a5' : '#E8D5C4' }}; background:{{ $errors->has('name') ? '#fff5f5' : '#fff' }}; color:#2C2A2A;"
                    >
                    @error('name')
                        <p class="mt-1.5 flex items-center gap-1 text-xs" style="color:#dc2626;">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="f-email" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">
                        {{ __('hall-owner.registration.fields.email') }}
                        <span class="ms-0.5" style="color:#ef4444;">*</span>
                    </label>
                    <input
                        id="f-email"
                        type="email"
                        wire:model="email"
                        autocomplete="email"
                        placeholder="you@example.com"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm transition-colors"
                        style="border:1px solid {{ $errors->has('email') ? '#fca5a5' : '#E8D5C4' }}; background:{{ $errors->has('email') ? '#fff5f5' : '#fff' }}; color:#2C2A2A;"
                    >
                    @error('email')
                        <p class="mt-1.5 flex items-center gap-1 text-xs" style="color:#dc2626;">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="f-phone" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">
                        {{ __('hall-owner.registration.fields.phone') }}
                        <span class="ms-0.5" style="color:#ef4444;">*</span>
                    </label>
                    <input
                        id="f-phone"
                        type="tel"
                        wire:model="phone"
                        autocomplete="tel"
                        placeholder="+968 XXXX XXXX"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm transition-colors"
                        style="border:1px solid {{ $errors->has('phone') ? '#fca5a5' : '#E8D5C4' }}; background:{{ $errors->has('phone') ? '#fff5f5' : '#fff' }}; color:#2C2A2A;"
                    >
                    @error('phone')
                        <p class="mt-1.5 flex items-center gap-1 text-xs" style="color:#dc2626;">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="f-password" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">
                        {{ __('hall-owner.registration.fields.password') }}
                        <span class="ms-0.5" style="color:#ef4444;">*</span>
                    </label>
                    <input
                        id="f-password"
                        type="password"
                        wire:model="password"
                        autocomplete="new-password"
                        placeholder="{{ __('hall-owner.registration.placeholders.password') }}"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm transition-colors"
                        style="border:1px solid {{ $errors->has('password') ? '#fca5a5' : '#E8D5C4' }}; background:{{ $errors->has('password') ? '#fff5f5' : '#fff' }}; color:#2C2A2A;"
                    >
                    @error('password')
                        <p class="mt-1.5 flex items-center gap-1 text-xs" style="color:#dc2626;">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @else
                        <p class="mt-1.5 text-xs" style="color:#8A8A8C;">{{ __('hall-owner.registration.hints.password_min') }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="f-password-confirm" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">
                        {{ __('hall-owner.registration.fields.password_confirmation') }}
                        <span class="ms-0.5" style="color:#ef4444;">*</span>
                    </label>
                    <input
                        id="f-password-confirm"
                        type="password"
                        wire:model="password_confirmation"
                        autocomplete="new-password"
                        placeholder="{{ __('hall-owner.registration.placeholders.password') }}"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm transition-colors"
                        style="border:1px solid {{ $errors->has('password_confirmation') ? '#fca5a5' : '#E8D5C4' }}; background:{{ $errors->has('password_confirmation') ? '#fff5f5' : '#fff' }}; color:#2C2A2A;"
                    >
                    @error('password_confirmation')
                        <p class="mt-1.5 flex items-center gap-1 text-xs" style="color:#dc2626;">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>
        </div>
    @endif

    {{-- ─────────────────────────────────────────────────────────────────── --}}
    {{-- STEP 2: Business Info                                               --}}
    {{-- ─────────────────────────────────────────────────────────────────── --}}
    @if($currentStep === 2)
        <div wire:key="step-2">
            <div class="mb-6 pb-4" style="border-bottom:1px solid #E8D5C4;">
                <h2 class="text-lg font-semibold" style="color:#2C2A2A;">{{ __('hall-owner.registration.steps.business') }}</h2>
                <p class="mt-1 text-sm" style="color:#8A8A8C;">{{ __('hall-owner.registration.steps.business_desc') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                <div>
                    <label for="f-bname" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">
                        {{ __('hall-owner.fields.business_name') }}
                        <span class="ms-0.5" style="color:#ef4444;">*</span>
                    </label>
                    <input
                        id="f-bname"
                        type="text"
                        wire:model="business_name"
                        placeholder="{{ __('hall-owner.registration.placeholders.business_name') }}"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm transition-colors"
                        style="border:1px solid {{ $errors->has('business_name') ? '#fca5a5' : '#E8D5C4' }}; background:{{ $errors->has('business_name') ? '#fff5f5' : '#fff' }}; color:#2C2A2A;"
                    >
                    @error('business_name')
                        <p class="mt-1.5 flex items-center gap-1 text-xs" style="color:#dc2626;">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="f-bname-ar" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">
                        {{ __('hall-owner.fields.business_name_ar') }}
                        <span class="font-normal text-xs ms-1" style="color:#8A8A8C;">({{ __('hall-owner.registration.fields.optional') }})</span>
                    </label>
                    <input
                        id="f-bname-ar"
                        type="text"
                        wire:model="business_name_ar"
                        dir="rtl"
                        placeholder="{{ __('hall-owner.registration.placeholders.business_name_ar') }}"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm"
                        style="border:1px solid #E8D5C4; background:#fff; color:#2C2A2A;"
                    >
                </div>

                <div>
                    <label for="f-cr" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">
                        {{ __('hall-owner.fields.commercial_registration') }}
                        <span class="ms-0.5" style="color:#ef4444;">*</span>
                    </label>
                    <input
                        id="f-cr"
                        type="text"
                        wire:model="commercial_registration"
                        placeholder="{{ __('hall-owner.registration.placeholders.commercial_registration') }}"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm transition-colors"
                        style="border:1px solid {{ $errors->has('commercial_registration') ? '#fca5a5' : '#E8D5C4' }}; background:{{ $errors->has('commercial_registration') ? '#fff5f5' : '#fff' }}; color:#2C2A2A;"
                    >
                    @error('commercial_registration')
                        <p class="mt-1.5 flex items-center gap-1 text-xs" style="color:#dc2626;">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="f-tax" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">
                        {{ __('hall-owner.fields.tax_number') }}
                        <span class="font-normal text-xs ms-1" style="color:#8A8A8C;">({{ __('hall-owner.registration.fields.optional') }})</span>
                    </label>
                    <input
                        id="f-tax"
                        type="text"
                        wire:model="tax_number"
                        placeholder="{{ __('hall-owner.registration.placeholders.tax_number') }}"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm"
                        style="border:1px solid #E8D5C4; background:#fff; color:#2C2A2A;"
                    >
                </div>

            </div>
        </div>
    @endif

    {{-- ─────────────────────────────────────────────────────────────────── --}}
    {{-- STEP 3: Contact Details                                             --}}
    {{-- ─────────────────────────────────────────────────────────────────── --}}
    @if($currentStep === 3)
        <div wire:key="step-3">
            <div class="mb-6 pb-4" style="border-bottom:1px solid #E8D5C4;">
                <h2 class="text-lg font-semibold" style="color:#2C2A2A;">{{ __('hall-owner.registration.steps.contact') }}</h2>
                <p class="mt-1 text-sm" style="color:#8A8A8C;">{{ __('hall-owner.registration.steps.contact_desc') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                <div>
                    <label for="f-bphone" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">
                        {{ __('hall-owner.fields.business_phone') }}
                        <span class="ms-0.5" style="color:#ef4444;">*</span>
                    </label>
                    <input
                        id="f-bphone"
                        type="tel"
                        wire:model="business_phone"
                        placeholder="+968 XXXX XXXX"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm transition-colors"
                        style="border:1px solid {{ $errors->has('business_phone') ? '#fca5a5' : '#E8D5C4' }}; background:{{ $errors->has('business_phone') ? '#fff5f5' : '#fff' }}; color:#2C2A2A;"
                    >
                    @error('business_phone')
                        <p class="mt-1.5 flex items-center gap-1 text-xs" style="color:#dc2626;">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="f-bemail" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">
                        {{ __('hall-owner.fields.business_email') }}
                        <span class="font-normal text-xs ms-1" style="color:#8A8A8C;">({{ __('hall-owner.registration.fields.optional') }})</span>
                    </label>
                    <input
                        id="f-bemail"
                        type="email"
                        wire:model="business_email"
                        placeholder="business@example.com"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm transition-colors"
                        style="border:1px solid {{ $errors->has('business_email') ? '#fca5a5' : '#E8D5C4' }}; background:{{ $errors->has('business_email') ? '#fff5f5' : '#fff' }}; color:#2C2A2A;"
                    >
                    @error('business_email')
                        <p class="mt-1.5 flex items-center gap-1 text-xs" style="color:#dc2626;">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="f-addr" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">
                        {{ __('hall-owner.fields.business_address') }}
                        <span class="ms-0.5" style="color:#ef4444;">*</span>
                    </label>
                    <textarea
                        id="f-addr"
                        wire:model="business_address"
                        rows="3"
                        placeholder="{{ __('hall-owner.registration.placeholders.business_address') }}"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm resize-none transition-colors"
                        style="border:1px solid {{ $errors->has('business_address') ? '#fca5a5' : '#E8D5C4' }}; background:{{ $errors->has('business_address') ? '#fff5f5' : '#fff' }}; color:#2C2A2A;"
                    ></textarea>
                    @error('business_address')
                        <p class="mt-1.5 flex items-center gap-1 text-xs" style="color:#dc2626;">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="f-addr-ar" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">
                        {{ __('hall-owner.fields.business_address_ar') }}
                        <span class="font-normal text-xs ms-1" style="color:#8A8A8C;">({{ __('hall-owner.registration.fields.optional') }})</span>
                    </label>
                    <textarea
                        id="f-addr-ar"
                        wire:model="business_address_ar"
                        rows="3"
                        dir="rtl"
                        placeholder="{{ __('hall-owner.registration.placeholders.business_address_ar') }}"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm resize-none"
                        style="border:1px solid #E8D5C4; background:#fff; color:#2C2A2A;"
                    ></textarea>
                </div>

            </div>
        </div>
    @endif

    {{-- ─────────────────────────────────────────────────────────────────── --}}
    {{-- STEP 4: Bank Details                                                --}}
    {{-- ─────────────────────────────────────────────────────────────────── --}}
    @if($currentStep === 4)
        <div wire:key="step-4">
            <div class="mb-6 pb-4" style="border-bottom:1px solid #E8D5C4;">
                <h2 class="text-lg font-semibold" style="color:#2C2A2A;">{{ __('hall-owner.registration.steps.bank') }}</h2>
                <p class="mt-1 text-sm" style="color:#8A8A8C;">{{ __('hall-owner.registration.steps.bank_desc') }}</p>
            </div>

            <div class="mb-5 flex items-start gap-2.5 p-3.5 rounded-lg"
                 style="background-color:rgba(185,145,109,0.08); border:1px solid rgba(185,145,109,0.25);">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" style="color:#B9916D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs leading-relaxed" style="color:#8A8A8C;">{{ __('hall-owner.registration.bank_optional_note') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                <div>
                    <label for="f-bank" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">{{ __('hall-owner.fields.bank_name') }}</label>
                    <input id="f-bank" type="text" wire:model="bank_name"
                           placeholder="{{ __('hall-owner.registration.placeholders.bank_name') }}"
                           class="w-full px-3.5 py-2.5 rounded-lg text-sm"
                           style="border:1px solid #E8D5C4; background:#fff; color:#2C2A2A;">
                </div>

                <div>
                    <label for="f-baccname" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">{{ __('hall-owner.fields.bank_account_name') }}</label>
                    <input id="f-baccname" type="text" wire:model="bank_account_name"
                           placeholder="{{ __('hall-owner.registration.placeholders.bank_account_name') }}"
                           class="w-full px-3.5 py-2.5 rounded-lg text-sm"
                           style="border:1px solid #E8D5C4; background:#fff; color:#2C2A2A;">
                </div>

                <div>
                    <label for="f-baccno" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">{{ __('hall-owner.fields.bank_account_number') }}</label>
                    <input id="f-baccno" type="text" wire:model="bank_account_number"
                           inputmode="numeric"
                           placeholder="{{ __('hall-owner.registration.placeholders.bank_account_number') }}"
                           class="w-full px-3.5 py-2.5 rounded-lg text-sm transition-colors"
                           style="border:1px solid {{ $errors->has('bank_account_number') ? '#fca5a5' : '#E8D5C4' }}; background:{{ $errors->has('bank_account_number') ? '#fff5f5' : '#fff' }}; color:#2C2A2A;">
                    @error('bank_account_number')
                        <p class="mt-1.5 flex items-center gap-1 text-xs" style="color:#dc2626;">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="f-iban" class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">{{ __('hall-owner.fields.iban') }}</label>
                    <input id="f-iban" type="text" wire:model="iban"
                           placeholder="OM00 0000 0000 0000 0000 0000"
                           class="w-full px-3.5 py-2.5 rounded-lg text-sm"
                           style="border:1px solid #E8D5C4; background:#fff; color:#2C2A2A;">
                </div>

            </div>
        </div>
    @endif

    {{-- ─────────────────────────────────────────────────────────────────── --}}
    {{-- STEP 5: Documents                                                   --}}
    {{-- ─────────────────────────────────────────────────────────────────── --}}
    @if($currentStep === 5)
        <div wire:key="step-5">
            <div class="mb-6 pb-4" style="border-bottom:1px solid #E8D5C4;">
                <h2 class="text-lg font-semibold" style="color:#2C2A2A;">{{ __('hall-owner.registration.steps.documents') }}</h2>
                <p class="mt-1 text-sm" style="color:#8A8A8C;">{{ __('hall-owner.registration.steps.documents_desc') }}</p>
            </div>

            <div class="space-y-5">
                @php
                    $uploadFields = [
                        'cr-doc'  => ['model' => 'commercial_registration_document', 'label' => __('hall-owner.registration.fields.cr_document'),     'required' => true],
                        'id-doc'  => ['model' => 'identity_document',                'label' => __('hall-owner.registration.fields.identity_document'), 'required' => true],
                        'tax-doc' => ['model' => 'tax_certificate',                  'label' => __('hall-owner.registration.fields.tax_certificate'),   'required' => false],
                    ];
                @endphp

                @foreach($uploadFields as $inputId => $field)
                    <div wire:key="upload-{{ $inputId }}">
                        <label class="block text-sm font-medium mb-1.5" style="color:#2C2A2A;">
                            {{ $field['label'] }}
                            @if($field['required'])
                                <span class="ms-0.5" style="color:#ef4444;">*</span>
                            @else
                                <span class="font-normal text-xs ms-1" style="color:#8A8A8C;">({{ __('hall-owner.registration.fields.optional') }})</span>
                            @endif
                        </label>

                        <label for="{{ $inputId }}" class="block cursor-pointer">
                            <div class="upload-drop border-2 border-dashed rounded-xl p-5 text-center transition-all"
                                 style="
                                    @if($errors->has($field['model']))
                                        border-color:#fca5a5; background-color:#fff5f5;
                                    @elseif(!$errors->has($field['model']) && $this->{$field['model']})
                                        border-color:rgba(185,145,109,0.5); background-color:rgba(185,145,109,0.06);
                                    @else
                                        border-color:#E8D5C4; background-color:#F8F5F2;
                                    @endif
                                 ">
                                <input
                                    type="file"
                                    id="{{ $inputId }}"
                                    wire:model="{{ $field['model'] }}"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    class="sr-only"
                                >

                                @if(!$errors->has($field['model']) && $this->{$field['model']})
                                    <svg class="w-7 h-7 mx-auto mb-2" style="color:#B9916D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-sm font-medium truncate max-w-xs mx-auto" style="color:#B9916D;">
                                        {{ $this->{$field['model']}->getClientOriginalName() }}
                                    </p>
                                    <p class="text-xs mt-1" style="color:#8A8A8C;">{{ __('hall-owner.registration.fields.click_to_change') }}</p>
                                @else
                                    <svg class="w-7 h-7 mx-auto mb-2" style="color:#8A8A8C;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-sm font-medium" style="color:#2C2A2A;">{{ __('hall-owner.registration.fields.upload_or_drag') }}</p>
                                    <p class="text-xs mt-0.5" style="color:#8A8A8C;">PDF, JPG, PNG {{ __('hall-owner.registration.fields.max_size') }}</p>
                                @endif

                                <div wire:loading wire:target="{{ $field['model'] }}" class="mt-2">
                                    <div class="inline-flex items-center gap-1.5 text-xs" style="color:#B9916D;">
                                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                        {{ __('hall-owner.registration.fields.uploading') }}
                                    </div>
                                </div>
                            </div>
                        </label>

                        @error($field['model'])
                            <p class="mt-1.5 flex items-center gap-1 text-xs" style="color:#dc2626;">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ─── Navigation Buttons ─────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mt-8 pt-5" style="border-top:1px solid #E8D5C4;">

        <div>
            @if($currentStep > 1)
                <button
                    type="button"
                    wire:click="prevStep"
                    wire:loading.attr="disabled"
                    wire:target="prevStep"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    style="color:#2C2A2A; background:#fff; border:1px solid #E8D5C4;"
                    onmouseover="this.style.background='#F8F5F2'" onmouseout="this.style.background='#fff'"
                >
                    <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('hall-owner.registration.buttons.back') }}
                </button>
            @endif
        </div>

        <div class="flex items-center gap-3">
            <span class="text-xs tabular-nums" style="color:#8A8A8C;">
                {{ __('hall-owner.registration.step_of', ['current' => $currentStep, 'total' => $totalSteps]) }}
            </span>

            @if($currentStep < $totalSteps)
                <button
                    type="button"
                    wire:click="nextStep"
                    wire:loading.attr="disabled"
                    wire:target="nextStep"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-lg transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                    style="background:#B9916D; border:none;"
                    onmouseover="this.style.background='#a47a5a'" onmouseout="this.style.background='#B9916D'"
                >
                    <span wire:loading.remove wire:target="nextStep" class="inline-flex items-center gap-1.5">
                        {{ __('hall-owner.registration.buttons.next') }}
                        <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                    <span wire:loading wire:target="nextStep" class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ __('hall-owner.registration.buttons.processing') }}
                    </span>
                </button>
            @else
                <button
                    type="button"
                    wire:click="submit"
                    wire:loading.attr="disabled"
                    wire:target="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white rounded-lg transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                    style="background:#B9916D; border:none;"
                    onmouseover="this.style.background='#a47a5a'" onmouseout="this.style.background='#B9916D'"
                >
                    <span wire:loading.remove wire:target="submit" class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ __('hall-owner.registration.buttons.submit') }}
                    </span>
                    <span wire:loading wire:target="submit" class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ __('hall-owner.registration.buttons.submitting') }}
                    </span>
                </button>
            @endif
        </div>
    </div>

</div>
