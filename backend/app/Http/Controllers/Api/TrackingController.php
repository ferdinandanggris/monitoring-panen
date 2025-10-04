<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Helpers\TrackingHelper;
use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\SessionDetail;
use App\Models\Settings;
use App\Services\UpdateSessionTrackingService;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        // $query = SessionDetail::with(['session']);

        // if ($request->has('machine_id')) {
        //     $machineId = $request->get('machine_id');
        //     $query->whereHas('session', function ($q) use ($machineId) {
        //         $q->where('machine_id', $machineId);
        //     });
        // }

        // if ($request->has('driver_id')) {
        //     $driverId = $request->get('driver_id');
        //     $query->whereHas('session', function ($q) use ($driverId) {
        //         $q->where('driver_id', $driverId);
        //     });
        // }

        // if ($request->has('start_date')) {
        //     $query->where('recorded_at', '>=', $request->get('start_date'));
        // }

        // if ($request->has('end_date')) {
        //     $query->where('recorded_at', '<=', $request->get('end_date'));
        // }

        // $data = $query->orderBy('recorded_at')->get([
        //     'latitude',
        //     'longitude',
        //     'recorded_at',
        //     'session_id',
        //     'sequence',
        // ]);

        // $data = TrackingHelper::cleanTrackingData($data->toArray());

        $data = Session::with(['details', 'driver', 'machine'])
            ->when($request->has('machine_id'), function ($query) use ($request) {
                $query->where('machine_id', $request->get('machine_id'));
            })
            ->when($request->has('driver_id'), function ($query) use ($request) {
                $query->where('driver_id', $request->get('driver_id'));
            })
            ->when($request->has('start_date'), function ($query) use ($request) {
                $query->whereBetween('date', [$request->get('start_date'), $request->get('end_date')]);
            })
            // ->when($request->has('start_date'), function ($query) use ($request) {
            //     $query->whereHas('details', function ($q) use ($request) {
            //         $q->where('recorded_at', '>=', $request->get('start_date'));
            //     });
            // })
            // ->when($request->has('end_date'), function ($query) use ($request) {
            //     $query->whereHas('details', function ($q) use ($request) {
            //         $q->where('recorded_at', '<=', $request->get('end_date'));
            //     });
            // })
            ->orderBy('created_at')
            ->get();

        $data = $data->map(function ($item) {
            $item->details = $item->details->map(function ($detail) {
                $detail = [
                    'latitude' => $detail->latitude,
                    'longitude' => $detail->longitude,
                    'recorded_at' => $detail->recorded_at,
                    'sequence' => $detail->sequence,
                ];
                return $detail;
            });
            return [
                'date' => $item->date,
                'driver' => $item->driver,
                'machine' => $item->machine,
                "details" => TrackingHelper::cleanTrackingData($item->details->toArray()),
            ];
        });

        return ApiResponse::success($data, "Berhasil mengambil data tracking");
    }

    public function getSessionSummary(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
        $settings = Settings::where('name', 'hargaPerMeter')->first();

        $sessions = Session::whereBetween('date', [$request->start_date, $request->end_date])
            ->with(['details', 'driver', 'machine'])->get();

        $summary = $sessions->map(function ($session) use ($settings) {
            $resultUpdate = UpdateSessionTrackingService::updateSessionSummary($session->id);
            if (!$resultUpdate) {
                $resultUpdate = $session;
            }
            $resultUpdate->details = TrackingHelper::cleanTrackingData($session->details->toArray());
            $resultUpdate->total_harga = $resultUpdate->total_area * $settings->value; // 1m² = 1000 IDR
            return $resultUpdate;
        });



        $result = [
            'sessions' => $summary,
            'total_area' => $summary->sum('total_area'),
            'total_distance' => $summary->sum('total_distance'),
            'total_harga' => $summary->sum('total_distance') * $settings->value, // 1m² = 1000 IDR
        ];

        return ApiResponse::success($result, "Berhasil mengambil data summary");
    }
}
