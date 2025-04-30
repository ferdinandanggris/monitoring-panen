<?php

use App\Http\Controllers\Api\TrackingController;
use Illuminate\Support\Facades\Route;

Route::get('/tracking', [TrackingController::class, "index"])->name('api.tracking.index');
Route::get('/tracking-summary', [TrackingController::class, "getSessionSummary"])->name('api.tracking.summary');

Route::group(['prefix' => 'driver'], function () {
  Route::get('/', [\App\Http\Controllers\Api\DriverController::class, "index"])->name('api.driver.index');
  Route::post('/', [\App\Http\Controllers\Api\DriverController::class, "store"])->name('api.driver.store');
  Route::get('/{driver}', [\App\Http\Controllers\Api\DriverController::class, "show"])->name('api.driver.show');
  Route::put('/{driver}', [\App\Http\Controllers\Api\DriverController::class, "update"])->name('api.driver.update');
  Route::delete('/{driver}', [\App\Http\Controllers\Api\DriverController::class, "destroy"])->name('api.driver.destroy');
});

Route::group(['prefix' => 'machine'], function () {
  Route::get('/', [\App\Http\Controllers\Api\MachineController::class, "index"])->name('api.machine.index');
  Route::post('/', [\App\Http\Controllers\Api\MachineController::class, "store"])->name('api.machine.store');
  Route::get('/{machine}', [\App\Http\Controllers\Api\MachineController::class, "show"])->name('api.machine.show');
  Route::put('/{machine}', [\App\Http\Controllers\Api\MachineController::class, "update"])->name('api.machine.update');
  Route::delete('/{machine}', [\App\Http\Controllers\Api\MachineController::class, "destroy"])->name('api.machine.destroy');
});

Route::group(['prefix' => 'settings'], function () {
  Route::get('/', [\App\Http\Controllers\Api\SettingsController::class, "index"])->name('api.settings.index');
  Route::post('/', [\App\Http\Controllers\Api\SettingsController::class, "store"])->name('api.settings.store');
  Route::get('/{name}', [\App\Http\Controllers\Api\SettingsController::class, "show"])->name('api.settings.show');
  Route::put('/{settings}', [\App\Http\Controllers\Api\SettingsController::class, "update"])->name('api.settings.update');
  Route::delete('/{settings}', [\App\Http\Controllers\Api\SettingsController::class, "destroy"])->name('api.settings.destroy');
});
