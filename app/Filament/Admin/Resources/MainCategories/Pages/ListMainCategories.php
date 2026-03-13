<?php

namespace App\Filament\Admin\Resources\MainCategories\Pages;

use App\Filament\Admin\Resources\MainCategories\MainCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMainCategories extends ListRecords
{
    protected static string $resource = MainCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
