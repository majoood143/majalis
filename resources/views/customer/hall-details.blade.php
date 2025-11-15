@extends('customer.layout')

@section('title', $hall->name . ' - Majalis')

@section('content')
<div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('customer.halls.index') }}" class="text-indigo-600 hover:text-indigo-800">Halls</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li><span class="text-gray-600">{{ $hall->name }}</span></li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Image Gallery -->
            <div class="mb-6 overflow-hidden bg-white rounded-lg shadow-md">
                <div class="relative h-96">
                    @if($hall->featured_image)
                        <img src="{{ Storage::url($hall->featured_image) }}" alt="{{ $hall->name }}"
                            class="object-cover w-full h-full">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-indigo-400 to-purple-500"></div>
                    @endif
                </div>
            </div>

            <!-- Hall Info -->
            <div class="p-6 mb-6 bg-white rounded-lg shadow-md">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="mb-2 text-3xl font-bold text-gray-900">{{ $hall->name }}</h1>
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            {{ $hall->city->name }}, {{ $hall->address }}
                        </div>
                    </div>
                    @if($hall->average_rating > 0)
                        <div class="flex items-center px-3 py-2 space-x-1 rounded-lg bg-yellow-50">
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="font-semibold">{{ number_format($hall->average_rating, 1) }}</span>
                            <span class="text-gray-600">({{ $hall->total_reviews }})</span>
                        </div>
                    @endif
                </div>

                <!-- Description -->
                <div class="prose max-w-none">
                    <h3 class="mb-2 text-lg font-semibold">About This Hall</h3>
                    <p class="text-gray-700">{!! $hall->description !!}</p>
                </div>

                <!-- Capacity & Pricing -->
                <div class="grid grid-cols-2 gap-4 p-4 mt-6 rounded-lg md:grid-cols-4 bg-gray-50">
                    <div>
                        <div class="text-sm text-gray-600">Capacity</div>
                        <div class="text-lg font-semibold">{{ $hall->capacity_min }}-{{ $hall->capacity_max }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Area</div>
                        <div class="text-lg font-semibold">{{ $hall->area }} m²</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Price/Day</div>
                        <div class="text-lg font-semibold text-indigo-600">{{ number_format($hall->price_per_slot, 3) }} OMR</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Total Bookings</div>
                        <div class="text-lg font-semibold">{{ $hall->total_bookings }}</div>
                    </div>
                </div>
            </div>

            <!-- Features -->
            @if($features->count() > 0)
                <div class="p-6 mb-6 bg-white rounded-lg shadow-md">
                    <h3 class="mb-4 text-lg font-semibold">Features & Amenities</h3>
                    <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                        @foreach($features as $feature)
                            <div class="flex items-center space-x-2">

                                <x-dynamic-component :component="$feature->icon" class="w-5 h-5 text-green-500" />
                                <span class="text-gray-700">{{ $feature->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Extra Services -->
            @if($hall->activeExtraServices->count() > 0)
                <div class="p-6 mb-6 bg-white rounded-lg shadow-md">
                    <h3 class="mb-4 text-lg font-semibold">Available Extra Services</h3>
                    <div class="space-y-3">
                        @foreach($hall->activeExtraServices as $service)
                            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50">
                                <div>
                                    <div class="font-medium">{{ $service->name }}</div>
                                    <div class="text-sm text-gray-600">{!! $service->description !!}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-indigo-600">{{ number_format($service->price, 3) }} OMR</div>
                                    <div class="text-xs text-gray-500">{{ $service->unit }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Booking Card -->
            <div class="sticky p-6 bg-white rounded-lg shadow-md top-6">
                <div class="mb-6 text-center">
                    <div class="mb-1 text-3xl font-bold text-indigo-600">
                        {{ number_format($hall->price_per_slot, 3) }} OMR
                    </div>
                    <div class="text-sm text-gray-600">per day</div>
                </div>

                @auth
                    <a href="{{ route('customer.book', $hall->slug) }}"
                        class="block w-full px-6 py-3 mb-4 font-semibold text-center text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700">
                        Book Now
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="block w-full px-6 py-3 mb-4 font-semibold text-center text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700">
                        Login to Book
                    </a>
                @endauth

                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Instant confirmation</span>
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Secure payment</span>
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                <div class="pt-6 mt-6 border-t border-gray-200">
                    <h4 class="mb-3 font-semibold">Contact Owner</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span class="text-gray-700">{{ $hall->owner->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-gray-700">{{ $hall->owner->email }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Similar Halls -->
    @if($similarHalls->count() > 0)
        <div class="mt-12">
            <h2 class="mb-6 text-2xl font-bold">Similar Halls</h2>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                @foreach($similarHalls as $similar)
                    <div class="overflow-hidden transition bg-white rounded-lg shadow-md hover:shadow-xl">
                        <div class="h-48 bg-gray-200">
                            @if($similar->main_image)
                                <img src="{{ Storage::url($similar->main_image) }}" class="object-cover w-full h-full">
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="mb-2 font-semibold">{{ $similar->name }}</h3>
                            <div class="mb-2 font-bold text-indigo-600">{{ number_format($similar->price_per_day, 3) }} OMR/day</div>
                            <a href="{{ route('customer.halls.show', $similar->slug) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                View Details →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
