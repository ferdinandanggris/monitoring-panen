<?php

namespace App\Services;

use App\Models\Session;
use App\Models\SessionDetail;
use App\Helpers\TrackingHelper;
use Illuminate\Support\Facades\Log; // 🆕 TAMBAHKAN INI

class UpdateSessionTrackingService
{
  public static function updateSessionSummary($sessionId)
  {
    $session = Session::with(['details', 'driver', 'machine'])->findOrFail($sessionId);
   
    $latestDetail = SessionDetail::where('session_id', $sessionId)
      ->orderBy('sequence', 'desc')
      ->first();

    if (!$latestDetail) {
      return null;
    }

    if ($session->last_sequence_session_detail && $latestDetail->sequence <= $session->last_sequence_session_detail) {
      return $session;
    }

    $details = SessionDetail::where('session_id', $sessionId)
      ->orderBy('sequence')
      ->get();

    if ($details->count() < 2) {
      return $session;
    }

    $rawData = $session->details->map(function ($item) {
      return [
        'latitude' => (float)$item->latitude,
        'longitude' => (float)$item->longitude,
        'sequence' => (int)$item->sequence,
        'speed' => (float)$item->speed, // PENTING: Untuk Filter Kecepatan
      ];
    })->toArray();

    // 🆕 LOGGING 1: Jumlah Titik Mentah
    Log::info('DEBUG AREA: Session ' . $sessionId . ' - Titik Mentah = ' . count($rawData));

    // 1. Bersihkan data (Filter kecepatan, titik diam, dan noise)
    $cleanedData = TrackingHelper::cleanTrackingData($rawData);

    // 🆕 LOGGING 2: Jumlah Titik BERSIH (Setelah Filter)
    Log::info('DEBUG AREA: Session ' . $sessionId . ' - Titik BERSIH = ' . count($cleanedData));

    // 2. Hitung Jarak BERSIH (hanya yang produktif)
    $totalDistance = TrackingHelper::calculateTotalDistance($cleanedData);

    // 🆕 LOGGING 3: Total Jarak Bersih
    Log::info('DEBUG AREA: Session ' . $sessionId . ' - Total Jarak Bersih = ' . $totalDistance . ' m');

    // 3. Hitung Luas Area Kerja Produktif (Raster Aproksimasi)
    $WORKING_WIDTH = 2.5;
    $totalArea = TrackingHelper::calculateWorkingArea($totalDistance, $WORKING_WIDTH);

    // 🆕 LOGGING 4: Total Area
    Log::info('DEBUG AREA: Session ' . $sessionId . ' - Total Area Final = ' . $totalArea . ' m²');

    $session->total_area = $totalArea;
    $session->total_distance = $totalDistance;
    $session->last_sequence_session_detail = $latestDetail->sequence;
    $session->last_calculate_at = now();

    return $session;
  }
}
