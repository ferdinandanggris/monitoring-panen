<?php

namespace App\Filament\Resources\SessionDetailResource\Pages;

use App\Filament\Resources\SessionDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSessionDetails extends ListRecords
{
    protected static string $resource = SessionDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
