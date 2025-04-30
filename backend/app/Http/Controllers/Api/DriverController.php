<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Driver::all();
        return ApiResponse::success($data, "Berhasil mengambil data driver");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $driver = Driver::create($request->all());

        return ApiResponse::success($driver, "Driver created successfully", 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Driver $driver)
    {
        $driver = Driver::find($driver->id);
        if (!$driver) {
            return ApiResponse::error("Driver not found", 404);
        }

        return ApiResponse::success($driver, "Driver found");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Driver $driver)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $driver->update($request->all());

        return ApiResponse::success($driver, "Driver updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Driver $driver)
    {
        $driver = Driver::find($driver->id);
        if (!$driver) {
            return ApiResponse::error("Driver not found", 404);
        }

        if ($driver->delete()) {
            return ApiResponse::success(null, "Driver deleted successfully");
        }
        return ApiResponse::error("Failed to delete driver", 500);
    }
}
