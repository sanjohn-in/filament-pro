<?php

namespace App\Filament\App\Resources\Brands\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BrandInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                ->label(__('messages.name')),
                TextEntry::make('slug')
                ->label(__('messages.slug')),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull()
                    ->label(__('messages.description')),
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
