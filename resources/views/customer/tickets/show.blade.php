@extends('customer.layout')

@section('title', $ticket->ticket_number . ' - Majalis')

@section('content')
<div class="px-4 py-8 mx-auto max-w-3xl sm:px-6 lg:px-8">

    {{-- ─── Breadcrumb ──────────────────────────────────────── --}}
    <nav class="flex items-center gap-2 mb-6 text-sm text-gray-500">
        <a href="{{ route('customer.tickets.index') }}"
           class="hover:text-[#B9916D] transition-colors">{{ __('tickets.nav_link') }}</a>
        <svg class="w-3.5 h-3.5 shrink-0 rtl:rotate-180 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="font-medium text-gray-700 truncate">{{ $ticket->ticket_number }}</span>
    </nav>

    {{-- ─── Flash Messages ──────────────────────────────────── --}}
    @if(session('success'))
        <div class="p-4 mb-6 text-green-800 bg-green-50 border border-green-200 rounded-xl">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 mb-6 text-red-800 bg-red-50 border border-red-200 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    {{-- ─── Header Banner ───────────────────────────────────── --}}
    <div class="relative mb-6 overflow-hidden bg-[#B9916D] rounded-2xl">
        <div class="absolute inset-0 opacity-10"
             style="background-image: url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\");">
        </div>
        <div class="relative flex flex-col gap-3 px-6 py-7 sm:flex-row sm:items-start sm:justify-between sm:px-8">
            <div class="flex-1 min-w-0">
                {{-- Badges --}}
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="text-xs font-semibold tracking-wide text-[#e4c9b5] uppercase">
                        {{ $ticket->ticket_number }}
                    </span>
                    @php
                        $statusColors = [
                            'open'        => 'bg-blue-100 text-blue-700',
                            'pending'     => 'bg-amber-100 text-amber-700',
                            'in_progress' => 'bg-[#E8D5C4] text-[#B9916D]',
                            'on_hold'     => 'bg-gray-100 text-gray-600',
                            'resolved'    => 'bg-green-100 text-green-700',
                            'closed'      => 'bg-gray-100 text-gray-500',
                            'cancelled'   => 'bg-red-100 text-red-600',
                            'escalated'   => 'bg-red-100 text-red-700',
                        ];
                        $sc = $statusColors[$ticket->status->value] ?? 'bg-gray-100 text-gray-600';
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sc }}">
                        {{ $ticket->status->getLabel() }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                        {{ $ticket->type->getLabel() }}
                    </span>
                </div>
                <h1 class="text-xl font-bold text-white mb-1">{{ $ticket->subject }}</h1>
                <p class="text-sm text-[#e4c9b5]">
                    {{ __('tickets.submitted_on') }} {{ $ticket->created_at->format('M d, Y \a\t H:i') }}
                    @if($ticket->booking)
                        &mdash; {{ __('tickets.booking_ref') }}
                        <span class="font-medium text-white">#{{ $ticket->booking->id }}</span>
                        @if($ticket->booking->hall)
                            ({{ $ticket->booking->hall->name }})
                        @endif
                    @endif
                </p>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap gap-2 shrink-0">
                @if($ticket->status->value === 'resolved')
                    <form action="{{ route('customer.tickets.close', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('{{ __('tickets.confirm_close') }}')"
                                class="px-4 py-2 text-sm font-semibold text-[#B9916D] bg-white rounded-xl hover:bg-[#f5ede6] transition-colors">
                            {{ __('tickets.btn_close') }}
                        </button>
                    </form>
                @endif

                @if($ticket->status->value === 'closed')
                    <form action="{{ route('customer.tickets.reopen', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('{{ __('tickets.confirm_reopen') }}')"
                                class="px-4 py-2 text-sm font-semibold text-[#B9916D] bg-white rounded-xl hover:bg-[#f5ede6] transition-colors">
                            {{ __('tickets.btn_reopen') }}
                        </button>
                    </form>
                @endif

                @if($ticket->status->value === 'open')
                    <form action="{{ route('customer.tickets.close', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('{{ __('tickets.confirm_cancel') }}')"
                                class="px-4 py-2 text-sm font-semibold text-red-600 bg-white rounded-xl hover:bg-red-50 transition-colors">
                            {{ __('tickets.btn_cancel_req') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Resolution notice --}}
    @if($ticket->resolution)
        <div class="p-4 mb-6 bg-green-50 border border-green-200 rounded-xl">
            <p class="text-xs font-semibold text-green-700 uppercase mb-1">{{ __('tickets.label_resolution') }}</p>
            <p class="text-sm text-green-800">{{ $ticket->resolution }}</p>
        </div>
    @endif

    {{-- ─── Message Thread ──────────────────────────────────── --}}
    <div class="mb-6">
        <h2 class="mb-4 text-sm font-semibold tracking-wide text-gray-500 uppercase">
            {{ __('tickets.conversation') }}
        </h2>

        <div class="flex flex-col gap-4">
            @foreach($ticket->messages as $message)
                @php $isCustomer = $message->user_id === auth()->id(); @endphp

                <div class="flex {{ $isCustomer ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[85%] rounded-2xl px-5 py-4 shadow-sm
                                {{ $isCustomer
                                    ? 'bg-[#B9916D] text-white'
                                    : 'bg-white border border-gray-200 text-gray-800' }}">

                        {{-- Sender + time --}}
                        <div class="flex items-center justify-between gap-4 mb-2">
                            <span class="text-xs font-semibold
                                         {{ $isCustomer ? 'text-[#f5ede6]' : 'text-gray-500' }}">
                                {{ $isCustomer
                                    ? __('tickets.you')
                                    : ($message->user->name ?? __('tickets.support_team')) }}
                            </span>
                            <span class="text-xs {{ $isCustomer ? 'text-[#e4c9b5]' : 'text-gray-400' }}">
                                {{ $message->created_at->format('M d, H:i') }}
                            </span>
                        </div>

                        {{-- Body --}}
                        <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $message->message }}</p>

                        {{-- Attachments --}}
                        @if(!empty($message->attachments))
                            <div class="mt-3 pt-2 border-t
                                        {{ $isCustomer ? 'border-[#a07d5e]' : 'border-gray-100' }}
                                        flex flex-col gap-1">
                                @foreach($message->attachments as $index => $attachment)
                                    <a href="{{ route('customer.tickets.download-attachment', [$ticket, $message->id, $index]) }}"
                                       class="flex items-center gap-2 text-xs transition-colors
                                              {{ $isCustomer
                                                  ? 'text-[#f5ede6] hover:text-white'
                                                  : 'text-[#B9916D] hover:text-[#8a6a4f]' }}">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                        {{ $attachment['original_name'] ?? basename($attachment['path']) }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            @if($ticket->messages->isEmpty())
                <div class="py-12 text-center bg-white rounded-xl border border-gray-100">
                    <p class="text-gray-400 text-sm">{{ __('tickets.no_messages') }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ─── Reply Form ──────────────────────────────────────── --}}
    @if($ticket->status->value !== 'closed' && $ticket->status->value !== 'cancelled')
        <div class="p-6 bg-white shadow-sm rounded-2xl">
            <h2 class="mb-4 text-sm font-semibold tracking-wide text-gray-500 uppercase">
                {{ __('tickets.reply_heading') }}
            </h2>
            <form action="{{ route('customer.tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <textarea name="message" rows="4"
                              placeholder="{{ __('tickets.reply_placeholder') }}"
                              class="w-full px-4 py-3 text-sm bg-gray-50 border rounded-xl resize-none
                                     focus:outline-none focus:ring-2 focus:ring-[#B9916D] focus:border-transparent transition
                                     {{ $errors->has('message') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label for="reply-attachments" class="block mb-1.5 text-sm font-medium text-gray-600">
                        {{ __('tickets.section_attachments') }}
                        <span class="text-gray-400 font-normal">({{ __('tickets.attachments_optional') }})</span>
                    </label>
                    <input type="file" id="reply-attachments" name="attachments[]" multiple
                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv"
                           class="block w-full text-sm text-gray-600
                                  file:me-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-[#f5ede6] file:text-[#B9916D]
                                  hover:file:bg-[#E8D5C4] transition">
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-[#B9916D] rounded-xl hover:bg-[#a07d5e] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        {{ __('tickets.btn_send') }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- ─── Rating Form ─────────────────────────────────────── --}}
    @if($ticket->status->value === 'closed' && !$ticket->rating)
        <div class="p-6 mt-6 bg-white shadow-sm rounded-2xl">
            <h2 class="mb-1 text-base font-semibold text-gray-900">{{ __('tickets.rate_heading') }}</h2>
            <p class="mb-5 text-sm text-gray-500">{{ __('tickets.rate_subtitle') }}</p>
            <form action="{{ route('customer.tickets.rate', $ticket) }}" method="POST">
                @csrf
                <div class="flex gap-3 mb-5">
                    @foreach([1 => '😞', 2 => '😕', 3 => '😐', 4 => '😊', 5 => '😄'] as $star => $emoji)
                        <label class="cursor-pointer text-center">
                            <input type="radio" name="rating" value="{{ $star }}"
                                   class="sr-only peer"
                                   {{ old('rating') == $star ? 'checked' : '' }}>
                            <div class="text-2xl w-12 h-12 flex items-center justify-center rounded-xl border-2
                                        border-gray-200 transition
                                        peer-checked:border-[#B9916D] peer-checked:bg-[#f5ede6]
                                        hover:border-[#d4b49f]">
                                {{ $emoji }}
                            </div>
                            <p class="text-xs text-gray-400 mt-1">{{ $star }}</p>
                        </label>
                    @endforeach
                </div>
                <div class="mb-4">
                    <textarea name="feedback" rows="3"
                              placeholder="{{ __('tickets.feedback_placeholder') }}"
                              class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl resize-none
                                     focus:outline-none focus:ring-2 focus:ring-[#B9916D] focus:border-transparent transition">{{ old('feedback') }}</textarea>
                </div>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-green-600 rounded-xl hover:bg-green-700 transition-colors">
                    {{ __('tickets.btn_rate') }}
                </button>
            </form>
        </div>

    @elseif($ticket->rating)
        <div class="p-5 mt-6 bg-green-50 border border-green-200 rounded-xl text-center">
            <p class="text-green-700 font-medium">
                {{ __('tickets.rated_label') }}
                @for($i = 1; $i <= 5; $i++)
                    <span class="{{ $i <= $ticket->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                @endfor
            </p>
            @if($ticket->feedback)
                <p class="mt-1 text-sm text-green-600 italic">{{ $ticket->feedback }}</p>
            @endif
        </div>
    @endif

</div>
@endsection
