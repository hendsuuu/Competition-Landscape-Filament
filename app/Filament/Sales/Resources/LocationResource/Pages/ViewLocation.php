<?php

namespace App\Filament\Sales\Resources\LocationResource\Pages;

use App\Filament\Sales\Resources\LocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLocation extends ViewRecord
{
    protected static string $resource = LocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
