<?php

namespace App\Filament\App\Resources\Owners\Pages;

use App\Filament\App\Resources\Owners\OwnerResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOwner extends ViewRecord
{
    protected static string $resource = OwnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
