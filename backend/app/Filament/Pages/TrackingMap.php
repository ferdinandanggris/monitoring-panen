<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class TrackingMap extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.tracking-map';

    protected static ?string $navigationLabel = 'Peta Tracking Mesin';

    public $tanggal;
    public $totalPanen = 200;
    public $hargaPanen = 2000000;
    public $kecepatan = 10000;
    public $jarak = 100000;

    public function mount(): void
    {

        $this->tanggal = now()->format('Y-m-d');
        $this->totalPanen = 200;
        $this->hargaPanen = 2000000;
        $this->kecepatan = 10000;
        $this->jarak = 100000;
    }
}
