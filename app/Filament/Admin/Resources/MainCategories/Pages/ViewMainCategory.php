<?php

namespace App\Filament\Admin\Resources\MainCategories\Pages;

use App\Filament\Admin\Resources\MainCategories\MainCategoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMainCategory extends ViewRecord
{
    protected static string $resource = MainCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
