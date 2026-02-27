<?php

namespace App\Filament\App\Resources\CarModels\Pages;

use App\Filament\App\Resources\CarModels\CarModelResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCarModel extends CreateRecord
{
    protected static string $resource = CarModelResource::class;
}
