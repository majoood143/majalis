@extends('customer.layout')

@section('title', __('tickets.create_title') . ' - Majalis')

@section('content')
<div class="px-4 py-8 mx-auto max-w-3xl sm:px-6 lg:px-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 mb-6 text-sm text-gray-500">
        <a href="{{ route('customer.tickets.index') }}" class="hover:text-indigo-600">{{ __('tickets.nav_link') }}</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700">{{ __('tickets.breadcrumb_new') }}</span>
    </nav>

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="mb-2 text-3xl font-bold text-gray-900">{{ __('tickets.create_title') }}</h1>
        <p class="text-gray-600">{{ __('tickets.create_subtitle') }}</p>
    </div>

    {{-- Error Summary --}}
    @if($errors->any())
        <div class="p-4 mb-6 text-red-800 bg-red-100 border border-red-200 rounded-lg">
            <p class="mb-1 font-semibold">{{ __('tickets.errors_heading') }}</p>
            <ul class="text-sm list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('customer.tickets.store') }}" method="POST" enctype="multipart/form-data"
          class="space-y-6 bg-white rounded-lg shadow-md">

        @csrf

        {{-- Request Type --}}
        <div class="p-6 border-b border-gray-100">
            <h2 class="mb-4 text-base font-semibold text-gray-800">{{ __('tickets.section_type') }}</h2>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach($ticketTypes as $ticketType)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="type" value="{{ $ticketType->value }}"
                               class="sr-only peer"
                               {{ old('type') === $ticketType->value ? 'checked' : '' }}
                               {{ $ticketType->value === 'claim' && !old('type') ? 'checked' : '' }}>
                        <div class="flex flex-col items-center gap-1 p-3 text-center transition border-2 rounded-lg border-gray-200
                                    peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-indigo-300">
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
                            <svg class="w-6 h-6 text-gray-500 peer-checked:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"/>
                            </svg>
                            <span class="text-xs font-medium text-gray-700">{{ $ticketType->getLabel() }}</span>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('type')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Subject & Description --}}
        <div class="p-6 border-b border-gray-100 space-y-4">
            <h2 class="mb-4 text-base font-semibold text-gray-800">{{ __('tickets.section_details') }}</h2>

            <div>
                <label for="subject" class="block mb-1.5 text-sm font-medium text-gray-700">
                    {{ __('tickets.label_subject') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                       maxlength="200"
                       placeholder="{{ __('tickets.subject_placeholder') }}"
                       class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-indigo-500
                              {{ $errors->has('subject') ? 'border-red-400' : 'border-gray-300' }}">
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block mb-1.5 text-sm font-medium text-gray-700">
                    {{ __('tickets.label_description') }} <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="5"
                          placeholder="{{ __('tickets.description_placeholder') }}"
                          class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-indigo-500
                                 {{ $errors->has('description') ? 'border-red-400' : 'border-gray-300' }}">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Related Booking (optional) --}}
        @if($bookings->count() > 0)
        <div class="p-6 border-b border-gray-100">
            <h2 class="mb-1 text-base font-semibold text-gray-800">{{ __('tickets.section_booking') }}</h2>
            <p class="mb-4 text-sm text-gray-500">{{ __('tickets.section_booking_hint') }}</p>
            <select name="booking_id"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
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

        {{-- Attachments --}}
        <div class="p-6 border-b border-gray-100">
            <h2 class="mb-1 text-base font-semibold text-gray-800">{{ __('tickets.section_attachments') }}</h2>
            <p class="mb-4 text-sm text-gray-500">{{ __('tickets.attachments_hint') }}</p>
            <div class="flex items-center justify-center w-full">
                <label for="attachments"
                       class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer border-gray-300 bg-gray-50 hover:border-indigo-400 hover:bg-indigo-50 transition">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm text-gray-500">
                            <span class="font-semibold text-indigo-600">{{ __('tickets.upload_click') }}</span>
                            {{ __('tickets.upload_or') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">{{ __('tickets.upload_types') }}</p>
                    </div>
                    <input id="attachments" name="attachments[]" type="file" class="hidden" multiple
                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv">
                </label>
            </div>
            <div id="file-list" class="mt-3 space-y-1 text-sm text-gray-600"></div>
            @error('attachments.*')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-between p-6">
            <a href="{{ route('customer.tickets.index') }}"
               class="px-5 py-2.5 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                {{ __('tickets.btn_cancel') }}
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        item.className = 'flex items-center gap-2 text-sm text-gray-700';
        item.innerHTML = `<svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
        </svg> ${file.name} <span class="text-gray-400">(${(file.size / 1024).toFixed(0)} KB)</span>`;
        list.appendChild(item);
    });
});
</script>
@endsection
