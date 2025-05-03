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
}
