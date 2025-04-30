import * as turf from "@turf/turf";
import L from "leaflet";
import IconDefault from "../assets/icons/marker.png";
import "leaflet.gridlayer.googlemutant";

// Mengatur ikon marker secara manual
const DefaultIcon = L.icon({
  iconUrl: IconDefault, // URL ikon
  iconSize: [15, 25], // Ukuran ikon lebih kecil
  iconAnchor: [7, 25], // Anchor disesuaikan dengan ukuran ikon
  popupAnchor: [1, -20], // Popup juga disesuaikan dengan ukuran
  shadowSize: [25, 25], // Shadow disesuaikan proporsional
});

class Map {
  totalArea = 0;
  existingPoints = [];
  addedGrids = [];
  marker = [];
  gridLengthInMeters = 2;
  gridWidthInMeters = 2;

  map;
  constructor() {
  }

  initMap() {
    //inisialisasi map
    this.map = L.map("map", {
      center: [-7.8644782, 111.4819092], // Ganti dengan koordinat awal
      zoom: 30, // Tingkat zoom
    });

    // Menambahkan tile layer leaflet
    // L.tileLayer("https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}@2x?access_token=pk.eyJ1IjoibWJva25lYW50b24iLCJhIjoiY20ycmprMWJ4MWZrcDJrb3Jyb295Y3BzcSJ9.Ahnny5R-d-maOhOUaLuMOA", {
    //   attribution: '© <a href="https://www.mapbox.com/">Mapbox</a>',
    //   maxZoom: 20,
    //   id: "mapbox/satellite-v9", // Gaya peta dari Mapbox (misalnya streets-v11)
    //   tileSize: 512,
    //   detectRetina: true,
    //   zoomOffset: -1,
    //   accessToken: "pk.eyJ1IjoibWJva25lYW50b24iLCJhIjoiY20ycmprMWJ4MWZrcDJrb3Jyb295Y3BzcSJ9.Ahnny5R-d-maOhOUaLuMOA",
    // }).addTo(this.map);

    const googleLayer = L.gridLayer.googleMutant({
      type: 'satellite', // Ganti dengan 'roadmap', 'satellite', 'terrain', atau 'hybrid'
      updateWhenIdle: false,
    }).addTo(this.map);
  }

  removeMarker() {
    this.marker.forEach((m) => {
      this.map.removeLayer(m);
    });
  }

  removeAllGrids() {
    this.addedGrids.forEach((grid) => {
      this.map.removeLayer(grid);
    });
    this.addedGrids = [];
    this.totalArea = 0;
  }

  addMarker(lat, lng) {
    const marker = L.marker([lat, lng], { icon: DefaultIcon }).addTo(this.map);
    // marker.bindPopup(`Titik GPS (${lat.toFixed(4)}, ${lng.toFixed(4)})<br>Luas Grid: ${(Number(area.toFixed(2)) - Number(intersectionArea.toFixed(2))).toFixed(2)} m²<br>Total Luas: ${this.totalArea.toFixed(2)} m²`).openPopup();
    marker.bindPopup(`Titik GPS (${lat}, ${lng})`).openPopup();
    this.marker.push(marker);
  }

  // Fungsi untuk mengonversi meter ke derajat untuk latitude dan longitude
  metersToDegrees(lat, metersLat, metersLng) {
    const latDegree = metersLat / 111320; // 1 derajat lintang ≈ 111.32 km
    const lonDegree = metersLng / (111320 * Math.cos((lat * Math.PI) / 180)); // 1 derajat bujur tergantung pada lintang
    return { latDegree, lonDegree };
  }

  // Fungsi untuk menggambar satu grid persegi panjang di sekitar titik GPS
  drawRectangleGridSquare(lng, lat, lengthInMeters, widthInMeters) {
    const { latDegree, lonDegree } = this.metersToDegrees(lat, lengthInMeters, widthInMeters);

    // Buat koordinat untuk poligon (grid rectangle)
    const polygonCoords = [
      [lng - lonDegree / 2, lat - latDegree / 2], // Sudut kiri bawah
      [lng + lonDegree / 2, lat - latDegree / 2], // Sudut kanan bawah
      [lng + lonDegree / 2, lat + latDegree / 2], // Sudut kanan atas
      [lng - lonDegree / 2, lat + latDegree / 2], // Sudut kiri atas
      [lng - lonDegree / 2, lat - latDegree / 2], // Kembali ke sudut kiri bawah
    ];

    // Buat geometri poligon menggunakan Turf.js
    const gridRectangle = turf.polygon([polygonCoords]);

    // Tambahkan grid rectangle ke peta dengan garis tepi yang jelas
    L.geoJSON(gridRectangle, {
      style: function () {
        return { color: "blue", weight: 1, fillOpacity: 0.5 };
      },
    }).addTo(this.map);

    // Hitung luas dalam meter persegi
    const area = turf.area(gridRectangle);

    return { area, gridRectangle };
  }

