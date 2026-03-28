<?php

namespace App\Filament\Admin\Resources\Configurations\Pages;

use App\Filament\Admin\Resources\Configurations\ConfigurationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewConfiguration extends ViewRecord
{
    protected static string $resource = ConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
