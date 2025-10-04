<?php

namespace App\Helpers;

class TrackingHelper
{
  const EARTH_RADIUS = 6371000; // in meters

  /**
   * Membersihkan data tracking:
   * - Menghapus titik diam
   * - Menghapus noise (jarak kecil banget < 0.1m)
   */
  public static function cleanTrackingData(array $rawData): array
  {
    // Urutkan berdasarkan sequence
    usort($rawData, function ($a, $b) {
      return $a['sequence'] <=> $b['sequence'];
    });

    $cleaned = [];

    for ($i = 0; $i < count($rawData) - 1; $i++) {
      $current = $rawData[$i];
      $next = $rawData[$i + 1];

      // Skip titik kalau lat dan lon sama persis
      if (
        $current['latitude'] == $next['latitude'] &&
        $current['longitude'] == $next['longitude']
      ) {
        continue;
      }

      // Hitung jarak antar titik
      $distance = self::haversineDistance(
        $current['latitude'],
        $current['longitude'],
        $next['latitude'],
        $next['longitude']
      );

      // Jika jarak kurang dari 0.1 meter, skip
      if ($distance < 0.1) {
        continue;
      }

      $cleaned[] = $current;
    }

    // Masukkan titik terakhir (supaya track utuh)
    if (!empty($rawData)) {
      $cleaned[] = end($rawData);
    }

    return $cleaned;
  }

  /**
   * Menghitung total jarak tempuh dari data bersih.
   */
  public static function calculateTotalDistance(array $cleanedData): float
  {
    $totalDistance = 0.0;

    for ($i = 0; $i < count($cleanedData) - 1; $i++) {
      $current = $cleanedData[$i];
      $next = $cleanedData[$i + 1];

      $distance = self::haversineDistance(
        $current['latitude'],
        $current['longitude'],
        $next['latitude'],
        $next['longitude']
      );

      $totalDistance += $distance;
    }

    return round($totalDistance, 2); // Meter
  }

  /**
   * Haversine formula untuk menghitung jarak antar dua koordinat.
   */
  public static function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
  {
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    $dLat = $lat2 - $lat1;
    $dLon = $lon2 - $lon1;

    $a = sin($dLat / 2) * sin($dLat / 2) +
      cos($lat1) * cos($lat2) *
      sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return self::EARTH_RADIUS * $c;
  }

  /**
   * Menghitung luas Geodesik dari poligon berdasarkan koordinat Lat/Lng.
   * Format input: Array dari Associative Array: [['latitude' => lat, 'longitude' => lng], ...]
   *
   * @param array $points Array koordinat (Assoc Array)
   * @return float Luas dalam Meter Persegi (m²). Mengembalikan 0 jika kurang dari 3 titik.
   */
  public static function calculateGeodesicArea(array $points): float
  {
      // Re-index array untuk memastikan kunci (key) berurutan dari 0
      $points = array_values($points);
      
      $area = 0.0;
      $earthRadius = self::EARTH_RADIUS; 
      $n = count($points);
      
      // 1. Check jumlah titik. Poligon harus memiliki minimal 3 titik
      if ($n < 3) { 
          return 0.0;
      }
      
      // 2. Pastikan poligon tertutup
      $closedPoints = $points;
      
      // Mengakses titik pertama dan terakhir menggunakan key numerik setelah re-index
      $firstPoint = $points[0]; 
      $lastPoint = $points[$n - 1];

      // Jika titik awal dan akhir tidak sama, tambahkan titik awal ke akhir
      // Mengakses nilai Latitude dan Longitude menggunakan key string
      if (
          (float)$firstPoint['latitude'] !== (float)$lastPoint['latitude'] || 
          (float)$firstPoint['longitude'] !== (float)$lastPoint['longitude']
      ) {
          $closedPoints[] = $firstPoint;
          $n = count($closedPoints); 
      }
      
      // 3. Lakukan perhitungan Geodesik (Formula Lambert)
      for ($i = 0; $i < $n - 1; $i++) {
          $p1 = $closedPoints[$i]; 
          $p2 = $closedPoints[$i + 1];

          // Mengambil nilai dari array asosiatif dan mengkonversi ke float (dari string)
          $lat1 = (float)$p1['latitude'];
          $lng1 = (float)$p1['longitude'];
          $lat2 = (float)$p2['latitude'];
          $lng2 = (float)$p2['longitude'];

          // Konversi derajat ke radian
          $lat1Rad = deg2rad($lat1);
          $lng1Rad = deg2rad($lng1);
          $lat2Rad = deg2rad($lat2);
          $lng2Rad = deg2rad($lng2);

          // Formula: Σ (lng2 - lng1) * (2 + sin(lat1) + sin(lat2))
          $area += ($lng2Rad - $lng1Rad) * (2 + sin($lat1Rad) + sin($lat2Rad));
      }

      // 4. Hitung Luas Akhir: Area * (R^2 / 2)
      $area = $area * $earthRadius * $earthRadius / 2.0;

      return round(abs($area), 2); // Meter Persegi (m²)
  }

  /**
   * Konversi Meter Persegi ke Hektar.
   *
   * @param float $squareMeters
   * @return float Luas dalam Hektar (Ha)
   */
  public static function convertSqMetersToHectares(float $squareMeters): float
  {
      return round($squareMeters / 10000, 4);
  }
}