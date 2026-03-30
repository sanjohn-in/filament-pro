<?php

namespace App\Filament\Admin\Resources\TableGroups\Pages;

use App\Filament\Admin\Resources\TableGroups\TableGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTableGroups extends ListRecords
{
    protected static string $resource = TableGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
