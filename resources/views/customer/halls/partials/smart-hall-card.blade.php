{{--
|--------------------------------------------------------------------------
| Smart Hall Card — Date-aware card with availability badges
|--------------------------------------------------------------------------
|
| LOCATION: resources/views/customer/halls/partials/smart-hall-card.blade.php
|
| Variables passed from parent:
|   $hall         — Hall model (with dynamic: is_available, available_slots, slot_prices)
|   $isDateSearch — bool: whether user searched by date
|   $timeSlots   — array: ['morning' => 'Morning', …]
|
| Replaces the old hall-card.blade.php with full smart search support.
|
--}}

@php
    // ── Extract smart-search dynamic attributes ──
    $isAvailable  = $hall->is_available ?? true;
    $availSlots   = $hall->available_slots ?? [];
    $slotPrices   = $hall->slot_prices ?? [];
    $selectedSlot = request('time_slot');
    $displayPrice = null;

    // Determine the best display price for this context
    if ($isDateSearch && $selectedSlot && isset($slotPrices[$selectedSlot])) {
        $displayPrice = (float) $slotPrices[$selectedSlot];
    } elseif ($isDateSearch && count($availSlots) > 0) {
        $availPrices  = array_intersect_key($slotPrices, array_flip($availSlots));
        $displayPrice = $availPrices ? min($availPrices) : null;
    }

    // Hall name (handle translatable JSON)
    $hallName = is_array($hall->name)
        ? ($hall->name[app()->getLocale()] ?? $hall->name['en'] ?? '')
        : $hall->name;

    // City name (handle translatable JSON)
    $cityName = '';
    if ($hall->city) {
        $cityName = is_array($hall->city->name)
            ? ($hall->city->name[app()->getLocale()] ?? $hall->city->name['en'] ?? '')
            : ($hall->city->name ?? '');
    }

    // Region name
    $regionName = '';
    if ($hall->city?->region) {
        $regionName = is_array($hall->city->region->name)
            ? ($hall->city->region->name[app()->getLocale()] ?? $hall->city->region->name['en'] ?? '')
            : ($hall->city->region->name ?? '');
    }
@endphp

