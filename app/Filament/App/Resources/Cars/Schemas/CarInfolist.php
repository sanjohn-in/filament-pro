<?php

namespace App\Filament\App\Resources\Cars\Schemas;

use Carbon\Carbon;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CarInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('carModel.name')
                    ->label(__('messages.car_model'))
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
                    ->date('d/m/Y')
                    ->placeholder('-'),
                TextEntry::make('end_date')
                    ->label(__('messages.end_date'))
                    ->date('d/m/Y')
                    ->placeholder('-'),
                TextEntry::make('year')
                    ->label(__('messages.year'))
                    ->numeric()
                    ->placeholder('-'),

                TextEntry::make('pay_date')
                    ->label(__('messages.pay_date'))
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => $state ? "ទី {$state}" : '-')
                    ->placeholder('--'),
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
