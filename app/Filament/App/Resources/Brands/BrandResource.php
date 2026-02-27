<?php

namespace App\Filament\App\Resources\Brands;

use App\Filament\App\Resources\Brands\Pages\CreateBrand;
use App\Filament\App\Resources\Brands\Pages\EditBrand;
use App\Filament\App\Resources\Brands\Pages\ListBrands;
use App\Filament\App\Resources\Brands\Pages\ViewBrand;
use App\Filament\App\Resources\Brands\RelationManagers\CarModelsRelationManager;
use App\Filament\App\Resources\Brands\Schemas\BrandForm;
use App\Filament\App\Resources\Brands\Schemas\BrandInfolist;
use App\Filament\App\Resources\Brands\Tables\BrandsTable;
use App\Models\Brand;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Brand';

    public static function form(Schema $schema): Schema
    {
        return BrandForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BrandInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BrandsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CarModelsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBrands::route('/'),
            // 'create' => CreateBrand::route('/create'),
            // 'view' => ViewBrand::route('/{record}'),
            // 'edit' => EditBrand::route('/{record}/edit'),
        ];
    }
    public static function getNavigationLabel(): string { return __('messages.brands'); }
    public static function getModelLabel(): string { return __('messages.brand'); }
    public static function getPluralModelLabel(): string { return __('messages.brands'); }
}
