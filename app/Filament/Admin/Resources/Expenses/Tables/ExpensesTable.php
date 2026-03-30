<?php

namespace App\Filament\Admin\Resources\Expenses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('name')
                ->label(__('messages.expense_on'))
                ->searchable()
                ->sortable(),

            TextColumn::make('date')
                ->label(__('messages.date'))
                ->date('d/m/Y')
                ->sortable(),

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

            // ← Combined total in USD (converts KHR to USD)
            TextColumn::make('combined_total')
                ->label(__('messages.combined_total'))
                ->getStateUsing(function ($record): string {
                    // Exchange rate — adjust as needed
                    $rate = config('app.khr_to_usd_rate', 4100); // 1 USD = 4100 KHR
                    $usd     = floatval($record->amount_usd);
                    $khrToUsd = floatval($record->amount_khr) / $rate;
                    $total   = $usd + $khrToUsd;
                    return '$' . number_format($total, 2);
                })
                ->summarize(
                    Summarizer::make()
                        ->label(__('messages.grand_total_usd'))
                        ->using(function ($query): string {
                            $rate = config('app.khr_to_usd_rate', 4100);
                            $totalUsd = $query->sum('amount_usd');
                            $totalKhr = $query->sum('amount_khr');
                            $total    = $totalUsd + ($totalKhr / $rate);
                            return '$' . number_format($total, 2);
                        })
                ),

            TextColumn::make('status')
                ->label(__('messages.status'))
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'paid'   => 'success',
                    'unpaid' => 'danger',
                    default  => 'gray',
                })
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'paid'   => __('messages.status_paid'),
                    'unpaid' => __('messages.status_unpaid'),
                    default  => $state,
                }),

                TextColumn::make('combined_total_khr')
                ->label(__('messages.combined_total_khr'))
                ->getStateUsing(function ($record): string {
                    $rate    = 4100; // 1 USD = 4100 KHR
                    $khr     = floatval($record->amount_khr);
                    $usdToKhr = floatval($record->amount_usd) * $rate;
                    $total   = $khr + $usdToKhr;
                    return number_format($total, 0) . ' ៛';
                })
                ->summarize(
                    Summarizer::make()
                        ->label(__('messages.grand_total_khr'))
                        ->using(function ($query): string {
                            $rate     = 4100;
                            $totalUsd = $query->sum('amount_usd');
                            $totalKhr = $query->sum('amount_khr');
                            $total    = $totalKhr + ($totalUsd * $rate);
                            return number_format($total, 0) . ' ៛';
                        })
                ),
        ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('messages.status'))
                    ->options([
                        'paid'   => __('messages.status_paid'),
                        'unpaid' => __('messages.status_unpaid'),
                    ]),

                Filter::make('date_range')
                    ->label(__('messages.filter_by_date'))
                    ->schema([
                        DatePicker::make('date_from')
                            ->label(__('messages.date_from'))
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        DatePicker::make('date_until')
                            ->label(__('messages.date_until'))
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])
                    ->columns(2)
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn ($q) => $q->whereDate('date', '>=', $data['date_from'])
                            )
                            ->when(
                                $data['date_until'],
                                fn ($q) => $q->whereDate('date', '<=', $data['date_until'])
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['date_from']) {
                            $indicators[] = __('messages.date_from') . ': ' .
                                Carbon::parse($data['date_from'])->format('d/m/Y');
                        }
                        if ($data['date_until']) {
                            $indicators[] = __('messages.date_until') . ': ' .
                                Carbon::parse($data['date_until'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
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