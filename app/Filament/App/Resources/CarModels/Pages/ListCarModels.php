<?php

namespace App\Filament\App\Resources\CarModels\Pages;

use App\Filament\App\Resources\CarModels\CarModelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCarModels extends ListRecords
{
    protected static string $resource = CarModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
