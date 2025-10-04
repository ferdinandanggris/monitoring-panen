<?php

namespace App\Helpers;

class TrackingHelper
{
  const EARTH_RADIUS = 6371000; // in meters
  const MIN_WORKING_SPEED = 5.0; // in km/h. Kecepatan minimal agar dianggap kerja produktif

  /**
   * Membersihkan data tracking:
   * Filter 1: Menghapus titik jika kecepatan di bawah ambang batas (filter inefisiensi/putar balik)
   * Filter 2 & 3: Menghapus titik diam dan noise jarak (jarak kecil banget < 0.1m)
   */
  public static function cleanTrackingData(array $rawData): array
  {
    // 1. Filter Awal: Buang semua titik yang kecepatannya di bawah batas
    $initialFiltered = collect($rawData)->filter(function ($point) {
        // Hanya pertahankan jika kecepatan >= MIN_WORKING_SPEED (5.0)
        return (float)($point['speed'] ?? 0.0) >= self::MIN_WORKING_SPEED;
    })->toArray();
    
    // Pastikan array kunci berurutan setelah filter
    $rawData = array_values($initialFiltered);
      
    // Urutkan berdasarkan sequence
    usort($rawData, function ($a, $b) {
      return $a['sequence'] <=> $b['sequence'];
    });

    $cleaned = [];
    $n = count($rawData);

    if ($n < 1) { return []; }
    
    // Selalu masukkan titik pertama yang lolos filter kecepatan
    $cleaned[] = $rawData[0];

    // 2. Filter Lanjutan: Titik Diam dan Noise Jarak (Mulai dari titik kedua)
    for ($i = 1; $i < $n; $i++) {
        $current = $rawData[$i];
        $prev = end($cleaned); // Ambil titik terakhir yang sudah bersih

        // Skip titik kalau lat dan lon sama persis (Titik diam)
        if (
            (float)$current['latitude'] == (float)$prev['latitude'] &&
            (float)$current['longitude'] == (float)$prev['longitude']
        ) {
            continue;
        }

        // Hitung jarak antar titik
        $distance = self::haversineDistance(
            (float)$prev['latitude'],
            (float)$prev['longitude'],
            (float)$current['latitude'],
            (float)$current['longitude']
        );

        // Jika jarak kurang dari 0.1 meter, skip (Noise kecil)
        if ($distance < 0.1) {
            continue;
        }

        $cleaned[] = $current;
    }
    
    return array_values($cleaned);
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
        (float)$current['latitude'],
        (float)$current['longitude'],
        (float)$next['latitude'],
        (float)$next['longitude']
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
   * Menghitung Luas Area Kerja Produktif (Jarak x Lebar Kerja).
   * Ini adalah metode aproksimasi Raster/Swath terbaik untuk MariaDB.
   */
  public static function calculateWorkingArea(float $totalDistance, float $workingWidthInMeters): float
  {
      $area = $totalDistance * $workingWidthInMeters;
      return round($area, 2); // Meter Persegi (m²)
  }
  
  /**
   * Menghitung luas Geodesik dari poligon berdasarkan koordinat Lat/Lng.
   * (Dipertahankan untuk Luas Batas)
   */
  public static function calculateGeodesicArea(array $points): float
  {
      // ... (kode calculateGeodesicArea sama dengan sebelumnya, dipertahankan)
      $points = array_values($points);
      
      $area = 0.0;
      $earthRadius = self::EARTH_RADIUS; 
      $n = count($points);
      
      if ($n < 3) { 
          return 0.0;
      }
      
      $closedPoints = $points;
      $firstPoint = $points[0]; 
      $lastPoint = $points[$n - 1];

      if (
          (float)$firstPoint['latitude'] !== (float)$lastPoint['latitude'] || 
          (float)$firstPoint['longitude'] !== (float)$lastPoint['longitude']
      ) {
          $closedPoints[] = $firstPoint;
          $n = count($closedPoints); 
      }
      
      for ($i = 0; $i < $n - 1; $i++) {
          $p1 = $closedPoints[$i];
          $p2 = $closedPoints[$i + 1];

          $lat1 = (float)$p1['latitude'];
          $lng1 = (float)$p1['longitude'];
          $lat2 = (float)$p2['latitude'];
          $lng2 = (float)$p2['longitude'];
          
          $lat1Rad = deg2rad($lat1);
          $lng1Rad = deg2rad($lng1);
          $lat2Rad = deg2rad($lat2);
          $lng2Rad = deg2rad($lng2);

          $area += ($lng2Rad - $lng1Rad) * (2 + sin($lat1Rad) + sin($lat2Rad));
      }

      $area = $area * $earthRadius * $earthRadius / 2.0;

      return round(abs($area), 2); 
  }
  
  public static function convertSqMetersToHectares(float $squareMeters): float
  {
      return round($squareMeters / 10000, 4);
  }
}