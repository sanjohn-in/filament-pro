<?php

namespace App\Filament\App\Resources\Owners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class OwnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label(__('messages.name'))
                ->searchable()
                ->sortable(),

            TextColumn::make('phone')
                ->label(__('messages.phone')),

            TextColumn::make('cars_count')
                ->label(__('messages.cars'))
                ->counts('cars')
                ->badge()
                ->color('info')
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
