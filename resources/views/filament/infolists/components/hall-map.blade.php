{{--
    Hall Map View Component
    Displays an interactive OpenStreetMap with hall location marker
    Handles Livewire/Filament SPA navigation
--}}
@php
    $record = $getRecord();
    $lat = $record->latitude ?? 23.5880;
    $lng = $record->longitude ?? 58.3829;
    $hallName = $record->name ?? 'Hall Location';
    $mapId = 'hall-map-' . $record->id . '-' . uniqid();
@endphp

<div class="w-full" wire:ignore>
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.css" integrity="sha384-b8ANgTJvdlAnWM5YGMpKn7Kodm+1k7NYNG9zdjTCcZcKatzYHwZ0RLdWarbJJVzU" crossorigin="anonymous" />

    {{-- Map Container --}}
    <div
        id="{{ $mapId }}"
        style="height: 400px; width: 100%; border-radius: 8px; z-index: 1;"
    ></div>

    {{-- Leaflet JS --}}
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js" integrity="sha384-u5N8qJeJOO2iqNjIKTdl6KeKsEikMAmCUBPc6sC6uGpgL34aPJ4VgNhuhumedpEk" crossorigin="anonymous"></script>

    <script>
        (function() {
            function initMap() {
                const mapContainer = document.getElementById('{{ $mapId }}');

                // Check if map container exists and is not already initialized
                if (!mapContainer || mapContainer._leaflet_id) {
                    return;
                }

                // Initialize map
                const map = L.map('{{ $mapId }}').setView([{{ $lat }}, {{ $lng }}], 15);

                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                // Add marker
                const marker = L.marker([{{ $lat }}, {{ $lng }}]).addTo(map);

                // Add popup with hall name
                marker.bindPopup('<strong>{{ addslashes($hallName) }}</strong>').openPopup();

                // Fix map rendering issue in tabs/modals
                setTimeout(function() {
                    map.invalidateSize();
                }, 200);
            }

            // Initialize immediately
            initMap();

            // Re-initialize on Livewire navigation
            document.addEventListener('livewire:navigated', initMap);

            // Backup: Re-initialize after short delay
            setTimeout(initMap, 300);
        })();
    </script>
</div>
