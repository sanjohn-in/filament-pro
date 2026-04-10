<?php

namespace App\Filament\Admin\Resources\MainCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

class MainCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                ->label(__('messages.type'))
                ->badge(),
                TextColumn::make('bride_name')
                    ->label(__('messages.bride_name_kh'))
                    ->searchable(),
                TextColumn::make('groom_name')
                    ->label(__('messages.groom_name_kh'))
                    ->searchable(),

                TextColumn::make('bride_name_en')
                ->label(__('messages.bride_name_en'))
                ->searchable(),

                TextColumn::make('groom_name_en')
                    ->label(__('messages.groom_name_en'))
                    ->searchable(),
                TextColumn::make('slug')
                    ->label(__('messages.slug'))
                    ->searchable(),
                TextColumn::make('date')
                    ->label(__('messages.date'))
                    ->date('d/m/Y') 
                    ->searchable(),
                TextColumn::make('address')
                    ->label(__('messages.address'))
                    ->searchable(),
                    
                ColorColumn::make('theme_color')
                    ->label(__('messages.theme_color')),

                ColorColumn::make('bg_color')
                    ->label(__('messages.background_color')),

                IconColumn::make('is_visible')
                    ->label(__('messages.is_active'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                 ->label(__('messages.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            
            ->filters([
                //
            ])
        
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                ->after(function () {
                    session()->forget('main_category_id');

                    return redirect('/admin/select-category');
                }),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
