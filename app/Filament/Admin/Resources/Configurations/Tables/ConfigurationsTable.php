<?php

namespace App\Filament\Admin\Resources\Configurations\Tables;

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

class ConfigurationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->columns([
                TextColumn::make('name')
                    ->label(__('messages.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label(__('messages.slug'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Copied!')
                    ->color('gray'),

                TextColumn::make('type')
                    ->label(__('messages.type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'text'        => 'info',
                        'image'       => 'success',
                        'text-editor' => 'warning',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'text'        => __('messages.type_text'),
                        'image'       => __('messages.type_image'),
                        'text-editor' => __('messages.type_text_editor'),
                        default       => $state,
                    }),

                // Show image preview if type is image
                ImageColumn::make('value')
                    ->label(__('messages.value'))
                    ->disk('public')
                    ->visibility('public')
                    ->visible(fn ($record) => $record?->type === 'image')
                    ->circular(false)
                    ->width(80)
                    ->height(50),

                // Show text value if type is text
                TextColumn::make('value')
                    ->label(__('messages.value'))
                    ->limit(50)
                    ->visible(fn ($record) => $record?->type !== 'image')
                    ->html(),

                IconColumn::make('is_visible')
                    ->label(__('messages.is_visible'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('messages.type'))
                    ->options([
                        'text'        => __('messages.type_text'),
                        'image'       => __('messages.type_image'),
                        'text-editor' => __('messages.type_text_editor'),
                    ]),

                TernaryFilter::make('is_visible')
                    ->label(__('messages.is_visible')),
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
