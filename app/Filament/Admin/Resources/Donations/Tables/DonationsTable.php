<?php

namespace App\Filament\Admin\Resources\Donations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;

class DonationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guest.name')
                    ->label(__('messages.guest'))
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('guest.phone')
                    ->label(__('messages.phone'))
                    ->searchable(),

                TextColumn::make('payment_method')
                    ->label(__('messages.payment_method'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash'          => 'success',
                        'bank_transfer' => 'info',
                        'qr_code'       => 'warning',
                        default         => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash'          => __('messages.payment_cash'),
                        'bank_transfer' => __('messages.payment_bank_transfer'),
                        'qr_code'       => __('messages.payment_qr_code'),
                        'other'         => __('messages.payment_other'),
                        default         => $state,
                    }),

                TextColumn::make('cash_method')
                    ->label(__('messages.cash_method'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'usd'  => 'success',
                        'khr'  => 'warning',
                        'both' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'usd'  => 'USD ($)',
                        'khr'  => 'KHR (៛)',
                        'both' => __('messages.both'),
                        default => $state,
                    }),

                TextColumn::make('amount_usd')
                    ->label(__('messages.amount_usd'))
                    ->money('USD')
                    ->sortable()
                    ->summarize(
                        Sum::make()
                            ->label(__('messages.total_usd'))
                            ->money('USD')
                    ),

                TextColumn::make('amount_khr')
                    ->label(__('messages.amount_khr'))
                    ->numeric()
                    ->suffix(' ៛')
                    ->sortable()
                    ->summarize(
                        Sum::make()
                            ->label(__('messages.total_khr'))
                            ->numeric()
                            ->suffix(' ៛')
                    ),

                // Grand Total in USD (converts KHR to USD)
                TextColumn::make('combined_total')
                    ->label(__('messages.combined_total_usd'))
                    ->getStateUsing(function ($record): string {
                        $rate = config('app.khr_to_usd_rate', 4100);
                        $usd = floatval($record->amount_usd);
                        $khrToUsd = floatval($record->amount_khr) / $rate;
                        $total = $usd + $khrToUsd;
                        return '$' . number_format($total, 2);
                    })
                    ->summarize(
                        Summarizer::make()
                            ->label(__('messages.grand_total_usd'))
                            ->using(function ($query): string {
                                $rate = config('app.khr_to_usd_rate', 4100);
                                $totalUsd = $query->sum('amount_usd');
                                $totalKhr = $query->sum('amount_khr');
                                $total = $totalUsd + ($totalKhr / $rate);
                                return '$' . number_format($total, 2);
                            })
                    ),

                // Grand Total in KHR (converts USD to KHR)
                TextColumn::make('combined_total_khr')
                    ->label(__('messages.combined_total_khr'))
                    ->getStateUsing(function ($record): string {
                        $rate = config('app.khr_to_usd_rate', 4100);
                        $khr = floatval($record->amount_khr);
                        $usdToKhr = floatval($record->amount_usd) * $rate;
                        $total = $khr + $usdToKhr;
                        return number_format($total, 0) . ' ៛';
                    })
                    ->summarize(
                        Summarizer::make()
                            ->label(__('messages.grand_total_khr'))
                            ->using(function ($query): string {
                                $rate = config('app.khr_to_usd_rate', 4100);
                                $totalUsd = $query->sum('amount_usd');
                                $totalKhr = $query->sum('amount_khr');
                                $total = $totalKhr + ($totalUsd * $rate);
                                return number_format($total, 0) . ' ៛';
                            })
                    ),

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
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('guest')
                    ->label(__('messages.guest'))
                    ->relationship('guest', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('payment_method')
                    ->label(__('messages.payment_method'))
                    ->options([
                        'cash'          => __('messages.payment_cash'),
                        'bank_transfer' => __('messages.payment_bank_transfer'),
                        'qr_code'       => __('messages.payment_qr_code'),
                        'other'         => __('messages.payment_other'),
                    ]),

                SelectFilter::make('cash_method')
                    ->label(__('messages.cash_method'))
                    ->options([
                        'usd'  => 'USD ($)',
                        'khr'  => 'KHR (៛)',
                        'both' => __('messages.both'),
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