<?php

namespace App\Filament\App\Resources\Owners\RelationManagers;

use App\Filament\App\Resources\Cars\CarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class CarsRelationManager extends RelationManager
{
    protected static string $relationship = 'cars';

    protected static ?string $relatedResource = CarResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
