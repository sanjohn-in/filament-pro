<?php

namespace App\Filament\App\Resources\CarModels\Pages;

use App\Filament\App\Resources\CarModels\CarModelResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCarModel extends ViewRecord
{
    protected static string $resource = CarModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
