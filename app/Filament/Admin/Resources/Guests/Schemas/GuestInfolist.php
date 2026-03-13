<?php

namespace App\Filament\Admin\Resources\Guests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GuestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                ->label(__('messages.bride_name'))
                ->placeholder('-'),
                TextEntry::make('phone')
                    ->label(__('messages.phone'))
                    ->placeholder('-'),
                TextEntry::make('tag')
                    ->label(__('messages.tag'))
                    ->formatStateUsing(fn ($state) => __("messages.$state"))
                    ->placeholder('-'),
                TextEntry::make('note')
                ->label(__('messages.note'))
                ->placeholder('-'),
            ])->columns(2);;
    }
}
