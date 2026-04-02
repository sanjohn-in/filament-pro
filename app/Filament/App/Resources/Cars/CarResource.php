<?php

namespace App\Filament\App\Resources\Cars;

use App\Filament\App\Resources\Cars\Pages\CreateCar;
use App\Filament\App\Resources\Cars\Pages\EditCar;
use App\Filament\App\Resources\Cars\Pages\ListCars;
use App\Filament\App\Resources\Cars\Pages\ViewCar;
use App\Filament\App\Resources\Cars\Schemas\CarForm;
use App\Filament\App\Resources\Cars\Schemas\CarInfolist;
use App\Filament\App\Resources\Cars\Tables\CarsTable;
use App\Filament\App\Resources\Owners\RelationManagers\CarsRelationManager;
use App\Models\Car;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CarResource extends Resource
{
    protected static ?string $model = Car::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Car';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return CarForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CarInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CarsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCars::route('/'),
            // 'create' => CreateCar::route('/create'),
            // 'view' => ViewCar::route('/{record}'),
            // 'edit' => EditCar::route('/{record}/edit'),
        ];
    }
    public static function getNavigationLabel(): string { return __('messages.cars'); }
    public static function getModelLabel(): string { return __('messages.car'); }
    public static function getPluralModelLabel(): string { return __('messages.cars'); }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
