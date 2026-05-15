@extends('customer.layout')

@section('title', __('tickets.create_title') . ' - Majalis')

@section('content')
<div class="px-4 py-8 mx-auto max-w-3xl sm:px-6 lg:px-8">

    {{-- ─── Breadcrumb ──────────────────────────────────────── --}}
    <nav class="flex items-center gap-2 mb-6 text-sm text-gray-500">
        <a href="{{ route('customer.tickets.index') }}"
           class="hover:text-[#B9916D] transition-colors">{{ __('tickets.nav_link') }}</a>
        <svg class="w-3.5 h-3.5 shrink-0 rtl:rotate-180 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="font-medium text-gray-700">{{ __('tickets.breadcrumb_new') }}</span>
    </nav>

    {{-- ─── Header Banner ───────────────────────────────────── --}}
    <div class="relative mb-8 overflow-hidden bg-[#B9916D] rounded-2xl">
        <div class="absolute inset-0 opacity-10"
             style="background-image: url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\");">
        </div>
        <div class="relative px-6 py-7 sm:px-8">
            <h1 class="mb-1 text-2xl font-bold text-white sm:text-3xl">{{ __('tickets.create_title') }}</h1>
            <p class="text-[#e4c9b5] text-sm">{{ __('tickets.create_subtitle') }}</p>
        </div>
    </div>

    {{-- ─── Error Summary ───────────────────────────────────── --}}
    @if($errors->any())
        <div class="p-4 mb-6 text-red-800 bg-red-50 border border-red-200 rounded-xl">
            <p class="mb-1 font-semibold">{{ __('tickets.errors_heading') }}</p>
            <ul class="text-sm list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('customer.tickets.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white shadow-sm rounded-2xl overflow-hidden">

        @csrf

        {{-- ─── Request Type ────────────────────────────────── --}}
        <div class="p-6 border-b border-gray-100">
            <h2 class="mb-1 text-sm font-semibold tracking-wide text-gray-500 uppercase">
                {{ __('tickets.section_type') }}
            </h2>
            <p class="mb-4 text-xs text-gray-400">{{ __('tickets.create_subtitle') }}</p>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach($ticketTypes as $ticketType)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="type" value="{{ $ticketType->value }}"
                               class="sr-only peer"
                               {{ old('type') === $ticketType->value ? 'checked' : '' }}
                               {{ $ticketType->value === 'claim' && !old('type') ? 'checked' : '' }}>
                        <div class="flex flex-col items-center gap-1.5 p-3 text-center transition-all border-2 rounded-xl
                                    border-gray-200 text-gray-400
                                    peer-checked:border-[#B9916D] peer-checked:bg-[#f5ede6] peer-checked:text-[#B9916D]
                                    hover:border-[#d4b49f] hover:bg-[#faf5f0]">
                            @php
                                $icons = [
                                    'claim'        => 'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z',
                                    'complaint'    => 'M12 8v4m0 4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z',
                                    'inquiry'      => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                    'refund'       => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
                                    'cancellation' => 'M6 18L18 6M6 6l12 12',
                                    'technical'    => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                                    'feedback'     => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                                    'other'        => 'M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z',
                                ];
                                $iconPath = $icons[$ticketType->value] ?? $icons['other'];
                            @endphp
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"/>
                            </svg>
                            <span class="text-xs font-medium">{{ $ticketType->getLabel() }}</span>
                        </div>
                    </label>
                @endforeach
            </div>

            @error('type')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- ─── Subject & Description ───────────────────────── --}}
        <div class="p-6 border-b border-gray-100 space-y-5">
            <h2 class="text-sm font-semibold tracking-wide text-gray-500 uppercase">
                {{ __('tickets.section_details') }}
            </h2>

            <div>
                <label for="subject" class="block mb-1.5 text-sm font-medium text-gray-700">
                    {{ __('tickets.label_subject') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                       maxlength="200"
                       placeholder="{{ __('tickets.subject_placeholder') }}"
                       class="w-full px-4 py-2.5 text-sm bg-gray-50 border rounded-xl
                              focus:outline-none focus:ring-2 focus:ring-[#B9916D] focus:border-transparent transition
                              {{ $errors->has('subject') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                @error('subject')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block mb-1.5 text-sm font-medium text-gray-700">
                    {{ __('tickets.label_description') }} <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="5"
                          placeholder="{{ __('tickets.description_placeholder') }}"
                          class="w-full px-4 py-2.5 text-sm bg-gray-50 border rounded-xl resize-none
                                 focus:outline-none focus:ring-2 focus:ring-[#B9916D] focus:border-transparent transition
                                 {{ $errors->has('description') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- ─── Related Booking ────────────────────────────── --}}
        @if($bookings->count() > 0)
        <div class="p-6 border-b border-gray-100">
            <h2 class="mb-0.5 text-sm font-semibold tracking-wide text-gray-500 uppercase">
                {{ __('tickets.section_booking') }}
            </h2>
            <p class="mb-4 text-xs text-gray-400">{{ __('tickets.section_booking_hint') }}</p>
            <select name="booking_id"
                    class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl
                           focus:outline-none focus:ring-2 focus:ring-[#B9916D] focus:border-transparent transition">
                <option value="">{{ __('tickets.no_booking') }}</option>
                @foreach($bookings as $booking)
                    <option value="{{ $booking->id }}"
                            {{ old('booking_id') == $booking->id ? 'selected' : '' }}>
                        #{{ $booking->id }}
                        @if($booking->hall) — {{ $booking->hall->name }} @endif
                        ({{ $booking->booking_date?->format('M d, Y') ?? __('tickets.na') }})
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- ─── Attachments ─────────────────────────────────── --}}
        <div class="p-6 border-b border-gray-100">
            <h2 class="mb-0.5 text-sm font-semibold tracking-wide text-gray-500 uppercase">
                {{ __('tickets.section_attachments') }}
            </h2>
            <p class="mb-4 text-xs text-gray-400">{{ __('tickets.attachments_hint') }}</p>

            <label for="attachments"
                   class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed rounded-xl
                          cursor-pointer border-gray-200 bg-gray-50
                          hover:border-[#B9916D] hover:bg-[#faf5f0] transition-colors">
                <svg class="w-8 h-8 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <p class="text-sm text-gray-500">
                    <span class="font-semibold text-[#B9916D]">{{ __('tickets.upload_click') }}</span>
                    {{ __('tickets.upload_or') }}
                </p>
                <p class="mt-1 text-xs text-gray-400">{{ __('tickets.upload_types') }}</p>
                <input id="attachments" name="attachments[]" type="file" class="hidden" multiple
                       accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv">
            </label>

            <div id="file-list" class="mt-3 flex flex-col gap-1.5"></div>

            @error('attachments.*')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- ─── Submit ───────────────────────────────────────── --}}
        <div class="flex items-center justify-between gap-3 p-6">
            <a href="{{ route('customer.tickets.index') }}"
               class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                {{ __('tickets.btn_cancel') }}
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-[#B9916D] rounded-xl hover:bg-[#a07d5e] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                {{ __('tickets.btn_submit') }}
            </button>
        </div>

    </form>
</div>

<script>
document.getElementById('attachments').addEventListener('change', function () {
    const list = document.getElementById('file-list');
    list.innerHTML = '';
    Array.from(this.files).forEach(file => {
        const item = document.createElement('div');
        item.className = 'flex items-center gap-2 text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg';
        item.innerHTML = `<svg class="w-4 h-4 text-[#B9916D] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
        </svg>
        <span class="truncate">${file.name}</span>
        <span class="text-gray-400 shrink-0">(${(file.size / 1024).toFixed(0)} KB)</span>`;
        list.appendChild(item);
    });
});
</script>
@endsection
