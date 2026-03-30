<?php

namespace App\Filament\Admin\Resources\TableGroups\Pages;

use App\Filament\Admin\Resources\TableGroups\TableGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTableGroup extends EditRecord
{
    protected static string $resource = TableGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
