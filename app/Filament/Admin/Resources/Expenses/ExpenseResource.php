<?php

namespace App\Filament\Admin\Resources\Expenses;

use App\Filament\Admin\Resources\Expenses\Pages\CreateExpense;
use App\Filament\Admin\Resources\Expenses\Pages\EditExpense;
use App\Filament\Admin\Resources\Expenses\Pages\ListExpenses;
use App\Filament\Admin\Resources\Expenses\Schemas\ExpenseForm;
use App\Filament\Admin\Resources\Expenses\Tables\ExpensesTable;
use App\Filament\Admin\Resources\Expenses\Tables\ExpenseTable;
use App\Models\Admin\Expense;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Expense';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return ExpenseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExpensesTable::configure($table);
    }
    
    // Filter by session main_category_id
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('main_category_id', session('main_category_id'));
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
            'index' => ListExpenses::route('/'),
            // 'create' => CreateExpense::route('/create'),
            // 'edit' => EditExpense::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        return session()->has('main_category_id');
    }
    public static function getNavigationLabel(): string { return __('messages.expenses'); }
    public static function getModelLabel(): string { return __('messages.expense'); }
    public static function getPluralModelLabel(): string { return __('messages.expenses'); }
}
