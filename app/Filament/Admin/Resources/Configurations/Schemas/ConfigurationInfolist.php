<?php

namespace App\Filament\Admin\Resources\Configurations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ConfigurationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->components([
            TextEntry::make('name')
            ->label(__('messages.name'))
            ->placeholder('-'),
            TextEntry::make('link')
                ->label(__('messages.link'))
                ->placeholder('-'),
            TextEntry::make('slug')
                ->label(__('messages.slug'))
                ->placeholder('-'),
            TextEntry::make('value')
                ->label(__('messages.value'))
                ->placeholder('-'),
           
        ])->columns(2);;
    }
}
