<?php

namespace App\Filament\Admin\Resources\MainCategories\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
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

                TextEntry::make('slug')
                ->label(__('messages.slug')),

                Grid::make(2)
                    ->schema([
                        TextEntry::make('bride_name')
                            ->label(__('messages.bride_name_kh'))
                            ->placeholder('-')
                            ->color('primary'),

                        TextEntry::make('groom_name')
                            ->label(__('messages.groom_name_kh'))
                            ->placeholder('-')
                            ->color('primary'),
                    ])
                    ,
                Grid::make(2)
                    ->schema([
                        TextEntry::make('bride_name_en')
                        ->label(__('messages.bride_name_en'))
                        ->placeholder('-')
                        ->color('primary'),

                        TextEntry::make('groom_name_en')
                        ->label(__('messages.groom_name_en'))
                        ->placeholder('-')
                        ->color('primary'),
                    ]),
               
                TextEntry::make('date')
                    ->label(__('messages.date'))
                    ->placeholder('-')
                    ->date('d/m/Y') ,
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
                ->label(__('messages.cover_image'))
                // ->columnSpanFull()
                ->placeholder('No Cover Image'),

                ImageEntry::make('qr_code')
                ->disk('public')
                ->imageWidth(300)
                ->label(__('messages.qr_code'))
                // ->columnSpanFull()
                ->placeholder('No QR Code'),

                ImageEntry::make('portfolios')
                ->disk('public')
                ->imageWidth(200)
                ->label(__('messages.portfolios'))
                // ->columnSpanFull()
                ->placeholder('No Cover Image')
                ->columnSpanFull(),
                
                ColorEntry::make('theme_color')
                    ->label(__('messages.theme_color')),
                    
                ColorEntry::make('bg_color')
                    ->label(__('messages.background_color')),

                IconEntry::make('is_visible')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->label(__('messages.created_at'))
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->label(__('messages.updated_at'))
                    ->placeholder('-'),
            ]);
    }
}
