<x-filament::page>
    <div class="flex flex-col md:flex-row gap-6">
        <!-- MAP SECTION -->
     <!-- Map Section -->
     <div class="w-full md:w-3/4">
      <div id="map" class="w-full rounded-lg shadow" style="height: 500px;"></div>
  </div>

        <!-- INFO SECTION -->
        <div class="w-full md:w-1/4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-gray-600 text-sm">Nama Sopir</div>
                    <div class="text-lg font-bold">{{ $sopir->nama ?? '-' }}</div>
                </div>

                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-gray-600 text-sm">Tanggal</div>
                    <div class="text-lg font-bold">{{ $tanggal }}</div>
                </div>

                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-gray-600 text-sm">Total Panen</div>
                    <div class="text-lg font-bold">{{ number_format($totalPanen, 2) }} m²</div>
                </div>

                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-gray-600 text-sm">Estimasi Harga</div>
                    <div class="text-lg font-bold">Rp {{ number_format($hargaPanen) }}</div>
                </div>

                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-gray-600 text-sm">Kecepatan Rata-rata</div>
                    <div class="text-lg font-bold">{{ number_format($kecepatan, 2) }} km/jam</div>
                </div>

                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-gray-600 text-sm">Total Jarak Tempuh</div>
                    <div class="text-lg font-bold">{{ number_format($jarak, 2) }} km</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet Map Script -->
    @push('scripts')
        <link
            rel="stylesheet"
            href="/css/leaflet/leaflet.css"
            crossorigin=""
        />
        <script src="/js/leaflet/leaflet.js"
            crossorigin="">
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const map = L.map('map').setView([-6.200000, 106.816666], 13); // Default Jakarta

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                // Contoh tambah marker dummy
                L.marker([-6.200000, 106.816666])
                    .addTo(map)
                    .bindPopup('Mesin Combi 001')
                    .openPopup();

                setTimeout(() => {
                  map.invalidateSize();
                }, 100);
            });
        </script>
    @endpush
</x-filament::page>