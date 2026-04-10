<?php

namespace App\Filament\Admin\Resources\Guests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class GuestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                ->label(__('messages.name'))
                ->placeholder('-'),
                TextEntry::make('phone')
                    ->label(__('messages.phone'))
                    ->placeholder('-'),

                TextEntry::make('link')
                ->getStateUsing(function ($record) {
                    $domain = \App\Models\Admin\Configuration::where('slug', 'domain')->value('link');
                    // return $record->mainCategory;
                        return rtrim($domain, '/') . '/events/' . $record->mainCategory->slug  . '/template/' 
                        //  . $record->mainCategory->defaultTheme 
                        .  '1/'
                        . '?gid=' . $record->id . '&lang=' . $record->lang;
                    })
                    ->limit(40) // 👀 show short
                    ->tooltip(fn ($state) => $state) // full on hover
                    ->label(__('messages.link'))

                    ->copyable()
                    ->copyableState(fn ($record) => 
                        rtrim(\App\Models\Admin\Configuration::where('slug', 'domain')->value('link'), '/') 
                        . '/events/' . $record->mainCategory->slug  
                        . '/template/' 
                        .  '1/'
                        .
                        '?gid=' . $record->id . '&lang=' . $record->lang
                    )
                    ->placeholder('-'),
                
                    Grid::make(2)->schema([
                        TextEntry::make('tag')
                        ->label(__('messages.tag'))
                        ->formatStateUsing(fn ($state) => __("messages.$state"))
                        ->placeholder('-'),

                        TextEntry::make('lang')
                        ->label(__('messages.language'))
                        ->formatStateUsing(fn ($state) => $state == 'kh' ? __('messages.khmer') : __('messages.english'))
                        ->placeholder('-'),
                    ]),

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
