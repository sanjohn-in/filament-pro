<?php

namespace App\Filament\App\Resources\Owners\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OwnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
            ]);
    }
}
