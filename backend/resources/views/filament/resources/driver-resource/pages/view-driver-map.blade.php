<x-filament::page>
    <div class="mb-4 text-xl font-bold text-gray-800">Driver: {{ $driver->name }}</div>

    <div class="mb-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-filament::card>
            <div class="text-sm text-gray-600">Total Panen</div>
            <div class="text-xl font-bold">{{ number_format($totalArea, 2) }} m²</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm text-gray-600">Kecepatan Rata-rata</div>
            <div class="text-xl font-bold">{{ number_format($averageSpeed, 2) }} km/jam</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm text-gray-600">Jarak Ditempuh</div>
            <div class="text-xl font-bold">{{ number_format($totalDistance, 2) }} km</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-sm text-gray-600">Konversi Rupiah</div>
            <div class="text-xl font-bold">Rp {{ number_format($totalCost, 0, ',', '.') }}</div>
        </x-filament::card>
    </div>

    <div id="map" class="w-full rounded-lg shadow" style="height: 500px;"></div>
</x-filament::page>

@push('scripts')
<!-- Leaflet & Turf.js CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6.5.0/turf.min.js"></script>

<script>
class Map {
    constructor() {
        this.totalArea = 0;
        this.existingPoints = [];
        this.addedGrids = [];
        this.marker = [];
        this.gridLengthInMeters = 2;
        this.gridWidthInMeters = 2;
        this.map = null;
    }

    initMap() {
        this.map = L.map("map", {
            center: [-7.8644782, 111.4819092],
            zoom: 30,
        });
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(this.map);
    }

    addMarker(lat, lng) {
        const marker = L.marker([lat, lng]).addTo(this.map);
        marker.bindPopup(`Titik GPS (${lat}, ${lng})`).openPopup();
        this.marker.push(marker);
    }

    metersToDegrees(lat, metersLat, metersLng) {
        const latDegree = metersLat / 111320;
        const lonDegree = metersLng / (111320 * Math.cos((lat * Math.PI) / 180));
        return { latDegree, lonDegree };
    }

    drawRectangleGridSquare(lng, lat, lengthInMeters, widthInMeters) {
        const { latDegree, lonDegree } = this.metersToDegrees(lat, lengthInMeters, widthInMeters);
        const polygonCoords = [
            [lng - lonDegree / 2, lat - latDegree / 2],
            [lng + lonDegree / 2, lat - latDegree / 2],
            [lng + lonDegree / 2, lat + latDegree / 2],
            [lng - lonDegree / 2, lat + latDegree / 2],
            [lng - lonDegree / 2, lat - latDegree / 2],
        ];
        const gridRectangle = turf.polygon([polygonCoords]);
        L.geoJSON(gridRectangle, {
            style: () => ({ color: "blue", weight: 1, fillOpacity: 0.5 })
        }).addTo(this.map);
        return { area: turf.area(gridRectangle), gridRectangle };
    }

    pointToMap(lat, lng) {
        this.addMarker(lat, lng);
        const { area, gridRectangle } = this.drawRectangleGridSquare(lng, lat, this.gridLengthInMeters, this.gridWidthInMeters);

        let intersectionArea = 0;
        this.addedGrids.forEach((grid) => {
            const intersection = turf.intersect(turf.featureCollection([grid, gridRectangle]));
            if (intersection) intersectionArea = turf.area(intersection);
        });

        this.totalArea += (area - intersectionArea);
        this.addedGrids.push(gridRectangle);
    }
}

const mapInstance = new Map();
mapInstance.initMap();

let lastFetchedAt = null;
const driverId = {{ $driver->id }};

async function fetchNewPoints() {
    const url = lastFetchedAt
        ? `/api/tracking?machine_id=${driverId}&after=${encodeURIComponent(lastFetchedAt)}`
        : `/api/tracking?machine_id=${driverId}`;

    try {
        const res = await fetch(url);
        const newPoints = await res.json();
        newPoints.forEach(point => {
            mapInstance.pointToMap(point.latitude, point.longitude);
        });
        if (newPoints.length > 0) {
            lastFetchedAt = newPoints[newPoints.length - 1].recorded_at;
        }
    } catch (err) {
        console.error('Gagal fetch data tracking:', err);
    }
}

setInterval(fetchNewPoints, 5000);
</script>
@endpush
