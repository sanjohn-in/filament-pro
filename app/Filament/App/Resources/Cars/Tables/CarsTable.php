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
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Support\Facades\DB;

class CarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('end_date', 'asc')
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
                    
                        $endDate = Carbon::parse($record->end_date)->startOfDay();
                        $today   = Carbon::today();
                        $daysLeft = $today->diffInDays($endDate, false); // false = signed difference
                    
                        // 🔴 end_date is today or past
                        if ($daysLeft <= 0) {
                            return 'expired';
                        }
                    
                        // 🟡 end_date is 1 to 6 days away (02/03 → 07/03)
                        if ($daysLeft <= 6) {
                            return 'expiring_soon';
                        }
                    
                        // 🟢 end_date is 7+ days away (08/03 or beyond)
                        return 'active';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'expired'       => 'danger',   // 🔴
                        'expiring_soon' => 'warning',  // 🟡
                        'active'        => 'success',  // 🟢
                        default         => 'success',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'expired'       => __('messages.status_expired'),
                        'expiring_soon' => __('messages.status_expiring_soon'),
                        'active'        => __('messages.status_active'),
                        default         => __('messages.status_active'),
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
                    
                SelectFilter::make('owner.name')
                ->label(__('messages.owner'))
                ->relationship('owner', 'name')
                ->searchable()
                ->preload(),

                Filter::make('end_date')
                    ->label(__('messages.end_date'))
                    ->schema([
                        DatePicker::make('end_date_from')
                            ->label(__('messages.start_date'))
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        DatePicker::make('end_date_until')
                            ->label(__('messages.end_date'))
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])
                    ->columns(2)
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['end_date_from'],
                                fn ($query) => $query->whereDate('end_date', '>=', $data['end_date_from'])
                            )
                            ->when(
                                $data['end_date_until'],
                                fn ($query) => $query->whereDate('end_date', '<=', $data['end_date_until'])
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['end_date_from']) {
                            $indicators[] = __('messages.start_date') . ': ' .
                                Carbon::parse($data['end_date_from'])->format('d/m/Y');
                        }

                        if ($data['end_date_until']) {
                            $indicators[] = __('messages.end_date') . ': ' .
                                Carbon::parse($data['end_date_until'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),

                    TernaryFilter::make('is_active')
                        ->label(__('messages.is_active')),
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