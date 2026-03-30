<?php

namespace App\Filament\Admin\Resources\TableGroups\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GuestsRelationManager extends RelationManager
{
    protected static string $relationship = 'guests';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id')
                ->label(__('messages.guest'))
                ->options(function ($record) {
                    return \App\Models\Admin\Guest::query()
                        ->where('main_category_id', session('main_category_id'))
                        ->where(function ($q) use ($record) {
                            $q->whereNull('table_group_id')
                              ->orWhere('id', $record?->id); // include current if editing
                        })
                        ->get()
                        ->mapWithKeys(fn ($g) =>
                            [$g->id => $g->name . ($g->phone ? " — {$g->phone}" : '')]
                        );
                })
                ->searchable()
                ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
        ->recordTitleAttribute('name')
        ->columns([
            TextColumn::make('name')
                ->label(__('messages.name'))
                ->searchable()
                ->sortable(),

            TextColumn::make('phone')
                ->label(__('messages.phone')),

            TextColumn::make('tag')
                ->label(__('messages.tag'))
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'online' => 'success',
                    'agent'  => 'warning',
                    'staff'  => 'info',
                    default  => 'gray',
                })
                ->formatStateUsing(fn (string $state): string =>
                    __("messages.{$state}")
                ),

        
        ])
        ->headerActions([
            // CreateAction::make(),
            AssociateAction::make()
                ->label(__('messages.add_guest'))
                ->recordSelect(fn (Select $select) =>
                    $select->options(
                        \App\Models\Admin\Guest::query()
                            ->where('main_category_id', session('main_category_id'))
                            ->whereNull('table_group_id') // ← only unassigned guests
                            ->get()
                            ->mapWithKeys(fn ($g) =>
                                [$g->id => $g->name . ($g->phone ? " — {$g->phone}" : '')]
                            )
                    )->searchable()
                ),
        ])
        ->actions([
            // EditAction::make(),
            DissociateAction::make()
                ->label(__('messages.remove_from_table')),
            // DeleteAction::make(),
        ])
        ->bulkActions([
            BulkActionGroup::make([
                DissociateBulkAction::make(),
                DeleteBulkAction::make(),
            ]),
        ])
        ->filters([
            //
        ]);
    }
    
}
