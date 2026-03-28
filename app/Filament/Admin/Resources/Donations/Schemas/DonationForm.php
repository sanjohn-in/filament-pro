<?php

namespace App\Filament\Admin\Resources\Donations\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DonationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::fields());
    }
    // ← Reusable fields array
    public static function fields(): array
    {
        return [
            Hidden::make('main_category_id')
                ->default(fn () => session('main_category_id')),

            Select::make('guest_id')
                ->label(__('messages.guest'))
                ->relationship(
                    name: 'guest',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn ($query, $record) => $query
                        ->whereDoesntHave('donation')
                        ->when(
                            $record?->guest_id,
                            fn ($q) => $q->orWhere('id', $record->guest_id)
                        )
                        ->where('main_category_id', session('main_category_id'))
                )
                ->searchable()
                ->preload()
                ->nullable()
                ->getOptionLabelFromRecordUsing(fn ($record) =>
                    "{$record->name}" . ($record->phone ? " — {$record->phone}" : '')
                )
                ->columnSpanFull(),

            Select::make('payment_method')
                ->label(__('messages.payment_method'))
                ->options([
                    'cash'    => __('messages.payment_cash'),
                    'qr_code' => __('messages.payment_qr_code'),
                    'other'   => __('messages.payment_other'),
                ])
                ->default('cash')
                ->required()
                ->native(false),

            Select::make('cash_method')
                ->label(__('messages.cash_method'))
                ->options([
                    'usd'  => 'USD ($)',
                    'khr'  => 'KHR (៛)',
                    'both' => __('messages.both'),
                ])
                ->default('usd')
                ->required()
                ->native(false),

            TextInput::make('amount_usd')
                ->label(__('messages.amount_usd'))
                ->numeric()
                ->prefix('$')
                ->default(0),

            TextInput::make('amount_khr')
                ->label(__('messages.amount_khr'))
                ->numeric()
                ->suffix('៛')
                ->default(0),

            Textarea::make('note')
                ->label(__('messages.note'))
                ->rows(1)
                ->columnSpanFull(),
        ];
    }

    /**
     * Used from the "tie hand" action inside Guests tables.
     * In that context, the form is bound to a Guest record, so we should not
     * call `->relationship('guest')` (it would look for `guest()` on Guest).
     */
    public static function tieHandFields(): array
    {
        return [
            Hidden::make('main_category_id')
                ->default(fn () => session('main_category_id')),

            // When this form is used in GuestsTable, Filament binds it to the current Guest record.
            Hidden::make('guest_id')
                ->default(fn ($record) => $record->id),

            Select::make('payment_method')
                ->label(__('messages.payment_method'))
                ->options([
                    'cash'    => __('messages.payment_cash'),
                    'qr_code' => __('messages.payment_qr_code'),
                    'other'   => __('messages.payment_other'),
                ])
                ->default('cash')
                ->required()
                ->native(false),

            Select::make('cash_method')
                ->label(__('messages.cash_method'))
                ->options([
                    'usd'  => 'USD ($)',
                    'khr'  => 'KHR (៛)',
                    'both' => __('messages.both'),
                ])
                ->default('usd')
                ->required()
                ->native(false),

            TextInput::make('amount_usd')
                ->label(__('messages.amount_usd'))
                ->numeric()
                ->prefix('$')
                ->default(0),

            TextInput::make('amount_khr')
                ->label(__('messages.amount_khr'))
                ->numeric()
                ->suffix('៛')
                ->default(0),

            Textarea::make('note')
                ->label(__('messages.note'))
                ->rows(1)
                ->columnSpanFull(),
        ];
    }
    
}
