<?php

namespace App\Filament\App\Resources\CarModels\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CarModelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('brand.name')
                ->label(__('messages.brand'))
                ->badge()
                ->color('warning')
                ->sortable(),

            TextColumn::make('name')
                ->label(__('messages.name'))
                ->searchable()
                ->sortable(),

            TextColumn::make('cars_count')
                ->label(__('messages.cars'))
                ->counts('cars')
                ->badge()
                ->sortable(),

            IconColumn::make('is_active')
                ->label(__('messages.is_active'))
                ->boolean(),

            TextColumn::make('created_at')
                ->label(__('messages.created_at'))
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
            ->filters([
                SelectFilter::make('brand')
                ->label(__('messages.brand'))
                ->relationship('brand', 'name')
                ->searchable()
                ->preload(),

                TernaryFilter::make('is_active')
                    ->label(__('messages.is_active')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
    
}
