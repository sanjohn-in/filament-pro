<?php

namespace App\Filament\Admin\Resources\Themes\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ThemesTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->columns([
            ImageColumn::make('image_url')
                ->label('Image')
                ->square()
                ->size(50),

            TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),

            TextColumn::make('price')
                ->label('Price')
                ->money('USD')
                ->sortable(),

            IconColumn::make('is_free')
                ->label('Free')
                ->boolean()
                ->sortable(),

            IconColumn::make('is_active')
                ->label('Active')
                ->boolean()
                ->sortable(),

            TextColumn::make('mainCategories.type')
                ->label('Categories')
                ->badge()
                ->separator(','),
        ])
            ->defaultSort('display_order')
            ->filters([
                TernaryFilter::make('is_free')
                    ->label('Is Free'),

                TernaryFilter::make('is_active')
                    ->label('Is Active'),

                SelectFilter::make('mainCategories')
                    ->label('Categories')
                    ->relationship('mainCategories', 'type')
                    ->multiple()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('toggle_active')
                        ->label('Toggle Active')
                        ->icon('heroicon-m-check-circle')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_active' => !$record->is_active]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

}
