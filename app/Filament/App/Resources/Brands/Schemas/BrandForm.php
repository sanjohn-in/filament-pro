<?php

namespace App\Filament\App\Resources\Brands\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('messages.name'))
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('slug', Str::of($state)->lower()->replace(' ', '_'));
                    })
                    ->required(),
                TextInput::make('slug')
                    ->label(__('messages.slug'))
                    ->required(),
                Textarea::make('description')
                ->label(__('messages.description'))
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label(__('messages.is_active'))
                    ->default(true)
                    ->required(),
            ]);
    }
}
