<?php

namespace App\Filament\Admin\Resources\Themes;

use App\Filament\Admin\Resources\Themes\Pages\CreateTheme;
use App\Filament\Admin\Resources\Themes\Pages\EditTheme;
use App\Filament\Admin\Resources\Themes\Pages\ListThemes;
use App\Filament\Admin\Resources\Themes\Schemas\ThemeForm;
use App\Filament\Admin\Resources\Themes\Tables\ThemesTable;
use App\Models\Admin\Theme;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ThemeResource extends Resource
{
    protected static ?string $model = Theme::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Theme';

    public static function form(Schema $schema): Schema
    {
        return ThemeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ThemesTable::configure($table);
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
            'index' => ListThemes::route('/'),
            'create' => CreateTheme::route('/create'),
            'edit' => EditTheme::route('/{record}/edit'),
        ];
    }
    // public static function canCreate(): bool
    // {
    //     return session()->has('main_category_id') && Auth::user()->email == 'admin@gmail.com';
    // }
    public static function canViewAny(): bool
    {
        return session()->has('main_category_id');
    }

    public static function getNavigationLabel(): string { return __('messages.themes'); }
    public static function getModelLabel(): string { return __('messages.theme'); }
    public static function getPluralModelLabel(): string { return __('messages.themes'); }
    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }
}
