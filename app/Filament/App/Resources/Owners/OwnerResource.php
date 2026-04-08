<?php

namespace App\Filament\App\Resources\Owners;

use App\Filament\App\Resources\Owners\Pages\CreateOwner;
use App\Filament\App\Resources\Owners\Pages\EditOwner;
use App\Filament\App\Resources\Owners\Pages\ListOwners;
use App\Filament\App\Resources\Owners\Pages\ViewOwner;
use App\Filament\App\Resources\Owners\RelationManagers\CarsRelationManager;
use App\Filament\App\Resources\Owners\Schemas\OwnerForm;
use App\Filament\App\Resources\Owners\Schemas\OwnerInfolist;
use App\Filament\App\Resources\Owners\Tables\OwnersTable;
use App\Models\Owner;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OwnerResource extends Resource
{
    protected static ?string $model = Owner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Owner';

    public static function form(Schema $schema): Schema
    {
        return OwnerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OwnerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OwnersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CarsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOwners::route('/'),
            // 'create' => CreateOwner::route('/create'),
            'view' => ViewOwner::route('/{record}'),
            // 'edit' => EditOwner::route('/{record}/edit'),
        ];
    }
    public static function getNavigationLabel(): string { return __('messages.owners'); }
    public static function getModelLabel(): string { return __('messages.owner'); }
    public static function getPluralModelLabel(): string { return __('messages.owners');}
}
