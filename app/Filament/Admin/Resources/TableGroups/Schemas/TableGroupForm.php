<?php

namespace App\Filament\Admin\Resources\TableGroups\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class TableGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('main_category_id')
                ->default(fn () => session('main_category_id')),
                
                Hidden::make('user_id')
                ->default(fn () => Auth::id()),

                TextInput::make('name')
                            ->label(__('messages.name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Table 1, VIP Table, Table A...'),


                Grid::make(2)->schema([
                    Select::make('tag')
                    ->label(__('messages.tag'))
                    ->options([
                        'bride_table' => __('messages.bride_table'),
                        'groom_table' => __('messages.groom_table'),
                    ]),

                    Select::make('status')
                        ->label(__('messages.status'))
                        ->options([
                            'open'   => __('messages.table_status_open'),
                            'full'   => __('messages.table_status_full'),
                            'closed' => __('messages.table_status_closed'),
                        ])
                        ->default('open')
                        ->required()
                        ->native(false),
                ]),

                Textarea::make('note')
                    ->label(__('messages.note'))
                    ->rows(2)
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }
}
