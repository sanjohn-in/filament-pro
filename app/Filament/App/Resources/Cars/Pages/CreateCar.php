<?php

namespace App\Filament\App\Resources\Cars\Pages;

use App\Filament\App\Resources\Cars\CarResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCar extends CreateRecord
{
    protected static string $resource = CarResource::class;
}
