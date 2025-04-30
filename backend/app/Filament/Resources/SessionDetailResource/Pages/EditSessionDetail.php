<?php

namespace App\Filament\Resources\SessionDetailResource\Pages;

use App\Filament\Resources\SessionDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSessionDetail extends EditRecord
{
    protected static string $resource = SessionDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
