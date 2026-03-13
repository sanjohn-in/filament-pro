<?php

namespace App\Filament\Admin\Resources\MainCategories;

use App\Filament\Admin\Resources\MainCategories\Pages\CreateMainCategory;
use App\Filament\Admin\Resources\MainCategories\Pages\EditMainCategory;
use App\Filament\Admin\Resources\MainCategories\Pages\ListMainCategories;
use App\Filament\Admin\Resources\MainCategories\Pages\ViewMainCategory;
use App\Filament\Admin\Resources\MainCategories\Schemas\MainCategoryForm;
use App\Filament\Admin\Resources\MainCategories\Schemas\MainCategoryInfolist;
use App\Filament\Admin\Resources\MainCategories\Tables\MainCategoriesTable;
use App\Models\Admin\MainCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MainCategoryResource extends Resource
{
    protected static ?string $model = MainCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'MainCategory';
  
    public static function form(Schema $schema): Schema
    {
        return MainCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MainCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MainCategoriesTable::configure($table);
    }
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('user_id', Auth::id());
        return $query;
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
            'index' => ListMainCategories::route('/'),
            // 'create' => CreateMainCategory::route('/create'),
            // 'view' => ViewMainCategory::route('/{record}'),
            // 'edit' => EditMainCategory::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        return session()->has('main_category_id');
    }

    public static function getNavigationLabel(): string { return __('messages.main_category'); }
    public static function getModelLabel(): string { return __('messages.main_category'); }
    public static function getPluralModelLabel(): string { return __('messages.main_category'); }

    // public static function getGloballySearchableAttributes(): array
    // {
    //     return ['bride_name', 'groom_name'];
    // }
    
}
