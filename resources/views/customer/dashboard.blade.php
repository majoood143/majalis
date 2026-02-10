@extends('customer.layout')

@section('title', 'Dashboard - majalis')

@section('content')
<div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="mb-2 text-3xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}!</h1>
        <p class="text-gray-600">Manage your bookings and profile</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
        <div class="p-6 bg-white rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-1 text-sm text-gray-600">Total Bookings</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_bookings'] }}</p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-indigo-100 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-1 text-sm text-gray-600">Upcoming</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['upcoming'] }}</p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-1 text-sm text-gray-600">Completed</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-1 text-sm text-gray-600">Cancelled</p>
                    <p class="text-3xl font-bold text-red-600">{{ $stats['cancelled'] }}</p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Upcoming Bookings -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold">Upcoming Bookings</h2>
                        <a href="{{ route('customer.bookings') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            View All →
                        </a>
                    </div>
                </div>

                <div class="divide-y divide-gray-200">
                    @forelse($upcomingBookings as $booking)
                        <div class="p-6 transition hover:bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="mb-1 font-semibold text-gray-900">{{ $booking->hall->name ?? 'Unnamed Hall' }}</h3>
                                    <div class="space-y-1 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $booking->booking_date->format('F d, Y') }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                            {{ $booking->hall->city->name ?? 'Unknown City' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4 text-right">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                        {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                    <div class="mt-2">
                                        <a href="{{ route('customer.booking.details', $booking) }}"
                                            class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="mb-4 text-gray-600">No upcoming bookings</p>
                            <a href="{{ route('customer.halls.index') }}"
                                class="inline-block px-6 py-2 text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700">
                                Browse Halls
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="lg:col-span-1">
            <div class="p-6 mb-6 bg-white rounded-lg shadow-md">
                <h3 class="mb-4 text-lg font-semibold">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('customer.halls.index') }}"
                        class="flex items-center p-3 transition rounded-lg bg-indigo-50 hover:bg-indigo-100 group">
                        <div class="flex items-center justify-center w-10 h-10 mr-3 bg-indigo-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <span class="font-medium text-gray-900 group-hover:text-indigo-600">Browse Halls</span>
                    </a>

                    <a href="{{ route('customer.bookings') }}"
                        class="flex items-center p-3 transition rounded-lg bg-gray-50 hover:bg-gray-100 group">
                        <div class="flex items-center justify-center w-10 h-10 mr-3 bg-gray-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <span class="font-medium text-gray-900 group-hover:text-gray-600">My Bookings</span>
                    </a>

                    <a href="{{ route('customer.profile') }}"
                        class="flex items-center p-3 transition rounded-lg bg-gray-50 hover:bg-gray-100 group">
                        <div class="flex items-center justify-center w-10 h-10 mr-3 bg-gray-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span class="font-medium text-gray-900 group-hover:text-gray-600">My Profile</span>
                    </a>
                </div>
            </div>

            <!-- Account Info -->
            <div class="p-6 bg-white rounded-lg shadow-md">
                <h3 class="mb-4 text-lg font-semibold">Account Information</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">Member since</span>
                        <div class="font-medium">{{ Auth::user()->created_at->format('F Y') }}</div>
                    </div>
                    <div>
                        <span class="text-gray-600">Email</span>
                        <div class="font-medium">{{ Auth::user()->email }}</div>
                    </div>
                    <div>
                        <span class="text-gray-600">Phone</span>
                        <div class="font-medium">{{ Auth::user()->phone ?? 'Not set' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
