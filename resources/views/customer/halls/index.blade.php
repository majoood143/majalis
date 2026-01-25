<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Halls - Majalis</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Leaflet CSS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        #map { height: 500px; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="container px-4 py-8 mx-auto" x-data="hallsPage()">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="mb-2 text-4xl font-bold text-gray-800">Browse Event Halls</h1>
            <p class="text-gray-600">Find the perfect venue for your special occasion in Oman</p>
        </div>

        <!-- View Toggle -->
        <div class="flex gap-2 mb-6">
            <button
                @click="view = 'grid'"
                :class="view === 'grid' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'"
                class="px-4 py-2 transition border rounded-lg hover:bg-blue-50">
                <svg class="inline w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                Grid View
            </button>
            <button
                @click="view = 'map'"
                :class="view === 'map' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'"
                class="px-4 py-2 transition border rounded-lg hover:bg-blue-50">
                <svg class="inline w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                </svg>
                Map View
            </button>
        </div>

        <!-- Filters -->
        <div class="p-6 mb-8 bg-white rounded-lg shadow-md">
            <form method="GET" action="{{ route('customer.halls.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">

                <!-- Region Dropdown -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Region</label>
                    <select
                        name="region_id"
                        id="region-select"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        onchange="loadCities(this.value)">
                        <option value="">All Regions</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                {{ is_array($region->name) ? ($region->name[app()->getLocale()] ?? $region->name['en']) : $region->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- City Dropdown -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">City</label>
                    <select
                        name="city_id"
                        id="city-select"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                {{ is_array($city->name) ? ($city->name[app()->getLocale()] ?? $city->name['en']) : $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Capacity -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Guest Capacity</label>
                    <input
                        type="number"
                        name="capacity"
                        value="{{ request('capacity') }}"
                        placeholder="Number of guests"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Search -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Search</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search halls..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Price Range -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Min Price (OMR)</label>
                    <input
                        type="number"
                        name="min_price"
                        value="{{ request('min_price') }}"
                        placeholder="Min price"
                        step="0.001"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Max Price (OMR)</label>
                    <input
                        type="number"
                        name="max_price"
                        value="{{ request('max_price') }}"
                        placeholder="Max price"
                        step="0.001"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Buttons -->
                <div class="flex items-end gap-2 lg:col-span-2">
                    <button
                        type="submit"
                        class="flex-1 px-6 py-2 font-medium text-white transition bg-blue-600 rounded-lg hover:bg-blue-700">
                        Search Halls
                    </button>
                    <a
                        href="{{ route('customer.halls.index') }}"
                        class="px-6 py-2 font-medium text-gray-700 transition bg-gray-200 rounded-lg hover:bg-gray-300">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" x-cloak class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($halls as $hall)
                <div class="overflow-hidden transition bg-white rounded-lg shadow-md hover:shadow-xl">
                    <!-- Image -->
                    <div class="relative h-48 overflow-hidden bg-gray-200">
                        @if($hall->featured_image)
                            <img
                                src="{{ asset('storage/' . $hall->featured_image) }}"
                                alt="{{ is_array($hall->name) ? ($hall->name[app()->getLocale()] ?? $hall->name['en']) : $hall->name }}"
                                class="object-cover w-full h-full transition duration-300 hover:scale-110">
                        @else
                            <div class="flex items-center justify-center w-full h-full text-gray-400">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        @endif

                        @if($hall->is_featured)
                            <span class="absolute px-2 py-1 text-xs font-semibold text-yellow-900 bg-yellow-400 rounded-full top-2 left-2">
                                Featured
                            </span>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <h3 class="mb-2 text-xl font-semibold text-gray-800 truncate">
                            {{ is_array($hall->name) ? ($hall->name[app()->getLocale()] ?? $hall->name['en']) : $hall->name }}
                        </h3>

                        <div class="flex items-center mb-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ is_array($hall->city->name) ? ($hall->city->name[app()->getLocale()] ?? $hall->city->name['en']) : $hall->city->name }}
                        </div>

                        <div class="flex items-center mb-3 text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            {{ $hall->capacity_min }} - {{ $hall->capacity_max }} guests
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-blue-600">{{ number_format($hall->price_per_slot, 3) }}</span>
                                <span class="text-sm text-gray-600"> OMR</span>
                            </div>
                            <a
                                href="{{ route('customer.halls.show', $hall->slug) }}"
                                class="px-4 py-2 font-medium text-white transition bg-blue-600 rounded-lg hover:bg-blue-700">
                                View Details
                            </a>
                        </div>

                        @if($hall->average_rating > 0)
                            <div class="flex items-center mt-3">
                                <span class="text-lg text-yellow-500">★</span>
                                <span class="ml-1 text-sm text-gray-600">
                                    {{ number_format($hall->average_rating, 1) }}
                                    ({{ $hall->total_reviews }} reviews)
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-12 text-center col-span-full">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <h3 class="mb-2 text-xl font-semibold text-gray-700">No halls found</h3>
                    <p class="text-gray-500">Try adjusting your filters to see more results</p>
                </div>
            @endforelse
        </div>

        <!-- Map View majid -->
        <div x-show="view === 'map'" x-cloak>
            <div id="map" class="rounded-lg shadow-lg"></div>

            <div class="p-4 mt-4 border border-blue-200 rounded-lg bg-blue-50">
                <p class="text-sm text-blue-800">
                    <strong>{{ $mapHalls->count() }}</strong> halls with location data shown on map.
                    Click on markers to view hall details.
                </p>
            </div>
        </div>

        <!-- Pagination -->
        @if($halls->hasPages())
            <div class="mt-8" x-show="view === 'grid'">
                {{ $halls->links() }}
            </div>
        @endif
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        function hallsPage() {
            return {
                view: 'grid',
                map: null,
                markers: [],

                init() {
                    this.$watch('view', value => {
                        if (value === 'map' && !this.map) {
                            this.$nextTick(() => this.initMap());
                        }
                    });
                },

                initMap() {
                    // Initialize map centered on Oman
                    this.map = L.map('map').setView([23.6100, 58.5400], 7);

                    // Add tile layer
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors',
                        maxZoom: 19
                    }).addTo(this.map);

                    // Add markers for halls
                    const halls = @json($mapHalls);

                    if (halls.length === 0) {
                        alert('No halls with location data found.');
                        return;
                    }

                    halls.forEach(hall => {
                        if (hall.latitude && hall.longitude) {
                            const lat = parseFloat(hall.latitude);
                            const lng = parseFloat(hall.longitude);

                            const marker = L.marker([lat, lng]).addTo(this.map);

                            const hallName = typeof hall.name === 'object' ? (hall.name.en || hall.name.ar || 'Unnamed Hall') : hall.name;
                            const cityName = typeof hall.city.name === 'object' ? (hall.city.name.en || hall.city.name.ar || '') : hall.city.name;
                            const imageUrl = hall.featured_image ? `/storage/${hall.featured_image}` : '';

                            const popupContent = `
                                <div class="p-2" style="min-width: 200px;">
                                    ${imageUrl ? `<img src="${imageUrl}" class="object-cover w-full h-32 mb-2 rounded">` : ''}
                                    <h3 class="text-lg font-bold">${hallName}</h3>
                                    <p class="text-sm text-gray-600">${cityName}</p>
                                    <p class="mt-1 font-bold text-blue-600">${parseFloat(hall.price_per_slot).toFixed(3)} OMR</p>
                                    <a href="/halls/${hall.slug}" class="block px-3 py-1 mt-2 text-sm text-center text-white bg-blue-600 rounded hover:bg-blue-700">
                                        View Details
                                    </a>
                                </div>
                            `;

                            marker.bindPopup(popupContent);
                            this.markers.push(marker);
                        }
                    });

                    // Fit bounds to show all markers
                    if (this.markers.length > 0) {
                        const group = new L.featureGroup(this.markers);
                        this.map.fitBounds(group.getBounds().pad(0.1));
                    }
                }
            }
        }

        // Load cities when region changes
        function loadCities(regionId) {
            const citySelect = document.getElementById('city-select');

            if (!regionId) {
                citySelect.innerHTML = '<option value="">All Cities</option>';
                return;
            }

            fetch(`/halls/cities/${regionId}`)
                .then(response => response.json())
                .then(cities => {
                    citySelect.innerHTML = '<option value="">All Cities</option>';
                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.id;
                        const name = typeof city.name === 'object' ? (city.name.en || city.name.ar) : city.name;
                        option.textContent = name;
                        citySelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading cities:', error));
        }

        // Load cities on page load if region is selected
        document.addEventListener('DOMContentLoaded', function() {
            const regionSelect = document.getElementById('region-select');
            if (regionSelect.value) {
                loadCities(regionSelect.value);
            }
        });
    </script>
</body>
</html>



