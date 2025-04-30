<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $machines = Machine::all();
        return ApiResponse::success($machines, "Berhasil mengambil data mesin");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $machine = Machine::create($request->all());

        return ApiResponse::success($machine, "Mesin created successfully", 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Machine $machine)
    {
        $machine = Machine::find($machine->id);
        if (!$machine) {
            return ApiResponse::error("Mesin not found", 404);
        }

        return ApiResponse::success($machine, "Mesin found");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Machine $machine)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $machine->update($request->all());

        return ApiResponse::success($machine, "Mesin updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Machine $machine)
    {
        $machine = Machine::find($machine->id);
        if (!$machine) {
            return ApiResponse::error("Mesin not found", 404);
        }

        if ($machine->delete()) {
            return ApiResponse::success(null, "Mesin deleted successfully");
        }

        return ApiResponse::error("Failed to delete mesin", 500);
    }
}
