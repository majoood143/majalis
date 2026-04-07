@extends('customer.layout')

@section('title', __('tickets.page_title') . ' - Majalis')

@section('content')
<div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="flex flex-col mb-8 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="mb-2 text-3xl font-bold text-gray-900">{{ __('tickets.heading') }}</h1>
            <p class="text-gray-600">{{ __('tickets.subtitle') }}</p>
        </div>
        <a href="{{ route('customer.tickets.create') }}"
            class="inline-flex items-center gap-2 px-6 py-3 mt-4 font-medium text-white transition bg-indigo-600 rounded-lg md:mt-0 hover:bg-indigo-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('tickets.submit_new') }}
        </a>
    </div>

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

    {{-- Filters --}}
    <div class="p-6 mb-6 bg-white rounded-lg shadow-md">
        <form action="{{ route('customer.tickets.index') }}" method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">{{ __('tickets.filter_status') }}</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">{{ __('tickets.all_statuses') }}</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">{{ __('tickets.filter_type') }}</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">{{ __('tickets.all_types') }}</option>
                    @foreach($types as $value => $label)
                        <option value="{{ $value }}" {{ $type === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-6 py-2 text-white transition bg-gray-900 rounded-lg hover:bg-gray-800">
                    {{ __('tickets.filter_btn') }}
                </button>
                @if($status || $type)
                    <a href="{{ route('customer.tickets.index') }}" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                        {{ __('tickets.clear_btn') }}
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tickets List --}}
    @if($tickets->count() > 0)
        <div class="space-y-4">
            @foreach($tickets as $ticket)
                <a href="{{ route('customer.tickets.show', $ticket) }}"
                   class="flex flex-col p-5 transition bg-white rounded-lg shadow-md hover:shadow-lg md:flex-row md:items-center md:justify-between">

                    {{-- Left: ticket info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
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
                            {{-- Type Badge --}}
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                {{ $ticket->type->getLabel() }}
                            </span>
                        </div>
                        <p class="mb-1 font-semibold text-gray-900 truncate">{{ $ticket->subject }}</p>
                        <p class="text-sm text-gray-500">
                            {{ __('tickets.submitted_on') }} {{ $ticket->created_at->diffForHumans() }}
                            @if($ticket->booking)
                                &mdash; {{ __('tickets.booking_ref') }} #{{ $ticket->booking->id }}
                            @endif
                        </p>
                    </div>

                    {{-- Right: last reply / chevron --}}
                    <div class="flex items-center gap-3 mt-3 md:mt-0 md:ms-6">
                        @if($ticket->messages->isNotEmpty())
                            <span class="text-xs text-gray-400">
                                {{ __('tickets.last_reply') }} {{ $ticket->messages->first()?->created_at->diffForHumans() }}
                            </span>
                        @endif
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $tickets->withQueryString()->links() }}
        </div>

    @else
        <div class="py-16 text-center bg-white rounded-lg shadow-md">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h3 class="mb-2 text-lg font-semibold text-gray-700">{{ __('tickets.no_requests') }}</h3>
            <p class="mb-6 text-gray-500">{{ __('tickets.no_requests_desc') }}</p>
            <a href="{{ route('customer.tickets.create') }}"
               class="inline-flex items-center gap-2 px-6 py-3 font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                {{ __('tickets.submit_first') }}
            </a>
        </div>
    @endif

</div>
@endsection
