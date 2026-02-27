<?php

namespace App\Filament\App\Resources\Brands\RelationManagers;

use App\Filament\App\Resources\CarModels\CarModelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class CarModelsRelationManager extends RelationManager
{
    protected static string $relationship = 'carModels';

    protected static ?string $relatedResource = CarModelResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
