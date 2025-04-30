<?php

namespace App\Filament\Resources\DriverResource\Pages;

use App\Filament\Resources\DriverResource;
use App\Models\Driver;
use App\Models\Session;
use App\Models\Settings;
use Filament\Resources\Pages\Page;
use Illuminate\Http\Request;

class ViewDriverMap extends Page
{
    protected static string $resource = DriverResource::class;

    protected static string $view = 'filament.resources.driver-resource.pages.view-driver-map';

    public $driver;
    public $totalArea;
    public $averageSpeed;
    public $totalDistance;
    public $totalCost;

    public function mount(Request $request, $record): void
    {
        $this->driver = Driver::findOrFail($record);

        $sessions = Session::where('driver_id', $record)->get();
        $this->totalArea = $sessions->sum('area');
        $this->averageSpeed = $sessions->avg('average_speed');
        $this->totalDistance = $this->averageSpeed * ($sessions->count() * 0.5); // estimasi 30 menit per sesi

        $harga = Settings::where('name', 'harga')->first()?->value ?? 2000;
        $this->totalCost = $this->totalArea * (int) $harga;
    }
}
