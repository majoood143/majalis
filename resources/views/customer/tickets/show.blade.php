@extends('customer.layout')

@section('title', $ticket->ticket_number . ' - Majalis')

@section('content')
<div class="px-4 py-8 mx-auto max-w-3xl sm:px-6 lg:px-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 mb-6 text-sm text-gray-500">
        <a href="{{ route('customer.tickets.index') }}" class="hover:text-indigo-600">{{ __('tickets.nav_link') }}</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700">{{ $ticket->ticket_number }}</span>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="p-4 mb-6 text-green-800 bg-green-100 border border-green-200 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 mb-6 text-red-800 bg-red-100 border border-red-200 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Ticket Header Card --}}
    <div class="p-6 mb-6 bg-white rounded-lg shadow-md">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="text-xs font-semibold tracking-wide text-indigo-600 uppercase">
                        {{ $ticket->ticket_number }}
                    </span>
                    {{-- Status Badge --}}
                    @php
                        $statusColors = [
                            'open'        => 'bg-blue-100 text-blue-700',
                            'pending'     => 'bg-yellow-100 text-yellow-700',
                            'in_progress' => 'bg-indigo-100 text-indigo-700',
                            'on_hold'     => 'bg-gray-100 text-gray-600',
                            'resolved'    => 'bg-green-100 text-green-700',
                            'closed'      => 'bg-gray-100 text-gray-500',
                            'cancelled'   => 'bg-red-100 text-red-600',
                            'escalated'   => 'bg-red-100 text-red-700',
                        ];
                        $sc = $statusColors[$ticket->status->value] ?? 'bg-gray-100 text-gray-600';
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sc }}">
                        {{ $ticket->status->getLabel() }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                        {{ $ticket->type->getLabel() }}
                    </span>
                </div>
                <h1 class="text-xl font-bold text-gray-900 mb-1">{{ $ticket->subject }}</h1>
                <p class="text-sm text-gray-500">
                    {{ __('tickets.submitted_on') }} {{ $ticket->created_at->format('M d, Y \a\t H:i') }}
                    @if($ticket->booking)
                        &mdash; {{ __('tickets.booking_ref') }}
                        <span class="font-medium">#{{ $ticket->booking->id }}</span>
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
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            {{ __('tickets.btn_close') }}
                        </button>
                    </form>
                @endif

                @if($ticket->status->value === 'closed')
                    <form action="{{ route('customer.tickets.reopen', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('{{ __('tickets.confirm_reopen') }}')"
                                class="px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-100 rounded-lg hover:bg-indigo-200 transition">
                            {{ __('tickets.btn_reopen') }}
                        </button>
                    </form>
                @endif

                @if($ticket->status->value === 'open')
                    <form action="{{ route('customer.tickets.close', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('{{ __('tickets.confirm_cancel') }}')"
                                class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition">
                            {{ __('tickets.btn_cancel_req') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Resolution (when resolved/closed) --}}
        @if($ticket->resolution)
            <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-xs font-semibold text-green-700 uppercase mb-1">{{ __('tickets.label_resolution') }}</p>
                <p class="text-sm text-green-800">{{ $ticket->resolution }}</p>
            </div>
        @endif
    </div>

    {{-- Message Thread --}}
    <div class="mb-6 space-y-4">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ __('tickets.conversation') }}</h2>

        @foreach($ticket->messages as $message)
            @php
                $isCustomer = $message->user_id === auth()->id();
            @endphp
            <div class="flex {{ $isCustomer ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[85%] {{ $isCustomer ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-200 text-gray-800' }} rounded-2xl px-5 py-4 shadow-sm">
                    {{-- Sender name + time --}}
                    <div class="flex items-center justify-between gap-4 mb-2">
                        <span class="text-xs font-semibold {{ $isCustomer ? 'text-indigo-200' : 'text-gray-500' }}">
                            {{ $isCustomer ? __('tickets.you') : ($message->user->name ?? __('tickets.support_team')) }}
                        </span>
                        <span class="text-xs {{ $isCustomer ? 'text-indigo-200' : 'text-gray-400' }}">
                            {{ $message->created_at->format('M d, H:i') }}
                        </span>
                    </div>

                    {{-- Message body --}}
                    <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $message->message }}</p>

                    {{-- Attachments --}}
                    @if(!empty($message->attachments))
                        <div class="mt-3 space-y-1 border-t {{ $isCustomer ? 'border-indigo-500' : 'border-gray-100' }} pt-2">
                            @foreach($message->attachments as $index => $attachment)
                                <a href="{{ route('customer.tickets.download-attachment', [$ticket, $message->id, $index]) }}"
                                   class="flex items-center gap-2 text-xs {{ $isCustomer ? 'text-indigo-100 hover:text-white' : 'text-indigo-600 hover:text-indigo-800' }} transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <p class="py-8 text-center text-gray-400">{{ __('tickets.no_messages') }}</p>
        @endif
    </div>

    {{-- Reply Form --}}
    @if($ticket->status->value !== 'closed' && $ticket->status->value !== 'cancelled')
        <div class="p-6 bg-white rounded-lg shadow-md">
            <h2 class="mb-4 text-sm font-semibold text-gray-700 uppercase tracking-wide">{{ __('tickets.reply_heading') }}</h2>
            <form action="{{ route('customer.tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <textarea name="message" rows="4"
                              placeholder="{{ __('tickets.reply_placeholder') }}"
                              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500
                                     {{ $errors->has('message') ? 'border-red-400' : 'border-gray-300' }}">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Attachment upload --}}
                <div class="mb-4">
                    <label for="reply-attachments" class="block mb-1.5 text-sm font-medium text-gray-600">
                        {{ __('tickets.section_attachments') }}
                        <span class="text-gray-400 font-normal">({{ __('tickets.attachments_optional') }})</span>
                    </label>
                    <input type="file" id="reply-attachments" name="attachments[]" multiple
                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv"
                           class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                  file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        {{ __('tickets.btn_send') }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Rating Form (closed ticket, not yet rated) --}}
    @if($ticket->status->value === 'closed' && !$ticket->rating)
        <div class="p-6 bg-white rounded-lg shadow-md">
            <h2 class="mb-2 text-base font-semibold text-gray-800">{{ __('tickets.rate_heading') }}</h2>
            <p class="mb-4 text-sm text-gray-500">{{ __('tickets.rate_subtitle') }}</p>
            <form action="{{ route('customer.tickets.rate', $ticket) }}" method="POST">
                @csrf
                <div class="flex gap-3 mb-4">
                    @foreach([1 => '😞', 2 => '😕', 3 => '😐', 4 => '😊', 5 => '😄'] as $star => $emoji)
                        <label class="cursor-pointer text-center">
                            <input type="radio" name="rating" value="{{ $star }}" class="sr-only peer"
                                   {{ old('rating') == $star ? 'checked' : '' }}>
                            <div class="text-2xl w-12 h-12 flex items-center justify-center rounded-xl border-2 border-gray-200
                                        peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-indigo-300 transition">
                                {{ $emoji }}
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $star }}</p>
                        </label>
                    @endforeach
                </div>
                <div class="mb-4">
                    <textarea name="feedback" rows="3"
                              placeholder="{{ __('tickets.feedback_placeholder') }}"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">{{ old('feedback') }}</textarea>
                </div>
                <button type="submit"
                        class="px-6 py-2.5 font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition">
                    {{ __('tickets.btn_rate') }}
                </button>
            </form>
        </div>
    @elseif($ticket->rating)
        <div class="p-5 mt-4 bg-green-50 border border-green-200 rounded-lg text-center">
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
