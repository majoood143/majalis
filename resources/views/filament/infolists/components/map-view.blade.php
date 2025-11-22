<div
    x-data="{
        lat: {{ $getRecord()->latitude ?? 23.5880 }},
        lng: {{ $getRecord()->longitude ?? 58.3829 }}
    }"
    x-init="
        const map = L.map($refs.map).setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        L.marker([lat, lng]).addTo(map);
    "
>
    <div x-ref="map" style="height: 400px; width: 100%; border-radius: 8px;"></div>
</div>
