<?php

namespace App\Filament\App\Resources\Cars\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('carModel.name')
                    ->label(__('messages.car_model'))
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('carModel.brand.name')
                    ->label(__('messages.brand'))
                    ->badge()
                    ->color('success')
                    ->sortable(),
                    
                TextColumn::make('owner.name')
                    ->label(__('messages.owner'))
                    ->badge()
                    ->color('warning')
                    ->sortable(),


                TextColumn::make('year')
                    ->label(__('messages.year'))
                    ->sortable(),

                TextColumn::make('price')
                    ->label(__('messages.price'))
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('interest')
                    ->label(__('messages.interest'))
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('contract')
                    ->label(__('messages.contract'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state . ' ' . __('messages.months')),


            TextColumn::make('status')
                ->label(__('messages.status'))
                ->badge()
                ->getStateUsing(function (Model $record): string {
                    if (blank($record->end_date)) {
                        return 'active';
                    }
            
                    $endDate = Carbon::parse($record->end_date);
                    $today   = Carbon::today();
            
                    if ($endDate->lte($today)) {
                        return 'expired';
                    }
            
                    if ($endDate->diffInDays($today) <= 7) {
                        return 'expiring_soon';
                    }
            
                    return 'active';
                })
                ->color(fn (string $state): string => match ($state) {
                    'expired'       => 'danger',
                    'expiring_soon' => 'warning',
                    'active'        => 'success',
                })
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'expired'       => __('messages.status_expired'),
                    'expiring_soon' => __('messages.status_expiring_soon'),
                    'active'        => __('messages.status_active'),
                }),

                TextColumn::make('start_date')
                    ->label(__('messages.start_date'))
                    ->date('d/m/Y'),
                
                TextColumn::make('end_date')
                    ->label(__('messages.end_date'))
                    ->date('d/m/Y'),


                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label(__('messages.is_active'))
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordClasses(function (Model $record): string {
                if (blank($record->end_date)) {
                    return '';
                }

                $endDate = Carbon::parse($record->end_date);
                $today   = Carbon::today();

                // end_date is today or already passed → danger
                if ($endDate->lte($today)) {
                    return 'bg-red-50 dark:bg-red-950';
                }

                // end_date is within 7 days from today → warning
                if ($endDate->diffInDays($today) <= 7) {
                    return 'bg-yellow-50 dark:bg-yellow-950';
                }

                return '';
            })
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