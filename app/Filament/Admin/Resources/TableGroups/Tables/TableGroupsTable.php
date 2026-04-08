<?php

namespace App\Filament\Admin\Resources\TableGroups\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class TableGroupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label(__('messages.name'))
                ->searchable()
                ->sortable(),

                TextColumn::make('guests_count')
                    ->label(__('messages.guests'))
                    ->counts('guests')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open'   => 'success',
                        'full'   => 'warning',
                        'closed' => 'danger',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open'   => __('messages.table_status_open'),
                        'full'   => __('messages.table_status_full'),
                        'closed' => __('messages.table_status_closed'),
                        default  => $state,
                    }),

                TextColumn::make('note')
                    ->label(__('messages.note'))
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            
            ->filters([
                SelectFilter::make('status')
                    ->label(__('messages.status'))
                    ->options([
                        'open'   => __('messages.table_status_open'),
                        'full'   => __('messages.table_status_full'),
                        'closed' => __('messages.table_status_closed'),
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
