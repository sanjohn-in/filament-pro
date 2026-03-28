<?php

namespace App\Filament\Admin\Resources\Configurations;

use App\Filament\Admin\Resources\Configurations\Pages\CreateConfiguration;
use App\Filament\Admin\Resources\Configurations\Pages\EditConfiguration;
use App\Filament\Admin\Resources\Configurations\Pages\ListConfigurations;
use App\Filament\Admin\Resources\Configurations\Pages\ViewConfiguration;
use App\Filament\Admin\Resources\Configurations\Schemas\ConfigurationForm;
use App\Filament\Admin\Resources\Configurations\Schemas\ConfigurationInfolist;
use App\Filament\Admin\Resources\Configurations\Tables\ConfigurationsTable;
use App\Models\Admin\Configuration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ConfigurationResource extends Resource
{
    protected static ?string $model = Configuration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;
    protected static ?string $recordTitleAttribute = 'Configuration';
    protected static ?int $navigationSort = 10;


    public static function form(Schema $schema): Schema
    {
        return ConfigurationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ConfigurationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConfigurationsTable::configure($table);
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
            'index' => ListConfigurations::route('/'),
            // 'create' => CreateConfiguration::route('/create'),
            // 'view' => ViewConfiguration::route('/{record}'),
            // 'edit' => EditConfiguration::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->email === 'admin@gmail.com';
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->email === 'admin@gmail.com';
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->email === 'admin@gmail.com';
    }
    public static function canViewAny(): bool
    {
        return Auth::user()?->email === 'admin@gmail.com';
    }
    
    public static function getNavigationLabel(): string { return __('messages.configurations'); }
    public static function getModelLabel(): string { return __('messages.configuration'); }
    public static function getPluralModelLabel(): string { return __('messages.configurations'); }
}
