@extends('customer.layout')

@section('title', __('dashboard.page_title'))

@section('content')
<div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

    {{-- ─── Header ─────────────────────────────────────────── --}}
    <div class="relative mb-8 overflow-hidden bg-[#B9916D] rounded-2xl">
        <div class="absolute inset-0 opacity-10"
             style="background-image: url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\");">
        </div>
        <div class="relative px-6 py-8 sm:px-8">
            <h1 class="mb-1 text-2xl font-bold text-white sm:text-3xl">
                {{ __('dashboard.welcome_back', ['name' => Auth::user()->name]) }}
            </h1>
            <p class="text-[#e4c9b5]">{{ __('dashboard.manage_subtitle') }}</p>
        </div>
    </div>

    {{-- ─── Stats Cards ─────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-4 mb-8 lg:grid-cols-4">

        {{-- Total Bookings --}}
        <div class="p-5 bg-white border-s-4 border-[#B9916D] rounded-xl shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="mb-1 text-xs font-medium tracking-wide text-gray-500 uppercase">
                        {{ __('dashboard.total_bookings') }}
                    </p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_bookings'] }}</p>
                </div>
                <div class="flex items-center justify-center shrink-0 w-12 h-12 bg-[#E8D5C4] rounded-xl">
                    <svg class="w-6 h-6 text-[#B9916D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Upcoming --}}
        <div class="p-5 bg-white border-s-4 border-blue-500 rounded-xl shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="mb-1 text-xs font-medium tracking-wide text-gray-500 uppercase">
                        {{ __('dashboard.upcoming') }}
                    </p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['upcoming'] }}</p>
                </div>
                <div class="flex items-center justify-center shrink-0 w-12 h-12 bg-blue-100 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Completed --}}
        <div class="p-5 bg-white border-s-4 border-emerald-500 rounded-xl shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="mb-1 text-xs font-medium tracking-wide text-gray-500 uppercase">
                        {{ __('dashboard.completed') }}
                    </p>
                    <p class="text-3xl font-bold text-emerald-600">{{ $stats['completed'] }}</p>
                </div>
                <div class="flex items-center justify-center shrink-0 w-12 h-12 bg-emerald-100 rounded-xl">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Cancelled --}}
        <div class="p-5 bg-white border-s-4 border-red-400 rounded-xl shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="mb-1 text-xs font-medium tracking-wide text-gray-500 uppercase">
                        {{ __('dashboard.cancelled') }}
                    </p>
                    <p class="text-3xl font-bold text-red-500">{{ $stats['cancelled'] }}</p>
                </div>
                <div class="flex items-center justify-center shrink-0 w-12 h-12 bg-red-100 rounded-xl">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ─── Main Grid ───────────────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ── Upcoming Bookings ─────────────────────────────── --}}
        <div class="lg:col-span-2">
            <div class="overflow-hidden bg-white shadow-sm rounded-xl">

                {{-- Card header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-900">
                        {{ __('dashboard.upcoming_bookings') }}
                    </h2>
                    <a href="{{ route('customer.bookings') }}"
                       class="flex items-center gap-1 text-sm font-medium text-[#B9916D] hover:text-[#8a6a4f] transition-colors">
                        {{ __('dashboard.view_all') }}
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <div class="divide-y divide-gray-50">
                    @forelse($upcomingBookings as $booking)
                        <div class="px-6 py-4 transition-colors hover:bg-gray-50/60">
                            <div class="flex items-start justify-between gap-4">

                                {{-- Booking info --}}
                                <div class="flex-1 min-w-0">
                                    <h3 class="mb-2 font-semibold text-gray-900 truncate">
                                        {{ $booking->hall->name ?? __('dashboard.unnamed_hall') }}
                                    </h3>
                                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-500">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $booking->booking_date->format(__('dashboard.date_format')) }}
                                        </span>
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                            {{ $booking->hall->city->name ?? __('dashboard.unknown_city') }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Status + link --}}
                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $booking->status === 'confirmed'
                                            ? 'bg-emerald-100 text-emerald-800'
                                            : 'bg-amber-100 text-amber-800' }}">
                                        {{ __('dashboard.status_' . $booking->status) }}
                                    </span>
                                    <a href="{{ route('customer.booking.details', $booking) }}"
                                       class="text-xs font-medium text-[#B9916D] hover:text-[#8a6a4f] transition-colors">
                                        {{ __('dashboard.view_details') }}
                                    </a>
                                </div>

                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                            <div class="flex items-center justify-center w-16 h-16 mb-4 bg-[#f5ede6] rounded-full">
                                <svg class="w-8 h-8 text-[#d4b49f]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="mb-4 text-gray-500">{{ __('dashboard.no_upcoming_bookings') }}</p>
                            <a href="{{ route('customer.halls.index') }}"
                               class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-[#B9916D] rounded-lg hover:bg-[#a07d5e] transition-colors">
                                {{ __('dashboard.browse_halls') }}
                            </a>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>

        {{-- ── Sidebar ───────────────────────────────────────── --}}
        <div class="flex flex-col gap-6 lg:col-span-1">

            {{-- Quick Actions --}}
            <div class="p-6 bg-white shadow-sm rounded-xl">
                <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-500 uppercase">
                    {{ __('dashboard.quick_actions') }}
                </h3>
                <div class="flex flex-col gap-2">

                    <a href="{{ route('customer.halls.index') }}"
                       class="flex items-center gap-3 p-3 transition-colors rounded-xl bg-[#f5ede6] hover:bg-[#E8D5C4] group">
                        <div class="flex items-center justify-center shrink-0 w-9 h-9 bg-[#B9916D] rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-800 group-hover:text-[#a07d5e] transition-colors">
                            {{ __('dashboard.browse_halls') }}
                        </span>
                    </a>

                    <a href="{{ route('customer.bookings') }}"
                       class="flex items-center gap-3 p-3 transition-colors rounded-xl bg-gray-50 hover:bg-gray-100 group">
                        <div class="flex items-center justify-center shrink-0 w-9 h-9 bg-gray-700 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-800 group-hover:text-gray-900 transition-colors">
                            {{ __('dashboard.my_bookings') }}
                        </span>
                    </a>

                    <a href="{{ route('customer.profile') }}"
                       class="flex items-center gap-3 p-3 transition-colors rounded-xl bg-gray-50 hover:bg-gray-100 group">
                        <div class="flex items-center justify-center shrink-0 w-9 h-9 bg-gray-700 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-800 group-hover:text-gray-900 transition-colors">
                            {{ __('dashboard.my_profile') }}
                        </span>
                    </a>

                </div>
            </div>

            {{-- Account Info --}}
            <div class="p-6 bg-white shadow-sm rounded-xl">
                <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-500 uppercase">
                    {{ __('dashboard.account_info') }}
                </h3>
                <div class="flex flex-col gap-4">

                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-gray-500">{{ __('dashboard.member_since') }}</span>
                        <span class="text-sm font-medium text-gray-900 text-end">
                            {{ Auth::user()->created_at->format(__('dashboard.member_since_format')) }}
                        </span>
                    </div>

                    <div class="h-px bg-gray-100"></div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-gray-500 shrink-0">{{ __('dashboard.email') }}</span>
                        <span class="text-sm font-medium text-gray-900 truncate text-end">
                            {{ Auth::user()->email }}
                        </span>
                    </div>

                    <div class="h-px bg-gray-100"></div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-gray-500">{{ __('dashboard.phone') }}</span>
                        <span class="text-sm font-medium text-end
                            {{ Auth::user()->phone ? 'text-gray-900' : 'text-gray-400 italic' }}">
                            {{ Auth::user()->phone ?? __('dashboard.not_set') }}
                        </span>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
