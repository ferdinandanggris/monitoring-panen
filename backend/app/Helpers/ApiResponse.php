<?php

namespace App\Helpers;

class ApiResponse
{
  public static function success($data, $message = "Berhasil", $status = 200)
  {
    return response()->json([
      'success' => true,
      'message' => $message,
      'data' => $data
    ], $status);
  }

  public static function error($message = "Terjadi kesalahan", $status = 400)
  {
    return response()->json([
      'success' => false,
      'message' => $message
    ], $status);
  }
}
