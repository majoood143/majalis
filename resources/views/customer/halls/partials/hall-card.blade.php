<a href="{{ route('customer.halls.show', $hall->slug) }}" class="block overflow-hidden bg-white border border-gray-200 shadow-sm hall-card rounded-2xl">
    <!-- Image -->
    <div class="relative h-48 overflow-hidden bg-gray-200 sm:h-56">
        @if($hall->featured_image)
            <img
                src="{{ asset('storage/' . $hall->featured_image) }}"
                alt="{{ is_array($hall->name) ? ($hall->name[app()->getLocale()] ?? $hall->name['en']) : $hall->name }}"
                class="object-cover w-full h-full"
                loading="lazy">
        @else
            <div class="flex items-center justify-center w-full h-full bg-gradient-to-br from-primary-100 to-primary-200">
                <svg class="w-16 h-16 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
        @endif

        <!-- Badges -->
        <div class="absolute top-3 {{ app()->getLocale() === 'ar' ? 'left-3' : 'right-3' }} flex flex-col gap-2">
            @if($hall->is_featured)
                <span class="px-2.5 py-1 bg-amber-400 text-amber-900 rounded-full text-xs font-bold shadow-sm">
                    {{ __('halls.featured') }}
                </span>
            @endif
            @if($hall->average_rating > 0)
                <span class="px-2.5 py-1 bg-white rounded-full text-xs font-bold shadow-sm flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    {{ number_format($hall->average_rating, 1) }}
                </span>
            @endif
        </div>
    </div>

    <!-- Content -->
    <div class="p-4">
        <h3 class="mb-1 font-bold text-gray-900 line-clamp-1">
            {{ is_array($hall->name) ? ($hall->name[app()->getLocale()] ?? $hall->name['en']) : $hall->name }}
        </h3>

        <div class="flex items-center mb-2 text-sm text-gray-600">
            <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="truncate">{{ is_array($hall->city->name) ? ($hall->city->name[app()->getLocale()] ?? $hall->city->name['en']) : $hall->city->name }}</span>
        </div>

        <div class="flex items-center mb-3 text-sm text-gray-600">
            <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            {{ $hall->capacity_min }} - {{ $hall->capacity_max }} {{ __('halls.guests_count') }}
        </div>

        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
            <div>
                <span class="text-2xl font-bold text-gray-900">{{ number_format($hall->price_per_slot, 3) }}</span>
                <span class="text-sm text-gray-600"> OMR</span>
            </div>
            <span class="text-sm font-medium text-primary-600">{{ __('halls.view_details') }} →</span>
        </div>
    </div>
</a>
