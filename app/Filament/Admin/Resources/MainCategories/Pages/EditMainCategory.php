<?php

namespace App\Filament\Admin\Resources\MainCategories\Pages;

use App\Filament\Admin\Resources\MainCategories\MainCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMainCategory extends EditRecord
{
    protected static string $resource = MainCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