  getActualMeterByArea = (area, lng, lat) => {

    const { latDegree, lonDegree } = this.metersToDegrees(lat, this.gridLengthInMeters, this.gridWidthInMeters);

    // Buat koordinat untuk poligon (grid rectangle)
    const polygonCoords = [
      [lng - lonDegree / 2, lat - latDegree / 2], // Sudut kiri bawah
      [lng + lonDegree / 2, lat - latDegree / 2], // Sudut kanan bawah
      [lng + lonDegree / 2, lat + latDegree / 2], // Sudut kanan atas
      [lng - lonDegree / 2, lat + latDegree / 2], // Sudut kiri atas
      [lng - lonDegree / 2, lat - latDegree / 2], // Kembali ke sudut kiri bawah
    ];

    // Buat geometri poligon menggunakan Turf.js
    const gridRectangle = turf.polygon([polygonCoords]);

    let intersectionArea = 0;
    // Jika tumpang tindih, loop untuk setiap grid yang tumpang tindih
    this.addedGrids.forEach((overlappingGrid) => {
      const intersection = turf.intersect(turf.featureCollection([overlappingGrid, gridRectangle]));

      // Hitung luas potongan
      if (intersection) intersectionArea = turf.area(intersection);
    });

    return area - intersectionArea;
  };

  getMeterByArea = (lng, lat, lengthInMeters, widthInMeters) => {
    const { latDegree, lonDegree } = this.metersToDegrees(lat, lengthInMeters, widthInMeters);

    // Buat koordinat untuk poligon (grid rectangle)
    const polygonCoords = [
      [lng - lonDegree / 2, lat - latDegree / 2], // Sudut kiri bawah
      [lng + lonDegree / 2, lat - latDegree / 2], // Sudut kanan bawah
      [lng + lonDegree / 2, lat + latDegree / 2], // Sudut kanan atas
      [lng - lonDegree / 2, lat + latDegree / 2], // Sudut kiri atas
      [lng - lonDegree / 2, lat - latDegree / 2], // Kembali ke sudut kiri bawah
    ];

    // Buat geometri poligon menggunakan Turf.js
    const gridRectangle = turf.polygon([polygonCoords]);

    // Hitung luas dalam meter persegi
    const area = turf.area(gridRectangle);
    return area;
  };

  pointToMap(lat, lng) {
    // Tambahkan marker pada titik GPS yang diklik
    // const marker = L.marker([lat, lng], {icon : DefaultIcon}).addTo(this.map);
    this.addMarker(lat, lng);
    this.existingPoints.push([lng, lat]); // Simpan titik yang baru

    // Hitung luas dan gambar grid persegi panjang di sekitar titik
    const { area, gridRectangle } = this.drawRectangleGridSquare(lng, lat, this.gridLengthInMeters, this.gridWidthInMeters);

    let intersectionArea = 0;
    // Jika tumpang tindih, loop untuk setiap grid yang tumpang tindih
    this.addedGrids.forEach((overlappingGrid) => {
      const intersection = turf.intersect(turf.featureCollection([overlappingGrid, gridRectangle]));

      // Hitung luas potongan
      if (intersection) intersectionArea = turf.area(intersection);

      // Gambar hasil potongan
      // L.geoJSON(intersection, {
      //   style: function () {
      //     return { color: "red", weight: 1, fillOpacity: 0.5 }; // Warna garis tepi, ketebalan, dan opacity isi
      //   },
      // }).addTo(this.map);
    });

    this.totalArea += area; // Tambahkan area bukan potongan ke luas total

    this.addedGrids.push(gridRectangle); // Simpan grid yang baru ditambahkan
    // marker.bindPopup(`Titik GPS (${lat.toFixed(4)}, ${lng.toFixed(4)})<br>Luas Grid: ${(Number(area.toFixed(2)) - Number(intersectionArea.toFixed(2))).toFixed(2)} m²<br>Total Luas: ${this.totalArea.toFixed(2)} m²`).openPopup();

    if (this.addedGrids.length > 1) {
      // Combine geometries of all added grids
      // const combinedGrids = turf.union(turf.featureCollection([...addedGrids]));
      // tampikan grid yang sudah digabungkan di peta kedua
      // L.geoJSON(combinedGrids, {
      //   style: function () {
      //     return { color: "blue", weight: 1, fillOpacity: 1 };
      //   },
      // }).addTo(map2);
      // Tampilkan total luas di peta kedua
      // document.getElementById("luas").innerHTML = `Total Luas: ${totalArea.toFixed(2)} m²`;
    }
  }
}

export default Map;
