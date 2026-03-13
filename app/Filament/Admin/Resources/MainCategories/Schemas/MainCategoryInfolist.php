<?php

namespace App\Filament\Admin\Resources\MainCategories\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MainCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('type')
                    ->label(__('messages.type'))
                    ->badge(),
                TextEntry::make('bride_name')
                    ->label(__('messages.bride_name'))
                    ->placeholder('-'),
                TextEntry::make('groom_name')
                    ->label(__('messages.groom_name'))
                    ->placeholder('-'),
                TextEntry::make('slug')
                    ->label(__('messages.slug')),
                TextEntry::make('date')
                    ->label(__('messages.date'))
                    ->placeholder('-'),
                TextEntry::make('adress')
                    ->label(__('messages.address'))
                    ->placeholder('-'),
                TextEntry::make('google_map')
                    ->label(__('messages.google_map'))
                    ->placeholder('-')
                    ->columnSpanFull(),

                ImageEntry::make('cover_image')
                ->disk('public')
                ->imageWidth(300)
                // ->columnSpanFull()
                ->placeholder('No Cover Image'),

                ImageEntry::make('qr_code')
                ->disk('public')
                ->imageWidth(300)
                // ->columnSpanFull()
                ->placeholder('No QR Code'),
                
                IconEntry::make('is_visible')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
