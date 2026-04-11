<?php

namespace App\Filament\App\Resources\Cars\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('model_id')
                    ->label(__('messages.car_model'))
                    ->relationship('carModel', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        Grid::make(2)->schema([
                            Select::make('brand_id')
                                ->label(__('messages.brand'))
                                ->relationship('brand', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),

                            TextInput::make('name')
                                ->label(__('messages.name'))
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $set('slug', Str::of($state)->lower()->replace(' ', '_'));
                                })
                                ->required()
                                ->maxLength(255),

                            Textarea::make('description')
                                ->label(__('messages.description'))
                                ->rows(1),

                            TextInput::make('slug')
                                ->label(__('messages.slug'))
                                ->required()
                                ->maxLength(255),

                            Toggle::make('is_active')
                                ->label(__('messages.is_active'))
                                ->default(true),
                        ])
                    ])->createOptionAction(
                        fn($action) => $action->modalHeading(__('messages.car_model'))
                    ),

                Select::make('owner_id')
                    ->label(__('messages.owner'))
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->createOptionForm([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label(__('messages.name'))
                                ->required()
                                ->maxLength(255),
                
                            TextInput::make('phone')
                                ->label(__('messages.phone'))
                                ->tel()
                                ->nullable(),
                
                            Textarea::make('address')
                                ->label(__('messages.address'))
                                ->rows(3)
                                ->columnSpanFull(),
                
                            Toggle::make('is_active')
                                ->label(__('messages.is_active'))
                                ->default(true),
                        ]),
                    ])
                    ->createOptionAction(
                        fn ($action) => $action->modalHeading(__('messages.owner'))
                    ),

                TextInput::make('price')
                    ->label(__('messages.price'))
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->live(onBlur: true)                          // ← recalculate when price changes
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $price    = floatval($state);
                        $contract = floatval($get('contract'));

                        if ($price > 0 && $contract > 0) {
                            $set('interest', round($price / 12 * $contract, 2));
                        }
                    }),

                Select::make('contract')
                    ->label(__('messages.contract'))
                    ->options([
                        3  => '3 ' . __('messages.months'),
                        6  => '6 ' . __('messages.months'),
                        12 => '12 ' . __('messages.months'),
                        18 => '18 ' . __('messages.months'),
                        24 => '24 ' . __('messages.months'),
                    ])
                    ->default(3)
                    ->required()
                    ->live()                                       // ← recalculate when contract changes
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $price    = floatval($get('price'));
                        $contract = floatval($state);

                        if ($price > 0 && $contract > 0) {
                            $set('interest', round($price / 12 * $contract, 2));
                        }
                    }),

                TextInput::make('interest')
                    ->label(__('messages.interest'))
                    ->numeric()
                    ->prefix('$')
                    ->default(0)
                    ->required()
                    ->helperText(__('messages.interest_helper')),


                Grid::make(2)->schema([
                    TextInput::make('year')
                        ->label(__('messages.year'))
                        ->numeric()
                        ->nullable(),

                    Select::make('pay_date')
                        ->label(__('messages.pay_date'))
                        ->options(collect(range(1, 31))->mapWithKeys(fn($d) => [$d => $d]))
                        ->required(),

                ]),
                DatePicker::make('start_date')
                    ->label(__('messages.start_date'))
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->locale('km')
                    ->nullable(),


                DatePicker::make('end_date')
                    ->label(__('messages.end_date'))
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->locale('km')
                    ->nullable(),

                Textarea::make('note')
                    ->label(__('messages.note'))
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label(__('messages.is_active'))
                    ->default(true)
                    ->required(),
            ]);
    }
}
