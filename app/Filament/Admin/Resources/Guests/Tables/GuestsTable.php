<?php

namespace App\Filament\Admin\Resources\Guests\Tables;

use App\Filament\Admin\Resources\Donations\Schemas\DonationForm;
use App\Models\Admin\Donation;
use Filament\Actions\Action;
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
                    ->searchable()
                    ->placeholder('--'),

                TextColumn::make('tag')
                    ->label(__('messages.tag'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bride_site' => 'primary',
                        'groom_site'  => 'danger',
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
                TextColumn::make('is_attending')
                    ->label(__('messages.is_attending'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'yes' => 'primary',
                        'no'  => 'danger',
                        default  => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'yes' => __('messages.yes'),
                        'no'  => __('messages.no'),
                        default  => $state,
                    }),
                  
                // TextColumn::make('note')
                //     ->label(__('messages.note'))
                //     ->limit(40),

               

                TextColumn::make('guest_link')
                    ->label(__('messages.link'))
                    ->getStateUsing(function ($record) {
                        $domain = \App\Models\Admin\Configuration::where('slug', 'domain')->value('link');
                        return rtrim($domain, '/') . '/guest/' . $record->id;
                    })
                    
                    ->copyable()                          // ✅ built-in copy button
                    
                    ->copyMessageDuration(1500)
                    ->icon('heroicon-o-link'),

                TextColumn::make('created_at')
                ->label(__('messages.created_at'))
                ->date('d/m/Y') 
                
                ->sortable(),

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
                Action::make('tie_hand')
                ->label(__('messages.tie_hand'))
                ->button()
                ->color('info')
                ->icon('heroicon-o-hand-raised')
                ->schema(DonationForm::tieHandFields())
                ->action(function (array $data): void {
                    Donation::create($data);
                })
                ->visible(fn ($record) => ! $record->donation()->exists())
                ->successNotificationTitle(__('messages.donation_created'))
                ->modalHeading(__('messages.tie_hand'))
                ->modalSubmitActionLabel(__('messages.save'))
                ->modalWidth('xl'),

                Action::make('tied_hand')
                    ->label(__('messages.tied_hand'))
                    ->button()
                    ->color('secondary')
                    ->icon('heroicon-o-check-circle')
                    ->disabled()
                    ->visible(fn ($record) => $record->donation()->exists()),

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
