<?php

namespace App\Filament\Admin\Resources\Donations;

use App\Filament\Admin\Resources\Donations\Pages\CreateDonation;
use App\Filament\Admin\Resources\Donations\Pages\EditDonation;
use App\Filament\Admin\Resources\Donations\Pages\ListDonations;
use App\Filament\Admin\Resources\Donations\Pages\ViewDonation;
use App\Filament\Admin\Resources\Donations\Schemas\DonationForm;
use App\Filament\Admin\Resources\Donations\Schemas\DonationInfolist;
use App\Filament\Admin\Resources\Donations\Tables\DonationsTable;
use App\Models\Admin\Donation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?string $recordTitleAttribute = 'Donation';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return DonationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DonationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonationsTable::configure($table);
    }
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('main_category_id', session('main_category_id'));
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
            'index' => ListDonations::route('/'),
            // 'create' => CreateDonation::route('/create'),
            // 'view' => ViewDonation::route('/{record}'),
            // 'edit' => EditDonation::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return session()->has('main_category_id');
    }
    public static function getNavigationLabel(): string { return __('messages.donations'); }
    public static function getModelLabel(): string { return __('messages.donation'); }
    public static function getPluralModelLabel(): string { return __('messages.donations'); }
}
