<?php

namespace App\Filament\App\Resources\CarModels;

use App\Filament\App\Resources\CarModels\Pages\CreateCarModel;
use App\Filament\App\Resources\CarModels\Pages\EditCarModel;
use App\Filament\App\Resources\CarModels\Pages\ListCarModels;
use App\Filament\App\Resources\CarModels\Pages\ViewCarModel;
use App\Filament\App\Resources\CarModels\Schemas\CarModelForm;
use App\Filament\App\Resources\CarModels\Schemas\CarModelInfolist;
use App\Filament\App\Resources\CarModels\Tables\CarModelsTable;
use App\Models\CarModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CarModelResource extends Resource
{
    protected static ?string $model = CarModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'CarModel';

    public static function form(Schema $schema): Schema
    {
        return CarModelForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CarModelInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CarModelsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCarModels::route('/'),
            // 'create' => CreateCarModel::route('/create'),
            // 'view' => ViewCarModel::route('/{record}'),
            // 'edit' => EditCarModel::route('/{record}/edit'),
        ];
    }
    public static function getNavigationLabel(): string { return __('messages.car_models'); }
    public static function getModelLabel(): string { return __('messages.car_model'); }
    public static function getPluralModelLabel(): string { return __('messages.car_models'); }
}
