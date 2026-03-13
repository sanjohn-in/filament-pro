<?php

namespace App\Filament\Admin\Resources\Guests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GuestsTable
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
                    ->label(__('messages.phone'))
                    ->searchable(),

                TextColumn::make('tag')
                    ->label(__('messages.tag'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bride_site' => 'primary',
                        'groom_site'  => 'success',
                        'both_site'  => 'info',
                        'other' => 'warning',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bride_site' => __('messages.bride_site'),
                        'groom_site'  => __('messages.groom_site'),
                        'both_site'  => __('messages.both_site'),
                        'other' => __('messages.other'),
                        default  => $state,
                    }),

                TextColumn::make('note')
                    ->label(__('messages.note'))
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ])
            ->filters([
                SelectFilter::make('tag')
                    ->label(__('messages.tag'))
                    ->options([
                        'bride_site' => __('messages.bride_site'),
                        'groom_site'  => __('messages.groom_site'),
                        'both_site'  => __('messages.both_site'),
                        'other' => __('messages.other')
                    ]),
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
