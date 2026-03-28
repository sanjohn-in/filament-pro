<?php

namespace App\Filament\Admin\Resources\Donations\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;

class DonationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('messages.guest_information'))
                    ->schema([
                        TextEntry::make('guest.name')
                            ->label(__('messages.name'))
                            ->placeholder('-'),

                        // ← phone is on guest not donation
                        TextEntry::make('guest.phone')
                            ->label(__('messages.phone'))
                            ->placeholder('-'),

                        TextEntry::make('guest.tag')
                            ->label(__('messages.tag'))
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'online' => 'success',
                                'agent'  => 'warning',
                                'staff'  => 'info',
                                default  => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => __("messages.{$state}"))
                            ->placeholder('-'),
                    ])
                    ->columns(3),



                Section::make(__('messages.timestamps'))
                ->schema([
                    TextEntry::make('created_at')
                        ->label(__('messages.created_at'))
                        ->dateTime('d/m/Y H:i'),

                    TextEntry::make('updated_at')
                        ->label(__('messages.updated_at'))
                        ->dateTime('d/m/Y H:i'),
                ])
                ->columns(2),

                Section::make(__('messages.donation_information'))
                    ->schema([
                        TextEntry::make('amount_usd')
                            ->label(__('messages.amount_usd'))
                            ->money('USD')
                            ->placeholder('-'),

                        TextEntry::make('amount_khr')
                            ->label(__('messages.amount_khr'))
                            ->numeric()
                            ->suffix(' ៛')
                            ->placeholder('-'),

                        TextEntry::make('payment_method')
                            ->label(__('messages.payment_method'))
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'cash'          => 'success',
                                'bank_transfer' => 'info',
                                'qr_code'       => 'warning',
                                default         => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => __("messages.payment_{$state}"))
                            ->placeholder('-'),

                        TextEntry::make('cash_method')
                            ->label(__('messages.cash_method'))
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'usd'  => 'success',
                                'khr'  => 'warning',
                                'both' => 'info',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => match ($state) {
                                'usd'  => 'USD ($)',
                                'khr'  => 'KHR (៛)',
                                'both' => __('messages.both'),
                                default => $state,
                            })
                            ->placeholder('-'),

                        TextEntry::make('note')
                            ->label(__('messages.note'))
                            ->columnSpanFull()
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                
            ]);
    }
}