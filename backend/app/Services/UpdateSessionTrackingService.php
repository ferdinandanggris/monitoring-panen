<?php

namespace App\Services;

use App\Models\Session;
use App\Models\SessionDetail;
use App\Helpers\TrackingHelper;

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

    if ($session->last_sequence_session_detail && $latestDetail->id <= $session->last_sequence_session_detail) {
      return $session;
    }

    $details = SessionDetail::where('session_id', $sessionId)
      ->orderBy('sequence')
      ->get();

    if ($details->count() < 2) {
      return $session;
    }

    $rawData = $details->map(function ($item) {
      return [
        'latitude' => $item->latitude,
        'longitude' => $item->longitude,
        'sequence' => $item->sequence,
      ];
    })->toArray();

    $cleanedData = TrackingHelper::cleanTrackingData($rawData);
    $totalDistance = TrackingHelper::calculateTotalDistance($cleanedData);
    $totalArea = count($cleanedData) * 4; // 2m x 2m kotak = 4m²

    $session->total_area = $totalArea;
    $session->total_distance = $totalDistance;
    $session->last_sequence_session_detail = $latestDetail->sequence;
    $session->last_calculate_at = now();
    $session->save();

    return $session;
  }
}
