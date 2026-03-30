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
                    
                    Select::make('table_group_id')
                        ->label(__('messages.table_group'))
                        ->options(function () {
                            $tables = \App\Models\Admin\TableGroup::query()
                                ->where('main_category_id', session('main_category_id'))
                                ->where('status', 'open')
                                ->get()
                                ->mapWithKeys(fn ($t) => [$t->id => $t->name]);

                            return $tables;
                        })
                        ->searchable()
                        ->nullable()
                        ->native(false)
                        // ← Only show if tables exist
                        ->visible(fn () =>
                            \App\Models\Admin\TableGroup::where('main_category_id', session('main_category_id'))
                                ->exists()
                        )
                        ->placeholder(__('messages.select_table')),
    
                    Select::make('tag')
                        ->label(__('messages.tag'))
                        ->options([
                            'bride_site' => __('messages.bride_site'),
                            'groom_site' => __('messages.groom_site'),
                            'both_site' => __('messages.both_site'),
                            'other' => __('messages.other'),
                        ])
                        ->required(),

                    TextInput::make('phone')
                        ->label(__('messages.phone'))
                        ->tel()
                        ->nullable(),
                    Textarea::make('note')
                        ->label(__('messages.note'))
                        ->rows(2)->columnSpanFull(),
    
                ])
                ->columns(2),
        ])->columns(1);
    }
}
