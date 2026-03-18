<?php

namespace App\Filament\App\Resources\CarModels\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CarModelInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->components([
            TextEntry::make('brand.name')
            ->label(__('messages.brand')),

            TextEntry::make('name')
            ->label(__('messages.name')),

            TextEntry::make('description')
                ->placeholder('-')
                ->columnSpanFull()
                ->label(__('messages.description')),

            TextEntry::make('slug')
                ->placeholder('-')
                ->columnSpanFull()
                ->label(__('messages.slug')),

            IconEntry::make('is_active')
                ->label(__('messages.is_active'))
                ->boolean(),
            TextEntry::make('created_at')
                ->dateTime()
                ->placeholder('-')
                ->label(__('messages.created_at')),
            TextEntry::make('updated_at')
                ->dateTime()
                ->label(__('messages.updated_at'))
                ->placeholder('-'),
        ]);
    }
}
