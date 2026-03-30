<?php

namespace App\Filament\Admin\Resources\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::fields());
    }

    public static function fields(): array
    {
        return [
            Hidden::make('main_category_id')
                ->default(fn () => session('main_category_id')),

            Hidden::make('user_id')
                ->default(fn () => Auth::id()),

            Section::make(__('messages.expense_information'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('messages.expense_on'))
                        ->required()
                        ->maxLength(255),

                    DatePicker::make('date')
                        ->label(__('messages.date'))
                        ->required()
                        ->default(now())
                        ->displayFormat('d/m/Y')
                        ->native(false),

                    TextInput::make('amount_usd')
                        ->label(__('messages.amount_usd'))
                        ->numeric()
                        ->prefix('$')
                        ->default(0),

                    TextInput::make('amount_khr')
                        ->label(__('messages.amount_khr'))
                        ->numeric()
                        ->suffix('៛')
                        ->default(0),

                    Select::make('status')
                        ->label(__('messages.status'))
                        ->options([
                            'paid'   => __('messages.status_paid'),
                            'unpaid' => __('messages.status_unpaid'),
                        ])
                        ->default('unpaid')
                        ->required()
                        ->native(false),

                    Textarea::make('description')
                        ->label(__('messages.description'))
                        ->rows(2)
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make(__('messages.receipt'))
                ->schema([
                    FileUpload::make('receipt')
                        ->label(__('messages.receipt'))
                        ->image()
                        ->disk('public')
                        ->directory('expenses/receipts')
                        ->imageEditor()
                        ->nullable()
                        ->columnSpanFull()
                        ->afterStateHydrated(function ($component, $state): void {
                            if (is_string($state) && !empty($state)) {
                                $component->state([$state]);
                            }
                        })
                        ->dehydrateStateUsing(function ($state): ?string {
                            if (is_array($state)) {
                                return array_values($state)[0] ?? null;
                            }
                            return $state;
                        }),
                ]),
        ];
    }
}