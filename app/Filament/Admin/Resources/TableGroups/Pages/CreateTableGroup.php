<?php

namespace App\Filament\Admin\Resources\TableGroups\Pages;

use App\Filament\Admin\Resources\TableGroups\TableGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTableGroup extends CreateRecord
{
    protected static string $resource = TableGroupResource::class;
}
