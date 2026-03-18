<?php

namespace App\Filament\App\Resources\Cars\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CarInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('model_id')
                    ->numeric(),
                TextEntry::make('owner.name')
                    ->label(__('messages.owner'))
                    ->placeholder('-'),
                TextEntry::make('price')
                    ->label(__('messages.price'))
                    ->money(),
                TextEntry::make('contract')
                    ->label(__('messages.contract')),
                TextEntry::make('start_date')
                    ->label(__('messages.start_date'))
                    ->placeholder('-'),
                TextEntry::make('end_date')
                    ->label(__('messages.end_date'))
                    ->placeholder('-'),
                TextEntry::make('year')
                    ->label(__('messages.year'))
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->label(__('messages.is_active'))
                    ->boolean(),
                TextEntry::make('note')
                    ->label(__('messages.note'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(__('messages.updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
