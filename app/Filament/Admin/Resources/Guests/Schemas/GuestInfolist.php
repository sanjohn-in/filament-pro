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

                TextEntry::make('link')
                    ->getStateUsing(function ($record) {
                        $domain = \App\Models\Admin\Configuration::where('slug', 'domain')->value('link');
                        return rtrim($domain, '/') . '/guest/' . $record->id;
                    })
                    ->label(__('messages.link'))
                    ->copyable()
                    ->placeholder('-'),
                TextEntry::make('tag')
                    ->label(__('messages.tag'))
                    ->formatStateUsing(fn ($state) => __("messages.$state"))
                    ->placeholder('-'),

                    TextEntry::make('is_attending')
                    ->label(__('messages.is_attending'))
                    ->formatStateUsing(fn ($state) => __("messages.$state"))
                    ->placeholder('-'),
                TextEntry::make('note')
                ->label(__('messages.note'))
                ->placeholder('-'),
            ])->columns(2);
    }
}
