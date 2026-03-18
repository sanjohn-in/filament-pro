<?php

namespace App\Filament\App\Resources\Owners\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OwnerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                ->label(__('messages.name')),
                
                TextEntry::make('phone')
                    ->label(__('messages.phone'))
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->label(__('messages.address'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_active')
                    ->label(__('messages.is_active'))
                    ->boolean(),
                TextEntry::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->label(__('messages.updated_at'))
                    ->placeholder('-'),
            ]);
    }
}
