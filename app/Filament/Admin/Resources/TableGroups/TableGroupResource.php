<?php

namespace App\Filament\Admin\Resources\TableGroups;

use App\Filament\Admin\Resources\TableGroups\Pages\CreateTableGroup;
use App\Filament\Admin\Resources\TableGroups\Pages\EditTableGroup;
use App\Filament\Admin\Resources\TableGroups\Pages\ListTableGroups;
use App\Filament\Admin\Resources\TableGroups\RelationManagers\GuestsRelationManager;
use App\Filament\Admin\Resources\TableGroups\Schemas\TableGroupForm;
use App\Filament\Admin\Resources\TableGroups\Tables\TableGroupsTable;
use App\Models\Admin\TableGroup as AdminTableGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TableGroupResource extends Resource
{
    protected static ?string $model = AdminTableGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'TableGroup';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return TableGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TableGroupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            GuestsRelationManager::class,
        ];
    }

    // Filter by session main_category_id
    public static function getEloquentQuery(): Builder
    {
        $categoryId = session('main_category_id');
        return parent::getEloquentQuery()
            ->when($categoryId, fn ($query) => $query->where('main_category_id', $categoryId));
    }
    

    public static function getPages(): array
    {
        return [
            'index' => ListTableGroups::route('/'),
            'create' => CreateTableGroup::route('/create'),
            'edit' => EditTableGroup::route('/{record}/edit'),
        ];
    }
    public static function getNavigationLabel(): string { return __('messages.table_groups'); }
    public static function getModelLabel(): string { return __('messages.table_group'); }
    public static function getPluralModelLabel(): string { return __('messages.table_groups'); }
}
