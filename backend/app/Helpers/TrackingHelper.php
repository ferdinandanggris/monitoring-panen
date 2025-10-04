<?php

namespace App\Helpers;

class TrackingHelper
{
  const EARTH_RADIUS = 6371000; // in meters
  const MIN_WORKING_SPEED = 0.0; // in km/h. Kecepatan minimal agar dianggap kerja produktif

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
  
  // --- Fungsi Baru untuk Convex Hull ---

  /**
   * Menghitung kuadrat jarak antara dua titik planar. Digunakan untuk sorting collinear.
   */
  private static function sqDistance(array $p1, array $p2): float
  {
      return pow($p1['x'] - $p2['x'], 2) + pow($p1['y'] - $p2['y'], 2);
  }

  /**
   * Menghitung cross product (orientasi) dari tiga titik (o, a, b).
   * > 0 : counter-clockwise (CCW) turn
   * < 0 : clockwise (CW) turn
   * = 0 : collinear
   */
  private static function crossProduct(array $o, array $a, array $b): float
  {
      // o, a, b adalah array ['x' => x, 'y' => y]
      return (($a['x'] - $o['x']) * ($b['y'] - $o['y'])) - (($a['y'] - $o['y']) * ($b['x'] - $o['x']));
  }

  /**
   * Menggunakan algoritma Graham Scan untuk menemukan Convex Hull (batas terluar) dari serangkaian titik planar.
   */
  public static function findConvexHull(array $points): array
  {
      // Membutuhkan minimal 3 titik
      if (count($points) < 3) {
          return $points;
      }

      // 1. Temukan Titik Pivot (Titik terendah Y, terkecil X)
      usort($points, function ($a, $b) {
          if ($a['y'] != $b['y']) {
              return $a['y'] <=> $b['y'];
          }
          return $a['x'] <=> $b['x'];
      });
      $p0 = $points[0];
      
      // 2. Sort titik sisanya berdasarkan sudut polar dari p0 (menggunakan cross product)
      $remainingPoints = array_slice($points, 1);
      
      usort($remainingPoints, function ($a, $b) use ($p0) {
          $cp = self::crossProduct($p0, $a, $b);
          
          // Jika tidak kolinear, sort berdasarkan cross product (CCW)
          if ($cp != 0) {
              return -$cp;
          }
          
          // Jika kolinear, sort berdasarkan jarak dari p0 (terjauh terakhir)
          $distA = self::sqDistance($p0, $a);
          $distB = self::sqDistance($p0, $b);
          return $distA <=> $distB;
      });

      // Hapus titik kolinear di tengah, hanya simpan yang terjauh
      $m = count($remainingPoints);
      if ($m > 0) {
          $filteredRemaining = [$remainingPoints[0]];
          for ($i = 1; $i < $m; $i++) {
              $lastFiltered = end($filteredRemaining);
              $cp = self::crossProduct($p0, $lastFiltered, $remainingPoints[$i]);

              if ($cp != 0) {
                  $filteredRemaining[] = $remainingPoints[$i];
              } else {
                  // Collinear: update last point to current point (keep farthest)
                  $filteredRemaining[count($filteredRemaining) - 1] = $remainingPoints[$i];
              }
          }
          $remainingPoints = $filteredRemaining;
      }
      
      if (count($remainingPoints) < 2) {
          return $points;
      }

      // 3. Proses Stack Graham Scan
      $stack = [$p0, $remainingPoints[0]];
      
      for ($i = 1; $i < count($remainingPoints); $i++) {
          $nextPoint = $remainingPoints[$i];
          
          // Pop jika 3 titik terakhir membuat belokan CW atau kolinear
          while (count($stack) > 1 && self::crossProduct($stack[count($stack) - 2], $stack[count($stack) - 1], $nextPoint) <= 0) {
              array_pop($stack);
          }
          $stack[] = $nextPoint;
      }
      
      return $stack;
  }
  
  // --- Fungsi Lama (Shoelace) Diubah untuk Memakai Convex Hull ---

  /**
   * Menghitung luas poligon menggunakan Formula Shoelace.
   * Fungsi ini sekarang memfilter titik input dengan Convex Hull.
   */
  public static function calculateGeodesicArea(array $points): float
  {
      $points = array_values($points);
      
      if (count($points) < 3) { 
          return 0.0;
      }

      // 1. Tentukan Titik Origin Lokal (Titik pertama) untuk konversi planar
      $latStart = (float)$points[0]['latitude'];
      $lngStart = (float)$points[0]['longitude'];

      // 2. Konversi Lat/Lng menjadi koordinat Planar Lokal (X/Y dalam meter)
      $planarPoints = [];
      foreach ($points as $p) {
          $lat = (float)$p['latitude'];
          $lng = (float)$p['longitude'];

          // Y (Utara-Selatan)
          $y = self::haversineDistance($latStart, $lngStart, $lat, $lngStart);
          if ($lat < $latStart) { $y = -$y; }

          // X (Timur-Barat)
          $x = self::haversineDistance($latStart, $lngStart, $latStart, $lng);
          if ($lng < $lngStart) { $x = -$x; }

          $planarPoints[] = ['x' => $x, 'y' => $y];
      }

      // 3. Terapkan CONVEX HULL untuk mendapatkan hanya titik batas terluar
      $hullPoints = self::findConvexHull($planarPoints);
      
      $n = count($hullPoints);
      if ($n < 3) { 
          return 0.0;
      }

      // 4. Pastikan Poligon Tertutup (Titik terakhir = Titik pertama)
      $firstHullPoint = $hullPoints[0];
      $lastHullPoint = $hullPoints[$n - 1];

      if (
          abs($firstHullPoint['x'] - $lastHullPoint['x']) > 0.01 ||
          abs($firstHullPoint['y'] - $lastHullPoint['y']) > 0.01
      ) {
          $hullPoints[] = $firstHullPoint;
          $n = count($hullPoints);
      }
      
      $area = 0.0;

      // 5. Terapkan Formula Shoelace pada Hull Points: A = 0.5 * | Sum(X_i * Y_{i+1} - X_{i+1} * Y_i) |
      for ($i = 0; $i < $n - 1; $i++) {
          $p1 = $hullPoints[$i];
          $p2 = $hullPoints[$i + 1];
          $area += ($p1['x'] * $p2['y'] - $p2['x'] * $p1['y']);
      }

      $area = abs($area) / 2.0;

      return round($area, 2); 
  }
  
  public static function convertSqMetersToHectares(float $squareMeters): float
  {
      return round($squareMeters / 10000, 4);
  }
}