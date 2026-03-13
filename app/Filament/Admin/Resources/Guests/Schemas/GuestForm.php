<?php

namespace App\Filament\Admin\Resources\Guests\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GuestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->components([
            Section::make(__('messages.guest_information'))
                ->schema([
                    Hidden::make('main_category_id')->default(fn () => session('main_category_id')),
    
                    TextInput::make('name')
                        ->label(__('messages.guest_name'))
                        ->required()
                        ->maxLength(255),
    
                    TextInput::make('phone')
                        ->label(__('messages.phone'))
                        ->tel()
                        ->nullable(),
    
                    Select::make('tag')
                        ->label(__('messages.tag'))
                        ->options([
                            'bride_site' => __('messages.bride_site'),
                            'groom_site' => __('messages.groom_site'),
                            'both_site' => __('messages.both_site'),
                            'other' => __('messages.other'),
                        ])
                        ->required(),
    
                    Textarea::make('note')
                        ->label(__('messages.note'))
                        ->rows(1),
    
                ])
                ->columns(2),
        ])->columns(1);
    }
}
