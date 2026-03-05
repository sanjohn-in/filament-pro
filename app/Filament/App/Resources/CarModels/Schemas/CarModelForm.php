<?php

namespace App\Filament\App\Resources\CarModels\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

class CarModelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->unique(ignorable: fn ($record) => $record)
                    ->required()
                    ->maxLength(255),
                    
               

                Toggle::make('is_active')
                    ->label(__('messages.is_active'))
                    ->default(true),
                ]);
    }
}