<div class="hall-card group relative bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-200
    {{ $isAvailable ? '' : 'opacity-60' }}">

    {{-- ── FULLY BOOKED OVERLAY BADGE ── --}}
    @if ($isDateSearch && !$isAvailable)
        <div class="absolute top-3 {{ app()->getLocale() === 'ar' ? 'right-3' : 'left-3' }} z-20">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-500/90 text-white text-xs font-bold rounded-full shadow-lg backdrop-blur-sm">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                {{ __('halls.fully_booked') }}
            </span>
        </div>
    @endif

    {{-- ── FEATURED BADGE ── --}}
    @if ($hall->is_featured && ($isAvailable || !$isDateSearch))
        <div class="absolute top-3 {{ app()->getLocale() === 'ar' ? 'right-3' : 'left-3' }} z-20">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-400 text-amber-900 text-xs font-bold rounded-full shadow-lg">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                {{ __('halls.featured') }}
            </span>
        </div>
    @endif

    {{-- ── HALL IMAGE ── --}}
    <a href="{{ route('customer.halls.show', $hall->slug) }}" class="relative block h-48 overflow-hidden bg-gray-200 sm:h-52">
        @if ($hall->featured_image)
            <img src="{{ asset('storage/' . $hall->featured_image) }}"
                 alt="{{ $hallName }}"
                 class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-110"
                 loading="lazy">
        @else
            <div class="flex items-center justify-center w-full h-full bg-gradient-to-br from-gray-100 to-gray-300">
                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        @endif

        {{-- Price overlay badge --}}
        <div class="absolute bottom-3 {{ app()->getLocale() === 'ar' ? 'left-3' : 'right-3' }}">
            @if ($displayPrice !== null)
                <span class="inline-flex items-baseline gap-0.5 px-3 py-1.5 bg-white/95 backdrop-blur-sm rounded-lg shadow-md font-bold text-brand-700">
                    <span class="text-[10px] font-medium text-gray-500"><img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                        class="inline w-5 h-5 -mt-1"></span>
                    <span class="text-lg">{{ number_format($displayPrice, 3) }}</span>
                </span>
            @else
                <span class="inline-flex items-baseline gap-0.5 px-3 py-1.5 bg-white/95 backdrop-blur-sm rounded-lg shadow-md font-bold text-gray-600">
                    <span class="text-[10px] font-medium text-gray-400">{{ __('halls.starting_from') }}</span>
                    <span class="text-[10px] font-medium text-gray-500 {{ app()->getLocale() === 'ar' ? 'mr-1' : 'ml-1' }}"><img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                        class="inline w-5 h-5 -mt-1"></span>
                    <span class="text-lg">{{ number_format((float) ($hall->price_per_slot ?? 0), 3) }}</span>
                </span>
            @endif
        </div>

        {{-- Wishlist / favorite heart (top-right) --}}
        <div class="absolute top-3 {{ app()->getLocale() === 'ar' ? 'left-3' : 'right-3' }} z-10">
            <button type="button" class="flex items-center justify-center w-8 h-8 transition rounded-full shadow-md opacity-0 bg-white/80 backdrop-blur-sm hover:bg-white group-hover:opacity-100 md:opacity-0 md:group-hover:opacity-100" style="opacity: 0.7;">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
        </div>
    </a>

    {{-- ── CARD BODY ── --}}
    <div class="p-4">
        {{-- Name + Rating row --}}
        <div class="flex items-start justify-between gap-2 mb-1.5">
            <a href="{{ route('customer.halls.show', $hall->slug) }}" class="flex-1 min-w-0 transition hover:text-brand-600">
                <h3 class="text-base font-bold leading-tight text-gray-800 line-clamp-1">{{ $hallName }}</h3>
            </a>
            @if ((float) ($hall->average_rating ?? 0) > 0)
                <div class="flex-shrink-0 flex items-center gap-0.5 px-1.5 py-0.5 bg-amber-50 rounded-md">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="text-xs font-semibold text-amber-700">{{ number_format((float) $hall->average_rating, 1) }}</span>
                </div>
            @endif
        </div>

        {{-- Location --}}
        <div class="flex items-center gap-1 mb-1 text-sm text-gray-500">
            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="line-clamp-1">{{ $cityName }}{{ $regionName ? ' · ' . $regionName : '' }}</span>
        </div>

        {{-- Capacity --}}
        <div class="flex items-center gap-1 mb-3 text-sm text-gray-500">
            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>{{ $hall->capacity_min ?? 0 }} – {{ $hall->capacity_max ?? 0 }} {{ __('halls.guests') }}</span>
        </div>

        {{-- ═════════════════════════════════════════
             AVAILABILITY SLOT BADGES (date search only)
             ═════════════════════════════════════════ --}}
        @if ($isDateSearch)
            <div class="pt-3 mb-3 border-t border-gray-100">
                @if ($isAvailable && count($availSlots) > 0)
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($availSlots as $slot)
                            @php
                                $slotPrice = $slotPrices[$slot] ?? (float) ($hall->price_per_slot ?? 0);
                                $slotLabel = match($slot) {
                                    'morning'   => __('halls.slot_morning'),
                                    'afternoon' => __('halls.slot_afternoon'),
                                    'evening'   => __('halls.slot_evening'),
                                    'full_day'  => __('halls.slot_full_day'),
                                    default     => ucfirst($slot),
                                };
                                // Highlight the selected slot
                                $isSelected = ($selectedSlot === $slot);
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-semibold rounded-lg border
                                {{ $isSelected
                                    ? 'bg-brand-500 text-white border-brand-500'
                                    : 'bg-brand-50 text-brand-700 border-brand-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $isSelected ? 'bg-white' : 'bg-brand-500' }}"></span>
                                {{ $slotLabel }}
                                <span class="{{ $isSelected ? 'text-brand-100' : 'text-brand-400' }}">·</span>
                                {{ number_format($slotPrice, 3) }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <div class="flex items-center gap-1.5 text-sm text-red-400">
                        <span class="w-2 h-2 bg-red-400 rounded-full"></span>
                        {{ __('halls.all_slots_booked') }}
                    </div>
                @endif
            </div>
        @endif

        {{-- ── CTA BUTTON ── --}}
        @if ($isAvailable)
            @if ($isDateSearch && $selectedSlot && in_array($selectedSlot, $availSlots))
                {{-- Direct booking CTA with pre-filled date + slot --}}
                <a href="{{ route('customer.halls.show', $hall->slug) }}?date={{ request('date') }}&time_slot={{ $selectedSlot }}"
                   class="block w-full text-center px-4 py-2.5 bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl transition-all shadow-sm hover:shadow-md active:scale-[0.98] text-sm">
                    {{ __('halls.book') }} {{ __($timeSlots[$selectedSlot] ?? ucfirst($selectedSlot)) }}
                    — {{ __('OMR') }} {{ number_format($slotPrices[$selectedSlot] ?? (float) ($hall->price_per_slot ?? 0), 3) }}
                </a>
            @else
                <a href="{{ route('customer.halls.show', $hall->slug) }}{{ $isDateSearch ? '?date=' . request('date') : '' }}"
                   class="block w-full text-center px-4 py-2.5 bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl transition-all shadow-sm hover:shadow-md active:scale-[0.98] text-sm">
                    {{ __('halls.view_details') }}
                </a>
            @endif
        @else
            {{-- Greyed out for fully booked --}}
            <a href="{{ route('customer.halls.show', $hall->slug) }}"
               class="block w-full text-center px-4 py-2.5 bg-gray-100 text-gray-400 font-semibold rounded-xl text-sm cursor-default">
                {{ __('halls.fully_booked') }}
            </a>
        @endif
    </div>
</div>
